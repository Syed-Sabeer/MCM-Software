<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Webkul\Core\Eloquent\Repository;

class PurchaseOrderRepository extends Repository
{
    /**
     * Create a new repository instance.
     */
    public function __construct(
        protected PurchaseOrderItemRepository $purchaseOrderItemRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return 'Webkul\PurchaseOrder\Contracts\PurchaseOrder';
    }

    /**
     * Create purchase order.
     */
    public function create(array $data): \Webkul\PurchaseOrder\Contracts\PurchaseOrder
    {
        $data = $this->calculateTotals($data);

        $purchaseOrder = parent::create($data);

        $this->syncItems($purchaseOrder, $data);

        return $purchaseOrder;
    }

    /**
     * Update purchase order.
     */
    public function update(array $data, $id, $attribute = 'id'): \Webkul\PurchaseOrder\Contracts\PurchaseOrder
    {
        $data = $this->calculateTotals($data);

        $purchaseOrder = parent::update($data, $id, $attribute);

        $this->syncItems($purchaseOrder, $data);

        return $purchaseOrder;
    }

    /**
     * Calculate totals from items.
     */
    protected function calculateTotals(array $data): array
    {
        $subTotal = 0;

        if (! empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $quantity = floatval($item['quantity'] ?? 0);
                $price = floatval($item['price'] ?? 0);
                $subTotal += $quantity * $price;
            }
        }

        $salesTaxPercent = floatval($data['sales_tax_percent'] ?? 0);
        $taxAmount = $subTotal * ($salesTaxPercent / 100);
        $freight = floatval($data['freight'] ?? 0);
        $grandTotal = $subTotal + $taxAmount + $freight;

        $data['sub_total'] = $subTotal;
        $data['tax_amount'] = $taxAmount;
        $data['grand_total'] = $grandTotal;

        return $data;
    }

    /**
     * Sync items with purchase order.
     */
    protected function syncItems($purchaseOrder, array $data): void
    {
        $existingItemIds = $purchaseOrder->items->pluck('id')->toArray();
        $newItemIds = [];

        if (! empty($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $itemData['purchase_order_id'] = $purchaseOrder->id;

                $quantity = floatval($itemData['quantity'] ?? 0);
                $price = floatval($itemData['price'] ?? 0);
                $itemData['total'] = $quantity * $price;

                if (! empty($itemData['id'])) {
                    $this->purchaseOrderItemRepository->update($itemData, $itemData['id']);
                    $newItemIds[] = $itemData['id'];
                } else {
                    $item = $this->purchaseOrderItemRepository->create($itemData);
                    $newItemIds[] = $item->id;
                }
            }
        }

        $idsToDelete = array_diff($existingItemIds, $newItemIds);

        foreach ($idsToDelete as $idToDelete) {
            $this->purchaseOrderItemRepository->delete($idToDelete);
        }
    }
}
