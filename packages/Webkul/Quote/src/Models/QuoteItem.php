<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Quote\Contracts\QuoteItem as QuoteItemContract;

class QuoteItem extends Model implements QuoteItemContract
{
    protected $table = 'quote_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'item_code',
        'name',
        'item_name',
        'description',
        'quantity',
        'qty',
        'unit',
        'price',
        'unit_price',
        'coupon_code',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'total',
        'line_subtotal',
        'line_total',
        'sort_order',
        'product_id',
        'color_variant_id',
        'color_variant_name',
        'preview_image',
        'quote_id',
    ];

    protected $casts = [
        'quantity'         => 'decimal:4',
        'qty'              => 'decimal:4',
        'price'            => 'decimal:4',
        'unit_price'       => 'decimal:4',
        'discount_percent' => 'decimal:4',
        'discount_amount'  => 'decimal:4',
        'tax_percent'      => 'decimal:4',
        'tax_amount'       => 'decimal:4',
        'total'            => 'decimal:4',
        'line_subtotal'    => 'decimal:4',
        'line_total'       => 'decimal:4',
    ];

    /**
     * Get the quote record associated with the quote item.
     */
    public function quote()
    {
        return $this->belongsTo(QuoteProxy::modelClass());
    }
}
