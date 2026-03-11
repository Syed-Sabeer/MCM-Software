<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\PurchaseOrder\Contracts\JobCardSectionItem as JobCardSectionItemContract;

class JobCardSectionItem extends Model implements JobCardSectionItemContract
{
    protected $fillable = ['job_card_section_id', 'source_product_section_item_id', 'name', 'qty', 'unit', 'sort_order'];

    protected $casts = ['qty' => 'decimal:4'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(JobCardSection::class, 'job_card_section_id');
    }
}
