<?php

namespace Webkul\PurchaseOrder\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\GoodsReceipt;
use Webkul\PurchaseOrder\Models\PurchaseOrder;
use Webkul\PurchaseOrder\Models\VendorPayable;

class VendorPayableRepository extends Repository
{
    public function model(): string
    {
        return VendorPayable::class;
    }

    public function createFromReceipt(GoodsReceipt $goodsReceipt, PurchaseOrder $purchaseOrder): VendorPayable
    {
        $amount = (float) $goodsReceipt->items->sum('line_total');

        return $this->create([
            'purchase_order_id' => $purchaseOrder->id,
            'goods_receipt_id' => $goodsReceipt->id,
            'organization_id' => $purchaseOrder->organization_id,
            'payable_date' => $goodsReceipt->receipt_date,
            'total_amount' => $amount,
            'paid_amount' => 0,
            'remaining_amount' => $amount,
            'status' => 'open',
            'notes' => 'Auto-created from goods receipt ' . $goodsReceipt->goods_receipt_number,
        ]);
    }
}
