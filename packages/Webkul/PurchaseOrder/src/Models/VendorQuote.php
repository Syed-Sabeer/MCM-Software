<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Core\Traits\HasDocumentCharges;
use Webkul\PurchaseOrder\Contracts\VendorQuote as VendorQuoteContract;
use Webkul\User\Models\UserProxy;

class VendorQuote extends Model implements VendorQuoteContract
{
    use HasDocumentCharges;

    protected $fillable = [
        'vendor_quote_number',
        'job_order_id',
        'organization_id',
        'person_id',
        'billing_address',
        'shipping_address',
        'issue_date',
        'expected_response_date',
        'payment_term',
        'shipping_method',
        'first_delivery_date',
        'last_delivery_date',
        'status',
        'notes',
        'terms',
        'subtotal',
        'sales_tax_percent',
        'sales_tax_amount',
        'freight',
        'grand_total',
        'attachment_path',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'expected_response_date' => 'date',
        'first_delivery_date' => 'date',
        'last_delivery_date' => 'date',
        'subtotal' => 'decimal:4',
        'sales_tax_percent' => 'decimal:4',
        'sales_tax_amount' => 'decimal:4',
        'freight' => 'decimal:4',
        'grand_total' => 'decimal:4',
    ];

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
        $last = static::whereNotNull('vendor_quote_number')->orderByDesc('id')->first();

        if (! $last || ! preg_match('/(\d+)$/', (string) $last->vendor_quote_number, $matches, PREG_OFFSET_CAPTURE)) {
            return static::nextAvailableNumber('vendor_quote_number', 'VQ-', 1, 5);
        }

        $lastNumericPart = $matches[1][0];
        $paddingLength = max(strlen($lastNumericPart), 1);
        $numericOffset = $matches[1][1];
        $prefix = substr((string) $last->vendor_quote_number, 0, $numericOffset);
        $next = ((int) $lastNumericPart) + 1;

        return static::nextAvailableNumber('vendor_quote_number', $prefix, $next, $paddingLength);
    }

    protected static function nextAvailableNumber(string $column, string $prefix, int $next, int $paddingLength): string
    {
        do {
            $candidate = $prefix . str_pad((string) $next, $paddingLength, '0', STR_PAD_LEFT);
            $exists = static::where($column, $candidate)->exists();
            $next++;
        } while ($exists);

        return $candidate;
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
