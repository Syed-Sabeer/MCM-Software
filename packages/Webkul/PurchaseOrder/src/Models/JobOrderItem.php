<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Contracts\JobOrderItem as JobOrderItemContract;
use Webkul\Quote\Models\ProformaInvoiceItem;

class JobOrderItem extends Model implements JobOrderItemContract
{
    protected $fillable = [
        'job_order_id', 'proforma_invoice_item_id', 'product_id', 'item_name', 'item_code', 'description', 'qty', 'unit', 'unit_price', 'line_total', 'sort_order',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:3',
        'line_total' => 'decimal:3',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function proformaInvoiceItem(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoiceItem::class, 'proforma_invoice_item_id');
    }

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class);
    }

    public function getDisplayCodeAttribute(): string
    {
        return (string) ($this->item_code ?: $this->product?->sku ?: $this->product?->internal_code ?: '-');
    }

    public function getDisplayNameAttribute(): string
    {
        return (string) ($this->display_code !== '-' ? $this->display_code : ($this->item_name ?: $this->product?->name ?: '-'));
    }

    public function getColorVariantNameAttribute(): ?string
    {
        $colorName = trim((string) ($this->proformaInvoiceItem?->color_variant_name ?: ''));

        return $colorName !== '' ? $colorName : null;
    }
}

