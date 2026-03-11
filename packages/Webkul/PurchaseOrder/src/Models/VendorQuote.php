<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\PurchaseOrder\Contracts\VendorQuote as VendorQuoteContract;
use Webkul\User\Models\UserProxy;

class VendorQuote extends Model implements VendorQuoteContract
{
    protected $fillable = ['vendor_quote_number', 'job_order_id', 'organization_id', 'person_id', 'issue_date', 'expected_response_date', 'status', 'notes', 'attachment_path', 'created_by'];

    protected $casts = ['issue_date' => 'date', 'expected_response_date' => 'date'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->vendor_quote_number)) {
                $model->vendor_quote_number = static::generateNextNumber();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $last = static::orderByDesc('id')->first();
        $next = 1;
        if ($last && preg_match('/(\d+)$/', (string) $last->vendor_quote_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'VQ-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass(), 'organization_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass(), 'person_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VendorQuoteItem::class)->orderBy('sort_order');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
