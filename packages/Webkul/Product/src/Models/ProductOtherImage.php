<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOtherImage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'path',
        'original_name',
        'sort_order',
        'color_id',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }

    /**
     * Get the color associated with this image.
     */
    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'color_id');
    }
}
