<?php

namespace Webkul\Core\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Webkul\Core\Models\DocumentCharge;

trait HasDocumentCharges
{
    public function additionalCharges(): MorphMany
    {
        return $this->morphMany(DocumentCharge::class, 'chargeable')->orderBy('sort_order');
    }
}
