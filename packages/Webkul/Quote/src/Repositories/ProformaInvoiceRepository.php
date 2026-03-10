<?php

namespace Webkul\Quote\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Core\Eloquent\Repository;
use Webkul\Quote\Models\Quote;

class ProformaInvoiceRepository extends Repository
{
    public function __construct(
        protected ProformaInvoiceItemRepository $proformaInvoiceItemRepository,
        protected ProformaReceiptRepository $proformaReceiptRepository,
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
            'attachment_path'      => $quote->attachment_path,
            'source_type'          => 'quote',
            'status'               => 'draft',
            'created_by'           => auth()->id(),
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

        if ($status !== 'cancelled' && $status !== 'converted' && $status !== 'ready_for_job_order') {
            if ($received <= 0) {
                $status = 'issued';
            } elseif ($remaining > 0) {
                $status = 'partially_paid';
            } else {
                $status = 'fully_paid';
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
        $subTotal = 0;
        $discountAmount = 0;
        $taxAmount = 0;

        $items = $data['items'] ?? [];

        foreach ($items as $index => $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineSubTotal = $qty * $unitPrice;

            $lineDiscountPercent = (float) ($item['discount_percent'] ?? 0);
            $lineDiscountAmount = isset($item['discount_amount']) && $item['discount_amount'] !== ''
                ? (float) $item['discount_amount']
                : ($lineSubTotal * $lineDiscountPercent / 100);

            $taxPercent = (float) ($item['tax_percent'] ?? 0);
            $lineTaxAmount = isset($item['tax_amount']) && $item['tax_amount'] !== ''
                ? (float) $item['tax_amount']
                : max(($lineSubTotal - $lineDiscountAmount), 0) * $taxPercent / 100;

            $lineTotal = max($lineSubTotal - $lineDiscountAmount + $lineTaxAmount, 0);

            $items[$index]['line_subtotal'] = $lineSubTotal;
            $items[$index]['line_total'] = $lineTotal;
            $items[$index]['discount_amount'] = $lineDiscountAmount;
            $items[$index]['tax_amount'] = $lineTaxAmount;

            $subTotal += $lineSubTotal;
            $discountAmount += $lineDiscountAmount;
            $taxAmount += $lineTaxAmount;
        }

        $adjustment = (float) ($data['adjustment_amount'] ?? 0);
        $grandTotal = max($subTotal - $discountAmount + $taxAmount + $adjustment, 0);

        $data['items'] = $items;
        $data['subtotal'] = $subTotal;
        $data['discount_amount'] = $discountAmount;
        $data['tax_amount'] = $taxAmount;
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
