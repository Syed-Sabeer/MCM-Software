<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Quote\Contracts\ProformaInvoiceItem as ProformaInvoiceItemContract;

class ProformaInvoiceItem extends Model implements ProformaInvoiceItemContract
{
    protected $fillable = [
        'proforma_invoice_id',
        'product_id',
        'color_variant_id',
        'color_variant_name',
        'preview_image',
        'item_name',
        'item_code',
        'description',
        'qty',
        'unit',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'tax_percent',
        'tax_amount',
        'line_subtotal',
        'line_total',
        'sort_order',
    ];

    protected $casts = [
        'qty'              => 'decimal:4',
        'unit_price'       => 'decimal:4',
        'discount_percent' => 'decimal:4',
        'discount_amount'  => 'decimal:4',
        'tax_percent'      => 'decimal:4',
        'tax_amount'       => 'decimal:4',
        'line_subtotal'    => 'decimal:4',
        'line_total'       => 'decimal:4',
    ];

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoiceProxy::modelClass(), 'proforma_invoice_id');
    }
}
