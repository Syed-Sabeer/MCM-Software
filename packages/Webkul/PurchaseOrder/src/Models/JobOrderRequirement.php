<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\PurchaseOrder\Contracts\JobOrderRequirement as JobOrderRequirementContract;

class JobOrderRequirement extends Model implements JobOrderRequirementContract
{
    protected $fillable = [
        'job_order_id', 'job_order_item_id', 'product_id', 'material_name', 'unit', 'qty_per_unit', 'ordered_qty', 'required_qty', 'received_qty', 'balance_qty', 'status', 'sort_order',
    ];

    protected $casts = [
        'qty_per_unit' => 'decimal:4',
        'ordered_qty' => 'decimal:4',
        'required_qty' => 'decimal:4',
        'received_qty' => 'decimal:4',
        'balance_qty' => 'decimal:4',
    ];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }
}
