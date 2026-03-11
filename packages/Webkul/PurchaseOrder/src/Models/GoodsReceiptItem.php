<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\PurchaseOrder\Contracts\GoodsReceiptItem as GoodsReceiptItemContract;

class GoodsReceiptItem extends Model implements GoodsReceiptItemContract
{
    protected $fillable = ['goods_receipt_id', 'purchase_order_item_id', 'requirement_id', 'material_name', 'received_qty', 'unit', 'unit_price', 'line_total'];

    protected $casts = ['received_qty' => 'decimal:4', 'unit_price' => 'decimal:4', 'line_total' => 'decimal:4'];

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }
}
