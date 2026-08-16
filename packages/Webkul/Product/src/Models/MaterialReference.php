<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\Contact\Models\Organization;
use Webkul\PurchaseOrder\Models\MaterialInventory;

class MaterialReference extends Model
{
    protected $fillable = [
        'name',
        'qty',
        'unit',
        'color_name',
        'color_code',
    ];

    protected $casts = [
        'qty' => 'decimal:3',
    ];

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'material_reference_vendor', 'material_reference_id', 'organization_id')
            ->withTimestamps()
            ->orderBy('name');
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(MaterialInventory::class, 'material_reference_id');
    }
}
