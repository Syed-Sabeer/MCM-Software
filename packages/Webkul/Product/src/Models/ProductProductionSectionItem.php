<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductProductionSectionItem extends Model
{
    protected $fillable = [
        'product_production_section_id',
        'name',
        'qty',
        'unit',
        'sort_order',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ProductProductionSection::class, 'product_production_section_id');
    }
}
