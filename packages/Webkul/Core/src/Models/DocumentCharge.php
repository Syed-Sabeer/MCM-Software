<?php

namespace Webkul\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentCharge extends Model
{
    protected $table = 'document_charges';

    protected $fillable = [
        'name',
        'type',
        'value',
        'amount',
        'sort_order',
    ];

    protected $casts = [
        'value'      => 'decimal:4',
        'amount'     => 'decimal:4',
        'sort_order' => 'integer',
    ];

    public function chargeable(): MorphTo
    {
        return $this->morphTo();
    }
}
