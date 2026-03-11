<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model implements \Webkul\PurchaseOrder\Contracts\PurchaseOrderItem
{
    protected $table = 'purchase_order_items';

    protected $fillable = [
        'item', 'material_name', 'description', 'quantity', 'ordered_quantity', 'received_quantity', 'pending_quantity', 'unit', 'price', 'total',
        'purchase_order_id', 'requirement_id', 'product_id', 'expected_receive_date', 'line_status',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'ordered_quantity' => 'decimal:4',
        'received_quantity' => 'decimal:4',
        'pending_quantity' => 'decimal:4',
        'price' => 'decimal:4',
        'total' => 'decimal:4',
        'expected_receive_date' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderProxy::modelClass());
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(JobOrderRequirement::class, 'requirement_id');
    }
}
