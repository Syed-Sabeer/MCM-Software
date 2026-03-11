<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\PurchaseOrder\Contracts\VendorQuoteItem as VendorQuoteItemContract;

class VendorQuoteItem extends Model implements VendorQuoteItemContract
{
    protected $fillable = ['vendor_quote_id', 'requirement_id', 'material_name', 'description', 'quantity', 'unit', 'unit_price', 'total', 'vendor_lead_time', 'expected_receive_date', 'sort_order'];

    protected $casts = ['quantity' => 'decimal:4', 'unit_price' => 'decimal:4', 'total' => 'decimal:4', 'expected_receive_date' => 'date'];

    public function vendorQuote(): BelongsTo
    {
        return $this->belongsTo(VendorQuote::class);
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(JobOrderRequirement::class, 'requirement_id');
    }
}
