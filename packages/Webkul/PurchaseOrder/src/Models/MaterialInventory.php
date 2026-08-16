<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\MaterialReference;

class MaterialInventory extends Model
{
    protected $fillable = [
        'material_reference_id',
        'on_hand',
        'average_unit_cost',
        'reorder_level',
    ];

    protected $casts = [
        'on_hand'          => 'decimal:4',
        'average_unit_cost'=> 'decimal:4',
        'reorder_level'    => 'decimal:4',
    ];

    public function material(): BelongsTo
    {
        return $this->belongsTo(MaterialReference::class, 'material_reference_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MaterialInventoryTransaction::class)->latest('occurred_at')->latest('id');
    }
}
