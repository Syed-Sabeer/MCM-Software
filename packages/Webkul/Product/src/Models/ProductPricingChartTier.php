<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPricingChartTier extends Model
{
    protected $fillable = [
        'product_pricing_chart_id',
        'quantity',
        'price',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'price'    => 'decimal:4',
    ];

    public function pricingChart(): BelongsTo
    {
        return $this->belongsTo(ProductPricingChart::class, 'product_pricing_chart_id');
    }
}
