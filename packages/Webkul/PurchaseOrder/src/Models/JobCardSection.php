<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\PurchaseOrder\Contracts\JobCardSection as JobCardSectionContract;

class JobCardSection extends Model implements JobCardSectionContract
{
    protected $fillable = ['job_card_id', 'source_product_section_id', 'section_name', 'sort_order', 'status'];

    public function jobCard(): BelongsTo
    {
        return $this->belongsTo(JobCard::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(JobCardSectionItem::class)->orderBy('sort_order');
    }
}
