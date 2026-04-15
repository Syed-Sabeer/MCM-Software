<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Core\Support\DocumentChargeManager;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\PurchaseOrder\Models\VendorQuote;

class PurchaseOrderRepository extends Repository
{
    public function __construct(
        protected PurchaseOrderItemRepository $purchaseOrderItemRepository,
        protected DocumentChargeManager $documentChargeManager,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return 'Webkul\\PurchaseOrder\\Contracts\\PurchaseOrder';
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
            'payment_term' => $vendorQuote->payment_term,
            'shipping_method' => $vendorQuote->shipping_method,
            'completion_date' => $vendorQuote->first_delivery_date?->toDateString(),
            'last_delivery_date' => $vendorQuote->last_delivery_date?->toDateString(),
            'sales_tax_percent' => (float) ($vendorQuote->sales_tax_percent ?? 0),
            'freight' => (float) ($vendorQuote->freight ?? 0),
            'expected_receive_date' => optional($vendorQuote->last_delivery_date ?: $vendorQuote->first_delivery_date)->toDateString(),
            'status' => 'draft',
            'charges' => $this->documentChargeManager->extract($vendorQuote, 'vendor_quote'),
            'items' => $vendorQuote->items->map(fn ($item) => [
                'requirement_id' => $item->requirement_id,
                'item' => $item->material_name,
                'material_name' => $item->material_name,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'ordered_quantity' => $item->quantity,
                'received_quantity' => 0,
                'pending_quantity' => $item->quantity,
                'unit' => $item->unit ?: optional($item->requirement)->unit ?: 'PCS',
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
                'unit' => $requirement->unit ?: 'PCS',
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
            $data = $this->normalizeOptionalFields($data);
            $data = $this->calculateTotals($data);

            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = parent::create($data);

            $this->syncItems($purchaseOrder, $data);
            $this->documentChargeManager->sync($purchaseOrder, $data['charges'] ?? []);
            $this->refreshReceiptStatus($purchaseOrder->id);

            return $purchaseOrder->fresh(['items', 'receipts', 'payables']);
        });
    }

    public function update(array $data, $id, $attribute = 'id'): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            $data = $this->normalizeOptionalFields($data);
            $data = $this->calculateTotals($data);

            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = parent::update($data, $id, $attribute);

            $this->syncItems($purchaseOrder, $data);
            $this->documentChargeManager->sync($purchaseOrder, $data['charges'] ?? []);
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

        $charges = $this->documentChargeManager->normalize($data['charges'] ?? [], $subTotal);
        $chargeSummary = $this->documentChargeManager->summarize('purchase_order', $charges);
        $grandTotal = $subTotal + ($chargeSummary['charge_total'] ?? 0);

        $data['sub_total'] = $subTotal;
        $data['charges'] = $charges;
        $data['sales_tax_percent'] = $chargeSummary['sales_tax_percent'] ?? 0;
        $data['tax_amount'] = $chargeSummary['tax_amount'] ?? 0;
        $data['freight'] = $chargeSummary['freight'] ?? 0;
        $data['grand_total'] = $grandTotal;

        return $data;
    }

    protected function normalizeOptionalFields(array $data): array
    {
        foreach ([
            'person_id',
            'job_order_id',
            'vendor_quote_id',
            'completion_date',
            'last_delivery_date',
            'expected_receive_date',
            'payment_term',
            'shipping_method',
            'notes',
            'terms',
        ] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

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
                'unit' => ! empty($itemData['unit']) ? $itemData['unit'] : 'PCS',
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
