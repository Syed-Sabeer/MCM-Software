<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\VendorQuote;

class VendorQuoteRepository extends Repository
{
    public function __construct(protected VendorQuoteItemRepository $vendorQuoteItemRepository, Container $container)
    {
        parent::__construct($container);
    }

    public function model(): string
    {
        return VendorQuote::class;
    }

    public function createFromJobOrder(JobOrder $jobOrder, array $overrides = []): VendorQuote
    {
        $payload = array_merge([
            'job_order_id' => $jobOrder->id,
            'issue_date' => now()->toDateString(),
            'status' => 'draft',
            'created_by' => auth()->id(),
            'items' => $jobOrder->requirements->map(fn ($requirement) => [
                'requirement_id' => $requirement->id,
                'material_name' => $requirement->material_name,
                'quantity' => $requirement->balance_qty,
                'unit' => $requirement->unit,
                'expected_receive_date' => $jobOrder->required_delivery_date?->toDateString(),
            ])->toArray(),
        ], $overrides);

        return $this->create($payload);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $quote = parent::create($data);
            $this->syncItems($quote, $data['items'] ?? []);
            return $quote->fresh('items');
        });
    }

    public function update(array $data, $id, $attribute = 'id')
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            $quote = parent::update($data, $id, $attribute);
            $this->syncItems($quote, $data['items'] ?? []);
            return $quote->fresh('items');
        });
    }

    protected function syncItems(VendorQuote $vendorQuote, array $items): void
    {
        $existingIds = $vendorQuote->items()->pluck('id')->toArray();
        $savedIds = [];

        foreach ($items as $index => $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $payload = [
                'vendor_quote_id' => $vendorQuote->id,
                'requirement_id' => $item['requirement_id'] ?? null,
                'material_name' => $item['material_name'] ?? '',
                'description' => $item['description'] ?? null,
                'quantity' => $quantity,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $unitPrice,
                'total' => $quantity * $unitPrice,
                'vendor_lead_time' => $item['vendor_lead_time'] ?? null,
                'expected_receive_date' => $item['expected_receive_date'] ?? null,
                'sort_order' => $index,
            ];

            if (! empty($item['id'])) {
                $this->vendorQuoteItemRepository->update($payload, $item['id']);
                $savedIds[] = (int) $item['id'];
            } else {
                $savedIds[] = $this->vendorQuoteItemRepository->create($payload)->id;
            }
        }

        foreach (array_diff($existingIds, $savedIds) as $deleteId) {
            $this->vendorQuoteItemRepository->delete($deleteId);
        }
    }
}
