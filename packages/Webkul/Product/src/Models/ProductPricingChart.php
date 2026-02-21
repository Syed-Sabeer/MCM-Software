<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPricingChart extends Model
{
    protected $fillable = [
        'product_id',
        'heading',
        'type',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(ProductPricingChartTier::class, 'product_pricing_chart_id')->orderBy('sort_order');
    }
}
