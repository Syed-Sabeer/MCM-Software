<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\PurchaseOrder\Contracts\PurchaseOrderItem as PurchaseOrderItemContract;

class PurchaseOrderItem extends Model implements PurchaseOrderItemContract
{
    protected $table = 'purchase_order_items';

    protected $fillable = [
        'item',
        'description',
        'quantity',
        'price',
        'total',
        'purchase_order_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'decimal:4',
        'total'    => 'decimal:4',
    ];

    /**
     * Get the purchase order.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderProxy::modelClass());
    }
}
