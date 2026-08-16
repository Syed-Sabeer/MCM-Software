<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Services\UnitConversionService;
use Webkul\PurchaseOrder\Models\GoodsReceipt;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\PurchaseOrder\Services\MaterialInventoryService;

class GoodsReceiptRepository extends Repository
{
    public function __construct(
        protected GoodsReceiptItemRepository $goodsReceiptItemRepository,
        protected PurchaseOrderRepository $purchaseOrderRepository,
        protected JobOrderRequirementRepository $jobOrderRequirementRepository,
        protected VendorPayableRepository $vendorPayableRepository,
        protected MaterialInventoryService $materialInventoryService,
        protected UnitConversionService $unitConversionService,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return GoodsReceipt::class;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = PurchaseOrder::with('items')->findOrFail($data['purchase_order_id']);
            $receipt = parent::create($data);
            $this->applyReceiptItems($receipt, $purchaseOrder, $data['items'] ?? []);
            $this->materialInventoryService->syncGoodsReceipt($receipt->fresh('items'));

            return $receipt->fresh('items');
        });
    }

    public function update(array $data, $id, $attribute = 'id')
    {
        return DB::transaction(function () use ($data, $id, $attribute) {
            /** @var GoodsReceipt $receipt */
            $receipt = $this->with('items')->findOrFail($id);
            /** @var PurchaseOrder $purchaseOrder */
            $purchaseOrder = PurchaseOrder::with('items')->findOrFail($receipt->purchase_order_id);
            $receipt = parent::update($data, $id, $attribute);

            $this->appendReceiptItems($receipt->fresh('items'), $purchaseOrder, $data['items'] ?? []);
            $this->materialInventoryService->syncGoodsReceipt($receipt->fresh('items'));

            return $receipt->fresh('items');
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            /** @var GoodsReceipt $receipt */
            $receipt = $this->with('items')->findOrFail($id);
            $this->materialInventoryService->syncGoodsReceipt($receipt, true);
            /** @var PurchaseOrder|null $purchaseOrder */
            $purchaseOrder = PurchaseOrder::with('items')->find($receipt->purchase_order_id);

            if ($purchaseOrder) {
                $this->reverseReceiptEffects($receipt, $purchaseOrder);
            } else {
                $this->vendorPayableRepository->deleteByReceipt($receipt->id);
            }

            $receipt->items()->delete();

            return parent::delete($id);
        });
    }

    protected function applyReceiptItems(GoodsReceipt $receipt, PurchaseOrder $purchaseOrder, array $items): void
    {
        $postedLines = 0;

        foreach ($items as $item) {
            $purchaseOrderItem = $purchaseOrder->items->firstWhere('id', (int) ($item['purchase_order_item_id'] ?? 0));
            if (! $purchaseOrderItem) {
                continue;
            }

            $receivedQty = (float) ($item['received_qty'] ?? 0);
            if ($receivedQty <= 0) {
                continue;
            }

            $pending = (float) $purchaseOrderItem->pending_quantity;
            if ($receivedQty > $pending) {
                throw ValidationException::withMessages([
                    'items' => 'Received quantity cannot exceed pending quantity.',
                ]);
            }

            $lineTotal = $receivedQty * (float) $purchaseOrderItem->price;

            $this->goodsReceiptItemRepository->create([
                'goods_receipt_id' => $receipt->id,
                'purchase_order_item_id' => $purchaseOrderItem->id,
                'requirement_id' => $purchaseOrderItem->requirement_id,
                'material_name' => $purchaseOrderItem->material_name ?: $purchaseOrderItem->item,
                'received_qty' => $receivedQty,
                'unit' => $purchaseOrderItem->unit,
                'unit_price' => $purchaseOrderItem->price,
                'line_total' => $lineTotal,
            ]);
            $postedLines++;

            $this->applyItemReceipt($purchaseOrderItem, $receivedQty);
        }

        if ($postedLines === 0) {
            throw ValidationException::withMessages([
                'items' => 'Enter a received quantity for at least one line.',
            ]);
        }

        $this->purchaseOrderRepository->refreshReceiptStatus($purchaseOrder->id);
        $this->vendorPayableRepository->deleteByReceipt($receipt->id);
        $this->vendorPayableRepository->createFromReceipt($receipt->fresh('items'), $purchaseOrder->fresh());
    }

    protected function appendReceiptItems(GoodsReceipt $receipt, PurchaseOrder $purchaseOrder, array $items): void
    {
        $postedLines = 0;

        foreach ($items as $item) {
            $purchaseOrderItem = $purchaseOrder->items->firstWhere('id', (int) ($item['purchase_order_item_id'] ?? 0));

            if (! $purchaseOrderItem) {
                continue;
            }

            $receivedQty = (float) ($item['received_qty'] ?? 0);

            if ($receivedQty <= 0) {
                continue;
            }

            $pending = (float) $purchaseOrderItem->pending_quantity;

            if ($receivedQty > $pending) {
                throw ValidationException::withMessages([
                    'items' => 'Received quantity cannot exceed pending quantity.',
                ]);
            }

            $existingReceiptItem = $receipt->items->firstWhere('purchase_order_item_id', $purchaseOrderItem->id);
            $newReceiptQty = $receivedQty;
            $newLineTotal = $receivedQty * (float) $purchaseOrderItem->price;

            if ($existingReceiptItem) {
                $newReceiptQty += (float) $existingReceiptItem->received_qty;
                $newLineTotal += (float) $existingReceiptItem->line_total;

                $this->goodsReceiptItemRepository->update([
                    'received_qty' => $newReceiptQty,
                    'line_total'   => $newLineTotal,
                    'unit_price'   => $purchaseOrderItem->price,
                    'unit'         => $purchaseOrderItem->unit,
                    'material_name'=> $purchaseOrderItem->material_name ?: $purchaseOrderItem->item,
                    'requirement_id' => $purchaseOrderItem->requirement_id,
                ], $existingReceiptItem->id);
            } else {
                $this->goodsReceiptItemRepository->create([
                    'goods_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'requirement_id' => $purchaseOrderItem->requirement_id,
                    'material_name' => $purchaseOrderItem->material_name ?: $purchaseOrderItem->item,
                    'received_qty' => $receivedQty,
                    'unit' => $purchaseOrderItem->unit,
                    'unit_price' => $purchaseOrderItem->price,
                    'line_total' => $newLineTotal,
                ]);
            }

            $postedLines++;
            $this->applyItemReceipt($purchaseOrderItem, $receivedQty);
        }

        if ($postedLines === 0) {
            throw ValidationException::withMessages([
                'items' => 'Enter a received quantity for at least one line.',
            ]);
        }

        $this->purchaseOrderRepository->refreshReceiptStatus($purchaseOrder->id);
        $this->vendorPayableRepository->deleteByReceipt($receipt->id);
        $this->vendorPayableRepository->createFromReceipt($receipt->fresh('items'), $purchaseOrder->fresh());
    }

    protected function reverseReceiptEffects(GoodsReceipt $receipt, PurchaseOrder $purchaseOrder): void
    {
        foreach ($receipt->items as $receiptItem) {
            $purchaseOrderItem = $purchaseOrder->items->firstWhere('id', $receiptItem->purchase_order_item_id);
            if (! $purchaseOrderItem) {
                continue;
            }

            $newReceived = max((float) $purchaseOrderItem->received_quantity - (float) $receiptItem->received_qty, 0);
            $newPending = max((float) $purchaseOrderItem->ordered_quantity - $newReceived, 0);
            $lineStatus = $newReceived <= 0 ? 'open' : ($newPending <= 0 ? 'fully_received' : 'partially_received');

            $purchaseOrderItem->update([
                'received_quantity' => $newReceived,
                'pending_quantity' => $newPending,
                'line_status' => $lineStatus,
            ]);

            if ($receiptItem->requirement_id) {
                $requirement = $this->jobOrderRequirementRepository->find($receiptItem->requirement_id);

                if (! $requirement) {
                    continue;
                }

                $requirementReceivedQty = $this->convertRequirementQuantity(
                    (float) $receiptItem->received_qty,
                    (string) $receiptItem->unit,
                    (string) $requirement->unit
                );
                $receivedQty = max((float) $requirement->received_qty - $requirementReceivedQty, 0);
                $vendorRequired = max((float) $requirement->required_qty - (float) $requirement->inventory_allocated_qty, 0);
                $receivedQty = min($receivedQty, $vendorRequired);
                $balanceQty = max($vendorRequired - $receivedQty, 0);
                $status = $balanceQty <= 0
                    ? 'fulfilled'
                    : ($receivedQty > 0 || (float) $requirement->inventory_allocated_qty > 0 ? 'partial' : 'pending');

                $this->jobOrderRequirementRepository->update([
                    'received_qty' => $receivedQty,
                    'balance_qty' => $balanceQty,
                    'status' => $status,
                ], $requirement->id);
            }
        }

        $this->vendorPayableRepository->deleteByReceipt($receipt->id);
        $this->purchaseOrderRepository->refreshReceiptStatus($purchaseOrder->id);
    }

    protected function applyItemReceipt($purchaseOrderItem, float $receivedQty): void
    {
        $newReceived = (float) $purchaseOrderItem->received_quantity + $receivedQty;
        $newPending = max((float) $purchaseOrderItem->ordered_quantity - $newReceived, 0);
        $lineStatus = $newPending <= 0 ? 'fully_received' : 'partially_received';

        $purchaseOrderItem->update([
            'received_quantity' => $newReceived,
            'pending_quantity' => $newPending,
            'line_status' => $lineStatus,
        ]);

        if ($purchaseOrderItem->requirement_id) {
            $requirement = $this->jobOrderRequirementRepository->findOrFail((int) $purchaseOrderItem->requirement_id);
            $requirementReceivedQty = $this->convertRequirementQuantity(
                $receivedQty,
                (string) $purchaseOrderItem->unit,
                (string) $requirement->unit
            );

            $this->jobOrderRequirementRepository->applyReceivedQuantity($requirement->id, $requirementReceivedQty);
        }
    }

    protected function convertRequirementQuantity(float $quantity, string $fromUnit, string $toUnit): float
    {
        $converted = $this->unitConversionService->convert($quantity, $fromUnit, $toUnit);

        if ($converted === null) {
            throw ValidationException::withMessages([
                'items' => "The received unit {$fromUnit} cannot be converted to the material stock unit {$toUnit}.",
            ]);
        }

        return round($converted, 4);
    }
}
