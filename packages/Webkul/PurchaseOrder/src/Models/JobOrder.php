<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\PurchaseOrder\Contracts\JobOrder as JobOrderContract;
use Webkul\Quote\Models\ProformaInvoiceProxy;
use Webkul\User\Models\UserProxy;

class JobOrder extends Model implements JobOrderContract
{
    protected $fillable = [
        'job_order_number', 'proforma_invoice_id', 'organization_id', 'person_id', 'customer_po_reference', 'subject',
        'issue_date', 'required_delivery_date', 'status', 'total_order_qty', 'remarks', 'created_by', 'approved_by', 'approved_at',
        'customer_visible_at',
    ];

    protected $casts = [
        'issue_date'             => 'date',
        'required_delivery_date' => 'date',
        'approved_at'            => 'datetime',
        'total_order_qty'        => 'decimal:4',
        'customer_visible_at'    => 'datetime',
    ];

    public function scopeVisibleToCustomer($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId)
            ->whereNotNull('customer_visible_at')
            ->where('status', '!=', 'draft');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->job_order_number)) {
                $model->job_order_number = static::generateNextNumber();
            }

            if (empty($model->status)) {
                $model->status = 'open';
            }

            if (empty($model->issue_date)) {
                $model->issue_date = now()->toDateString();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $last = static::orderByDesc('id')->first();
        $next = 1;

        if ($last && preg_match('/(\d+)$/', (string) $last->job_order_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'JO-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoiceProxy::modelClass(), 'proforma_invoice_id');
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
        return $this->hasMany(JobOrderItem::class)->orderBy('sort_order');
    }

    public function jobCards(): HasMany
    {
        return $this->hasMany(JobCard::class)->orderBy('id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(JobOrderRequirement::class)->orderBy('sort_order');
    }

    public function vendorQuotes(): HasMany
    {
        return $this->hasMany(VendorQuote::class)->orderByDesc('issue_date');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class)->orderByDesc('created_at');
    }
}
