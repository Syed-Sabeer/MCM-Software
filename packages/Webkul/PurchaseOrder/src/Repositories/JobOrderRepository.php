<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Services\MaterialInventoryService;
use Webkul\Quote\Models\ProformaInvoice;

class JobOrderRepository extends Repository
{
    public function __construct(
        protected JobOrderItemRepository $jobOrderItemRepository,
        protected JobCardRepository $jobCardRepository,
        protected JobOrderRequirementRepository $jobOrderRequirementRepository,
        protected MaterialInventoryService $materialInventoryService,
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
        $proformaInvoice->loadMissing(['items', 'quote']);
        $sourceItems = $proformaInvoice->items->map(fn ($item) => [
            'proforma_invoice_item_id' => $item->id,
            'product_id' => $item->product_id,
            'item_name' => $item->item_name,
            'item_code' => $item->item_code,
            'description' => $item->description,
            'qty' => $item->qty,
            'unit' => $item->unit,
            'unit_price' => $item->unit_price,
            'line_total' => $item->line_total,
        ])->toArray();

        $payload = array_merge([
            'proforma_invoice_id' => $proformaInvoice->id,
            'organization_id' => $proformaInvoice->organization_id,
            'person_id' => $proformaInvoice->person_id,
            'customer_po_reference' => $proformaInvoice->customer_po_reference,
            'subject' => $proformaInvoice->subject,
            'issue_date' => now()->toDateString(),
            'required_delivery_date' => $proformaInvoice->quote?->etd?->toDateString()
                ?: $proformaInvoice->due_date?->toDateString(),
            'status' => 'open',
            'remarks' => $proformaInvoice->notes,
            'created_by' => auth()->id(),
            'items' => $sourceItems,
        ], $overrides);

        // These values always come from the latest proforma, not hidden form fields.
        $payload['proforma_invoice_id'] = $proformaInvoice->id;
        $payload['organization_id'] = $proformaInvoice->organization_id;
        $payload['person_id'] = $proformaInvoice->person_id;
        $payload['customer_po_reference'] = $proformaInvoice->customer_po_reference;
        $payload['subject'] = $proformaInvoice->subject;
        $payload['items'] = $sourceItems;

        return DB::transaction(function () use ($proformaInvoice, $payload) {
            ProformaInvoice::query()->whereKey($proformaInvoice->id)->lockForUpdate()->firstOrFail();

            $existing = JobOrder::query()
                ->where('proforma_invoice_id', $proformaInvoice->id)
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if (! $existing) {
                return $this->create($payload);
            }

            $existingItemIds = $existing->items()
                ->whereNotNull('proforma_invoice_item_id')
                ->pluck('id', 'proforma_invoice_item_id');

            $payload['items'] = collect($payload['items'])->map(function ($item) use ($existingItemIds) {
                $item['id'] = $existingItemIds->get($item['proforma_invoice_item_id']);

                return $item;
            })->all();
            $payload['job_order_number'] = $existing->job_order_number;
            $payload['created_by'] = $existing->created_by;

            return $this->update($payload, $existing->id);
        });
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
            $this->materialInventoryService->syncJobOrder($jobOrder->fresh('requirements'));

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
            $this->materialInventoryService->syncJobOrder($jobOrder->fresh('requirements'));

            return $jobOrder->fresh(['items', 'jobCards.sections.items', 'requirements']);
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            /** @var JobOrder $jobOrder */
            $jobOrder = $this->findOrFail($id);
            $this->materialInventoryService->syncJobOrder($jobOrder, true);

            return parent::delete($id);
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
