<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\PurchaseOrder\Models\VendorQuote;

class PurchaseOrderRepository extends Repository
{
    public function __construct(
        protected PurchaseOrderItemRepository $purchaseOrderItemRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return 'Webkul\PurchaseOrder\Contracts\PurchaseOrder';
    }

    public function createFromVendorQuote(VendorQuote $vendorQuote, array $overrides = []): PurchaseOrder
    {
        $payload = array_merge([
            'vendor_quote_id' => $vendorQuote->id,
            'job_order_id' => $vendorQuote->job_order_id,
            'organization_id' => $vendorQuote->organization_id,
            'person_id' => $vendorQuote->person_id,
            'user_id' => auth()->id(),
            'po_number' => PurchaseOrder::generateNextPoNumber(),
            'job_number' => optional($vendorQuote->jobOrder)->job_order_number,
            'notes' => $vendorQuote->notes,
            'payment_term' => '',
            'shipping_method' => '',
            'expected_receive_date' => optional($vendorQuote->items->sortBy('expected_receive_date')->firstWhere('expected_receive_date', '!=', null))->expected_receive_date?->toDateString(),
            'status' => 'draft',
            'items' => $vendorQuote->items->map(fn ($item) => [
                'requirement_id' => $item->requirement_id,
                'item' => $item->material_name,
                'material_name' => $item->material_name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'ordered_quantity' => $item->quantity,
                'received_quantity' => 0,
                'pending_quantity' => $item->quantity,
                'unit' => $item->unit ?: optional($item->requirement)->unit,
                'price' => $item->unit_price,
                'expected_receive_date' => $item->expected_receive_date?->toDateString(),
                'line_status' => 'open',
            ])->toArray(),
        ], $overrides);

        return $this->create($payload);
    }

    public function createFromJobOrder(JobOrder $jobOrder, array $overrides = []): PurchaseOrder
    {
        $payload = array_merge([
            'job_order_id' => $jobOrder->id,
            'organization_id' => null,
            'person_id' => null,
            'user_id' => auth()->id(),
            'po_number' => PurchaseOrder::generateNextPoNumber(),
            'job_number' => $jobOrder->job_order_number,
            'expected_receive_date' => $jobOrder->required_delivery_date?->toDateString(),
            'status' => 'draft',
            'items' => $jobOrder->requirements->map(fn ($requirement) => [
                'requirement_id' => $requirement->id,
                'item' => $requirement->material_name,
                'material_name' => $requirement->material_name,
                'description' => null,
                'quantity' => $requirement->balance_qty,
                'ordered_quantity' => $requirement->balance_qty,
                'received_quantity' => 0,
                'pending_quantity' => $requirement->balance_qty,
                'unit' => $requirement->unit,
                'price' => 0,
                'expected_receive_date' => $jobOrder->required_delivery_date?->toDateString(),
                'line_status' => 'open',
            ])->toArray(),
        ], $overrides);

        return $this->create($payload);
    }

    public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data) {
            $data = $this->calculateTotals($data);

            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = parent::create($data);

            $this->syncItems($purchaseOrder, $data);
            $this->refreshReceiptStatus($purchaseOrder->id);

            return $purchaseOrder->fresh(['items', 'receipts', 'payables']);
        });
    }

    public function update(array $data, $id, $attribute = 'id'): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            $data = $this->calculateTotals($data);

            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = parent::update($data, $id, $attribute);

            $this->syncItems($purchaseOrder, $data);
            $this->refreshReceiptStatus($purchaseOrder->id);

            return $purchaseOrder->fresh(['items', 'receipts', 'payables']);
        });
    }

    protected function calculateTotals(array $data): array
    {
        $subTotal = 0;

        if (! empty($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                $orderedQuantity = (float) ($item['ordered_quantity'] ?? $item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? 0);
                $total = $orderedQuantity * $price;
                $data['items'][$index]['ordered_quantity'] = $orderedQuantity;
                $data['items'][$index]['quantity'] = $orderedQuantity;
                $data['items'][$index]['total'] = $total;
                $data['items'][$index]['pending_quantity'] = max((float) ($item['pending_quantity'] ?? $orderedQuantity), 0);
                $subTotal += $total;
            }
        }

        $salesTaxPercent = (float) ($data['sales_tax_percent'] ?? 0);
        $taxAmount = $subTotal * ($salesTaxPercent / 100);
        $freight = (float) ($data['freight'] ?? 0);
        $grandTotal = $subTotal + $taxAmount + $freight;

        $data['sub_total'] = $subTotal;
        $data['tax_amount'] = $taxAmount;
        $data['grand_total'] = $grandTotal;

        return $data;
    }

    protected function syncItems(PurchaseOrder $purchaseOrder, array $data): void
    {
        $existingItemIds = $purchaseOrder->items()->pluck('id')->toArray();
        $newItemIds = [];

        foreach ($data['items'] ?? [] as $itemData) {
            $orderedQuantity = (float) ($itemData['ordered_quantity'] ?? $itemData['quantity'] ?? 0);
            $receivedQuantity = (float) ($itemData['received_quantity'] ?? 0);
            $pendingQuantity = max($orderedQuantity - $receivedQuantity, 0);
            $price = (float) ($itemData['price'] ?? 0);

            $payload = [
                'purchase_order_id' => $purchaseOrder->id,
                'requirement_id' => $itemData['requirement_id'] ?? null,
                'product_id' => $itemData['product_id'] ?? null,
                'item' => $itemData['item'] ?? ($itemData['material_name'] ?? ''),
                'material_name' => $itemData['material_name'] ?? ($itemData['item'] ?? ''),
                'description' => $itemData['description'] ?? null,
                'quantity' => $orderedQuantity,
                'ordered_quantity' => $orderedQuantity,
                'received_quantity' => $receivedQuantity,
                'pending_quantity' => $pendingQuantity,
                'unit' => $itemData['unit'] ?? null,
                'price' => $price,
                'total' => $orderedQuantity * $price,
                'expected_receive_date' => $itemData['expected_receive_date'] ?? null,
                'line_status' => $itemData['line_status'] ?? ($pendingQuantity <= 0 ? 'fully_received' : ($receivedQuantity > 0 ? 'partially_received' : 'open')),
            ];

            if (! empty($itemData['id'])) {
                $this->purchaseOrderItemRepository->update($payload, $itemData['id']);
                $newItemIds[] = (int) $itemData['id'];
            } else {
                $newItemIds[] = $this->purchaseOrderItemRepository->create($payload)->id;
            }
        }

        foreach (array_diff($existingItemIds, $newItemIds) as $idToDelete) {
            $this->purchaseOrderItemRepository->delete($idToDelete);
        }
    }

    public function refreshReceiptStatus(int $purchaseOrderId): void
    {
        /** @var PurchaseOrder $purchaseOrder */
        $purchaseOrder = $this->with('items')->findOrFail($purchaseOrderId);

        if (in_array($purchaseOrder->status, ['cancelled', 'closed'], true)) {
            return;
        }

        $ordered = (float) $purchaseOrder->items->sum('ordered_quantity');
        $received = (float) $purchaseOrder->items->sum('received_quantity');

        $status = 'issued';

        if ($received <= 0) {
            $status = 'issued';
        } elseif ($received < $ordered) {
            $status = 'partially_received';
        } elseif ($ordered > 0) {
            $status = 'fully_received';
        }

        parent::update([
            'status' => $status,
            'closed_at' => null,
            'closed_by' => null,
        ], $purchaseOrderId);
    }
}
