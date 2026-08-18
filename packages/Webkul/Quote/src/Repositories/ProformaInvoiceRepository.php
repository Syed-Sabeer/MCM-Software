<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Contact\Models\Organization;
use Webkul\Core\Support\DocumentAddressManager;
use Webkul\Core\Eloquent\Repository;
use Webkul\Core\Support\DocumentChargeManager;
use Webkul\Quote\Models\Quote;

class ProformaInvoiceRepository extends Repository
{
    public function __construct(
        protected ProformaInvoiceItemRepository $proformaInvoiceItemRepository,
        protected ProformaReceiptRepository $proformaReceiptRepository,
        protected DocumentAddressManager $documentAddressManager,
        protected DocumentChargeManager $documentChargeManager,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Quote\Contracts\ProformaInvoice';
    }

    /**
     * Create proforma invoice.
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data = $this->calculateTotals($data);

            $proforma = parent::create($data);

            $this->syncItems($proforma->id, $data['items'] ?? []);
            $this->documentChargeManager->sync($proforma, $data['charges'] ?? []);
            $this->recalculateReceiptSummary($proforma->id);

            return $proforma->fresh(['items', 'receipts']);
        });
    }

    /**
     * Update proforma invoice.
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            $data = $this->calculateTotals($data);

            $proforma = parent::update($data, $id, $attribute);

            $this->syncItems($id, $data['items'] ?? []);
            $this->documentChargeManager->sync($proforma, $data['charges'] ?? []);
            $this->recalculateReceiptSummary($id);

            return $proforma->fresh(['items', 'receipts']);
        });
    }

    /**
     * Create a new proforma invoice from quote.
     */
    public function createFromQuote(Quote $quote, array $overrides = [])
    {
        $items = $quote->items->map(function ($item) {
            return [
                'product_id'        => $item->product_id,
                'color_variant_id'  => $item->color_variant_id,
                'color_variant_name'=> $item->color_variant_name,
                'preview_image'     => $item->preview_image,
                'item_name'         => $item->item_name ?: $item->name,
                'item_code'         => $item->item_code ?: $item->sku,
                'description'       => $item->description,
                'qty'               => $item->qty ?: $item->quantity,
                'unit'              => $item->unit,
                'unit_price'        => $item->unit_price ?: $item->price,
                'discount_percent'  => $item->discount_percent,
                'discount_amount'   => $item->discount_amount,
                'tax_percent'       => $item->tax_percent,
                'tax_amount'        => $item->tax_amount,
            ];
        })->toArray();

        $payload = array_merge([
            'quote_id'             => $quote->id,
            'organization_id'      => $quote->organization_id ?: optional($quote->person)->organization_id,
            'person_id'            => $quote->person_id,
            'sales_owner_id'       => $quote->user_id,
            'subject'              => $quote->subject,
            'issue_date'           => now()->toDateString(),
            'due_date'             => $quote->expired_at?->toDateString(),
            'billing_address'      => $quote->billing_address,
            'shipping_address'     => $quote->shipping_address,
            'discount_percent'     => $quote->discount_percent,
            'adjustment_amount'    => $quote->adjustment_amount,
            'notes'                => $quote->notes,
            'terms'                => $quote->terms,
            'payment_term'         => $quote->payment_term,
            'attachment_path'      => $quote->attachment_path,
            'source_type'          => 'quote',
            'status'               => 'draft',
            'created_by'           => auth()->id(),
            'charges'              => $this->documentChargeManager->extract($quote, 'quote'),
            'items'                => $items,
        ], $overrides);

        return $this->create($payload);
    }

    /**
     * Add receipt and recalculate summary.
     */
    public function addReceipt(int $proformaInvoiceId, array $data)
    {
        return DB::transaction(function () use ($proformaInvoiceId, $data) {
            $invoice = $this->findOrFail($proformaInvoiceId);

            $remaining = (float) $invoice->remaining_amount;
            $amount = (float) ($data['amount'] ?? 0);

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Receipt amount must be greater than zero.',
                ]);
            }

            if ($amount > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Receipt amount cannot exceed remaining balance.',
                ]);
            }

            $this->proformaReceiptRepository->create(array_merge($data, [
                'proforma_invoice_id' => $proformaInvoiceId,
                'received_by'         => $data['received_by'] ?? auth()->id(),
            ]));

            $this->recalculateReceiptSummary($proformaInvoiceId);

            return $this->find($proformaInvoiceId);
        });
    }

    /**
     * Delete receipt and recalculate summary.
     */
    public function deleteReceipt(int $proformaInvoiceId, int $receiptId): void
    {
        DB::transaction(function () use ($proformaInvoiceId, $receiptId) {
            $this->proformaReceiptRepository
                ->where('id', $receiptId)
                ->where('proforma_invoice_id', $proformaInvoiceId)
                ->firstOrFail()
                ->delete();

            $this->recalculateReceiptSummary($proformaInvoiceId);
        });
    }

    /**
     * Recalculate receipt and balance summary.
     */
    public function recalculateReceiptSummary(int $proformaInvoiceId): void
    {
        $invoice = $this->findOrFail($proformaInvoiceId);

        $received = (float) $this->proformaReceiptRepository
            ->where('proforma_invoice_id', $proformaInvoiceId)
            ->sum('amount');

        $grandTotal = (float) $invoice->grand_total;
        $remaining = max($grandTotal - $received, 0);

        $status = $invoice->status;

        $automaticPaymentStatuses = ['draft', 'issued', 'partially_paid', 'fully_paid'];

        if (in_array($status, $automaticPaymentStatuses, true)) {
            if ($received > 0 && $remaining > 0) {
                $status = 'partially_paid';
            } elseif ($received > 0) {
                $status = 'fully_paid';
            } elseif (in_array($status, ['partially_paid', 'fully_paid'], true)) {
                $status = 'draft';
            }
        }

        parent::update([
            'received_amount'  => $received,
            'remaining_amount' => $remaining,
            'status'           => $status,
        ], $proformaInvoiceId);
    }

    /**
     * Calculate totals from line items.
     */
    protected function calculateTotals(array $data): array
    {
        foreach (['quote_id', 'person_id', 'sales_owner_id', 'created_by'] as $foreignKey) {
            if (array_key_exists($foreignKey, $data) && blank($data[$foreignKey])) {
                $data[$foreignKey] = null;
            }
        }

        $subTotal = 0;
        $items = $data['items'] ?? [];
        $organization = ! empty($data['organization_id'])
            ? Organization::find($data['organization_id'])
            : null;

        foreach ($items as $index => $item) {
            $qty = (float) ($item['qty'] ?? $item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);
            $lineSubTotal = $qty * $unitPrice;
            $lineTotal = max($lineSubTotal, 0);

            $items[$index]['qty'] = $qty;
            $items[$index]['quantity'] = $qty;
            $items[$index]['unit_price'] = $unitPrice;
            $items[$index]['price'] = $unitPrice;
            $items[$index]['discount_amount'] = 0;
            $items[$index]['tax_amount'] = 0;
            $items[$index]['line_subtotal'] = $lineSubTotal;
            $items[$index]['line_total'] = $lineTotal;

            $subTotal += $lineSubTotal;
        }

        $charges = $this->documentChargeManager->normalize($data['charges'] ?? [], $subTotal);
        $chargeSummary = $this->documentChargeManager->summarize('proforma', $charges);
        $discountAmount = 0;
        $grandTotal = max($subTotal + ($chargeSummary['charge_total'] ?? 0), 0);

        $data['items'] = $items;
        $data['billing_address'] = $this->documentAddressManager->normalize($organization, $data['billing_address'] ?? null, 'billing');
        $data['shipping_address'] = $this->documentAddressManager->normalize($organization, $data['shipping_address'] ?? null, 'shipping');
        $data['charges'] = $charges;
        $data['subtotal'] = $subTotal;
        $data['discount_amount'] = $discountAmount;
        $data['tax_amount'] = $chargeSummary['tax_amount'] ?? 0;
        $data['adjustment_amount'] = $chargeSummary['adjustment_amount'] ?? 0;
        $data['tariff_percent'] = $chargeSummary['tariff_percent'] ?? 0;
        $data['freight_percent'] = $chargeSummary['freight_percent'] ?? 0;
        $data['grand_total'] = $grandTotal;
        $data['remaining_amount'] = $grandTotal;

        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }

        return $data;
    }

    /**
     * Sync line items.
     */
    protected function syncItems(int $proformaInvoiceId, array $items): void
    {
        $invoice = $this->findOrFail($proformaInvoiceId);
        $existingIds = $invoice->items->pluck('id')->toArray();
        $savedIds = [];

        foreach ($items as $index => $item) {
            $payload = [
                'proforma_invoice_id' => $proformaInvoiceId,
                'product_id'          => $item['product_id'] ?? null,
                'color_variant_id'    => $item['color_variant_id'] ?? null,
                'color_variant_name'  => $item['color_variant_name'] ?? null,
                'preview_image'       => $item['preview_image'] ?? null,
                'item_name'           => $item['item_name'] ?? '',
                'item_code'           => $item['item_code'] ?? null,
                'description'         => $item['description'] ?? null,
                'qty'                 => $item['qty'] ?? 0,
                'unit'                => $item['unit'] ?? null,
                'unit_price'          => $item['unit_price'] ?? 0,
                'discount_percent'    => $item['discount_percent'] ?? 0,
                'discount_amount'     => $item['discount_amount'] ?? 0,
                'tax_percent'         => $item['tax_percent'] ?? 0,
                'tax_amount'          => $item['tax_amount'] ?? 0,
                'line_subtotal'       => $item['line_subtotal'] ?? 0,
                'line_total'          => $item['line_total'] ?? 0,
                'sort_order'          => $index,
            ];

            if (! empty($item['id'])) {
                $this->proformaInvoiceItemRepository->update($payload, $item['id']);
                $savedIds[] = (int) $item['id'];
            } else {
                $saved = $this->proformaInvoiceItemRepository->create($payload);
                $savedIds[] = $saved->id;
            }
        }

        $deleteIds = array_diff($existingIds, $savedIds);
        foreach ($deleteIds as $deleteId) {
            $this->proformaInvoiceItemRepository->delete($deleteId);
        }
    }
}
