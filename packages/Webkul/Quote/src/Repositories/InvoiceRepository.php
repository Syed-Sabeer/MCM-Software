<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Core\Eloquent\Repository;
use Webkul\Core\Support\DocumentChargeManager;
use Webkul\Quote\Models\InvoiceReceipt;
use Webkul\Quote\Models\ProformaInvoice;

class InvoiceRepository extends Repository
{
    public function __construct(
        protected DocumentChargeManager $documentChargeManager,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return 'Webkul\Quote\Contracts\Invoice';
    }

    public function createFromProforma(ProformaInvoice $proforma, array $data = [])
    {
        return DB::transaction(function () use ($proforma, $data) {
            $proforma = ProformaInvoice::query()->with(['items', 'additionalCharges'])->lockForUpdate()->findOrFail($proforma->id);

            if ($proforma->converted_to_invoice_id) {
                throw ValidationException::withMessages(['proforma' => 'This proforma has already been converted to a final invoice.']);
            }

            if (in_array($proforma->status, ['draft', 'cancelled'], true)) {
                throw ValidationException::withMessages(['proforma' => 'Draft or cancelled proformas cannot be invoiced.']);
            }

            $grandTotal = round((float) $proforma->grand_total, 4);
            $advance = min(round((float) $proforma->received_amount, 4), $grandTotal);
            $invoice = parent::create(array_merge([
                'proforma_invoice_id'   => $proforma->id,
                'quote_id'              => $proforma->quote_id,
                'organization_id'       => $proforma->organization_id,
                'person_id'             => $proforma->person_id,
                'sales_owner_id'        => $proforma->sales_owner_id,
                'subject'               => $proforma->subject,
                'issue_date'            => now()->toDateString(),
                'due_date'              => $proforma->due_date?->toDateString(),
                'billing_address'       => $proforma->billing_address,
                'shipping_address'      => $proforma->shipping_address,
                'subtotal'              => $proforma->subtotal,
                'tax_amount'            => $proforma->tax_amount,
                'adjustment_amount'     => $proforma->adjustment_amount,
                'grand_total'           => $grandTotal,
                'advance_applied'       => $advance,
                'received_amount'       => 0,
                'remaining_amount'      => max($grandTotal - $advance, 0),
                'status'                => $advance >= $grandTotal ? 'paid' : ($advance > 0 ? 'partially_paid' : 'issued'),
                'payment_term'          => $proforma->payment_term,
                'customer_po_reference' => $proforma->customer_po_reference,
                'notes'                 => $proforma->notes,
                'terms'                 => $proforma->terms,
                'attachment_path'       => $proforma->attachment_path,
                'created_by'            => auth()->id(),
                'customer_visible_at'   => $proforma->customer_visible_at,
            ], $data));

            $invoice->items()->createMany($proforma->items->values()->map(fn ($item, $index) => [
                'product_id'         => $item->product_id,
                'color_variant_id'   => $item->color_variant_id,
                'color_variant_name' => $item->color_variant_name,
                'preview_image'      => $item->preview_image,
                'item_name'          => $item->item_name,
                'item_code'          => $item->item_code,
                'description'        => $item->description,
                'qty'                => $item->qty,
                'unit'               => $item->unit,
                'unit_price'         => $item->unit_price,
                'line_total'         => $item->line_total,
                'sort_order'         => $index,
            ])->all());

            $charges = $this->documentChargeManager->extract($proforma, 'proforma');
            $this->documentChargeManager->sync($invoice, $this->documentChargeManager->normalize($charges, (float) $invoice->subtotal));

            $proforma->forceFill(['converted_to_invoice_id' => $invoice->id, 'status' => 'converted'])->save();

            return $invoice->fresh(['items', 'receipts', 'proformaInvoice.receipts', 'additionalCharges']);
        });
    }

    public function addReceipt(int $invoiceId, array $data)
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            $invoice = $this->model->newQuery()->lockForUpdate()->findOrFail($invoiceId);
            $amount = round((float) ($data['amount'] ?? 0), 4);

            if ($amount <= 0 || $amount > (float) $invoice->remaining_amount) {
                throw ValidationException::withMessages([
                    'amount' => $amount <= 0 ? 'Payment must be greater than zero.' : 'Payment cannot exceed the invoice balance.',
                ]);
            }

            InvoiceReceipt::create(array_merge($data, ['invoice_id' => $invoice->id, 'received_by' => $data['received_by'] ?? auth()->id()]));

            return $this->recalculate($invoice->id);
        });
    }

    public function deleteReceipt(int $invoiceId, int $receiptId): void
    {
        DB::transaction(function () use ($invoiceId, $receiptId) {
            InvoiceReceipt::where('invoice_id', $invoiceId)->findOrFail($receiptId)->delete();
            $this->recalculate($invoiceId);
        });
    }

    public function recalculate(int $invoiceId)
    {
        $invoice = $this->findOrFail($invoiceId);
        $received = round((float) InvoiceReceipt::where('invoice_id', $invoiceId)->sum('amount'), 4);
        $paid = round((float) $invoice->advance_applied + $received, 4);
        $remaining = max(round((float) $invoice->grand_total - $paid, 4), 0);
        $status = $invoice->status;

        if ($status !== 'cancelled') {
            $status = $remaining <= 0 ? 'paid' : ($paid > 0 ? 'partially_paid' : 'issued');
        }

        parent::update(['received_amount' => $received, 'remaining_amount' => $remaining, 'status' => $status], $invoiceId);

        return $this->find($invoiceId);
    }
}
