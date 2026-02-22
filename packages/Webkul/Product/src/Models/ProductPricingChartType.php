<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPricingChartType extends Model
{
    protected $fillable = [
        'product_pricing_chart_id',
        'type',
        'sort_order',
    ];

    public function pricingChart(): BelongsTo
    {
        return $this->belongsTo(ProductPricingChart::class, 'product_pricing_chart_id');
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(ProductPricingChartTier::class, 'product_pricing_chart_type_id')->orderBy('sort_order');
    }
}
