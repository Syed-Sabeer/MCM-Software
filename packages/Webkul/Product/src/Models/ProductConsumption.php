<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Contact\Models\Organization;

class ProductConsumption extends Model
{
    protected $fillable = [
        'product_id',
        'material_reference_id',
        'name',
        'qty',
        'unit',
        'vendor_ids',
        'color_name',
        'color_code',
        'sort_order',
    ];

    protected $casts = [
        'qty' => 'decimal:4',
        'vendor_ids' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function materialReference(): BelongsTo
    {
        return $this->belongsTo(MaterialReference::class, 'material_reference_id');
    }

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'material_reference_vendor', 'material_reference_id', 'organization_id', 'material_reference_id', 'id');
    }
}
