<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Quote\Contracts\InvoiceItem as InvoiceItemContract;

class InvoiceItem extends Model implements InvoiceItemContract
{
    protected $fillable = [
        'invoice_id', 'product_id', 'color_variant_id', 'color_variant_name', 'preview_image',
        'item_name', 'item_code', 'description', 'qty', 'unit', 'unit_price', 'line_total', 'sort_order',
    ];

    protected $casts = ['qty' => 'decimal:4', 'unit_price' => 'decimal:4', 'line_total' => 'decimal:4'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceProxy::modelClass());
    }
}
