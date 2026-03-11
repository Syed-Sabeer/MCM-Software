<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\PurchaseOrder\Contracts\JobCard as JobCardContract;

class JobCard extends Model implements JobCardContract
{
    protected $fillable = ['job_order_id', 'job_order_item_id', 'product_id', 'title', 'status', 'remarks', 'created_by'];

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function jobOrderItem(): BelongsTo
    {
        return $this->belongsTo(JobOrderItem::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(JobCardSection::class)->orderBy('sort_order');
    }
}
