<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\Quote\Models\ProformaInvoice;

class JobOrderRepository extends Repository
{
    public function __construct(
        protected JobOrderItemRepository $jobOrderItemRepository,
        protected JobCardRepository $jobCardRepository,
        protected JobOrderRequirementRepository $jobOrderRequirementRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return JobOrder::class;
    }

    public function createFromProforma(ProformaInvoice $proformaInvoice, array $overrides = []): JobOrder
    {
        $payload = array_merge([
            'proforma_invoice_id' => $proformaInvoice->id,
            'organization_id' => $proformaInvoice->organization_id,
            'person_id' => $proformaInvoice->person_id,
            'customer_po_reference' => $proformaInvoice->customer_po_reference,
            'subject' => $proformaInvoice->subject,
            'issue_date' => now()->toDateString(),
            'required_delivery_date' => $proformaInvoice->due_date?->toDateString(),
            'status' => 'open',
            'remarks' => $proformaInvoice->notes,
            'created_by' => auth()->id(),
            'items' => $proformaInvoice->items->map(fn ($item) => [
                'proforma_invoice_item_id' => $item->id,
                'product_id' => $item->product_id,
                'item_name' => $item->item_name,
                'item_code' => $item->item_code,
                'description' => $item->description,
                'qty' => $item->qty,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
            ])->toArray(),
        ], $overrides);

        return $this->create($payload);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['total_order_qty'] = collect($data['items'] ?? [])->sum(fn ($item) => (float) ($item['qty'] ?? 0));
            /** @var JobOrder $jobOrder */
            $jobOrder = parent::create($data);
            $this->syncItems($jobOrder, $data['items'] ?? []);
            $this->jobCardRepository->regenerateForJobOrder($jobOrder->fresh('items'));
            $this->jobOrderRequirementRepository->regenerateForJobOrder($jobOrder->fresh('items'));

            return $jobOrder->fresh(['items', 'jobCards.sections.items', 'requirements']);
        });
    }

    public function update(array $data, $id, $attribute = 'id')
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            $data['total_order_qty'] = collect($data['items'] ?? [])->sum(fn ($item) => (float) ($item['qty'] ?? 0));
            /** @var JobOrder $jobOrder */
            $jobOrder = parent::update($data, $id, $attribute);
            $this->syncItems($jobOrder, $data['items'] ?? []);
            $this->jobCardRepository->regenerateForJobOrder($jobOrder->fresh('items'));
            $this->jobOrderRequirementRepository->regenerateForJobOrder($jobOrder->fresh('items'));

            return $jobOrder->fresh(['items', 'jobCards.sections.items', 'requirements']);
        });
    }

    protected function syncItems(JobOrder $jobOrder, array $items): void
    {
        $existingIds = $jobOrder->items()->pluck('id')->toArray();
        $savedIds = [];

        foreach ($items as $index => $item) {
            $qty = (float) ($item['qty'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? $item['rate'] ?? 0);
            $lineTotal = (float) ($item['line_total'] ?? $item['amount'] ?? ($qty * $unitPrice));

            $payload = [
                'job_order_id' => $jobOrder->id,
                'proforma_invoice_item_id' => $item['proforma_invoice_item_id'] ?? null,
                'product_id' => $item['product_id'] ?? null,
                'item_name' => $item['item_name'] ?? '',
                'item_code' => $item['item_code'] ?? null,
                'description' => $item['description'] ?? null,
                'qty' => $qty,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'sort_order' => $index,
            ];

            if (! empty($item['id'])) {
                $this->jobOrderItemRepository->update($payload, $item['id']);
                $savedIds[] = (int) $item['id'];
            } else {
                $savedIds[] = $this->jobOrderItemRepository->create($payload)->id;
            }
        }

        foreach (array_diff($existingIds, $savedIds) as $deleteId) {
            $this->jobOrderItemRepository->delete($deleteId);
        }
    }
}

