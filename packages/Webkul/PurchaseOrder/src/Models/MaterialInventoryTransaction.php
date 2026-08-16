<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Product\Models\MaterialReference;
use Webkul\User\Models\UserProxy;

class MaterialInventoryTransaction extends Model
{
    protected $fillable = [
        'material_inventory_id',
        'material_reference_id',
        'type',
        'quantity',
        'unit_cost',
        'total_value',
        'balance_after',
        'average_cost_after',
        'reference_type',
        'reference_id',
        'reference_line_id',
        'notes',
        'occurred_at',
        'created_by',
    ];

    protected $casts = [
        'quantity'          => 'decimal:4',
        'unit_cost'         => 'decimal:4',
        'total_value'       => 'decimal:4',
        'balance_after'     => 'decimal:4',
        'average_cost_after'=> 'decimal:4',
        'occurred_at'       => 'datetime',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(MaterialInventory::class, 'material_inventory_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(MaterialReference::class, 'material_reference_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'created_by');
    }
}
