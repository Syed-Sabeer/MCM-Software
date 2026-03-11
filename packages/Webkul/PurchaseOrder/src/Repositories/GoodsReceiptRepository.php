<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\GoodsReceipt;
use Webkul\PurchaseOrder\Models\PurchaseOrder;

class GoodsReceiptRepository extends Repository
{
    public function __construct(
        protected GoodsReceiptItemRepository $goodsReceiptItemRepository,
        protected PurchaseOrderRepository $purchaseOrderRepository,
        protected JobOrderRequirementRepository $jobOrderRequirementRepository,
        protected VendorPayableRepository $vendorPayableRepository,
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
            $postedLines = 0;

            foreach ($data['items'] ?? [] as $item) {
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

                $newReceived = (float) $purchaseOrderItem->received_quantity + $receivedQty;
                $newPending = max((float) $purchaseOrderItem->ordered_quantity - $newReceived, 0);
                $lineStatus = $newPending <= 0 ? 'fully_received' : 'partially_received';

                $purchaseOrderItem->update([
                    'received_quantity' => $newReceived,
                    'pending_quantity' => $newPending,
                    'line_status' => $lineStatus,
                ]);

                if ($purchaseOrderItem->requirement_id) {
                    $this->jobOrderRequirementRepository->applyReceivedQuantity((int) $purchaseOrderItem->requirement_id, $receivedQty);
                }
            }

            if ($postedLines === 0) {
                throw ValidationException::withMessages([
                    'items' => 'Enter a received quantity for at least one line.',
                ]);
            }

            $this->purchaseOrderRepository->refreshReceiptStatus($purchaseOrder->id);
            $this->vendorPayableRepository->createFromReceipt($receipt->fresh('items'), $purchaseOrder->fresh());

            return $receipt->fresh('items');
        });
    }
}
