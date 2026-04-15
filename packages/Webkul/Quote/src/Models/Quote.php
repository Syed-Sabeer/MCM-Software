<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Core\Traits\HasDocumentCharges;
use Webkul\Lead\Models\LeadProxy;
use Webkul\Quote\Contracts\Quote as QuoteContract;
use Webkul\User\Models\UserProxy;

class Quote extends Model implements QuoteContract
{
    use CustomAttribute;
    use HasDocumentCharges;

    protected $table = 'quotes';

    protected $casts = [
        'billing_address'  => 'array',
        'shipping_address' => 'array',
        'quote_date'       => 'date',
        'expired_at'       => 'datetime',
        'etd'              => 'date',
        'eta'              => 'date',
        'tariff_percent'   => 'decimal:4',
        'freight_percent'  => 'decimal:4',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject',
        'description',
        'billing_address',
        'shipping_address',
        'discount_percent',
        'discount_amount',
        'tax_amount',
        'adjustment_amount',
        'sub_total',
        'grand_total',
        'quote_number',
        'quote_date',
        'status',
        'organization_id',
        'notes',
        'terms',
        'payment_term',
        'shipping_method',
        'production_time',
        'transit_time',
        'etd',
        'eta',
        'tariff_percent',
        'freight_percent',
        'attachment_path',
        'expired_at',
        'user_id',
        'person_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->quote_number)) {
                $quote->quote_number = static::generateNextQuoteNumber();
            }

            if (empty($quote->status)) {
                $quote->status = 'draft';
            }

            if (empty($quote->quote_date)) {
                $quote->quote_date = now()->toDateString();
            }
        });
    }

    public static function generateNextQuoteNumber(): string
    {
        $last = static::whereNotNull('quote_number')->orderByDesc('id')->first();

        if (! $last || ! preg_match('/(\\d+)$/', (string) $last->quote_number, $matches)) {
            return '000001';
        }

        $lastNumericPart = $matches[1];
        $paddingLength = max(strlen($lastNumericPart), 1);
        $next = ((int) $lastNumericPart) + 1;

        return str_pad((string) $next, $paddingLength, '0', STR_PAD_LEFT);
    }

    /**
     * Get the quote items record associated with the quote.
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItemProxy::modelClass());
    }

    /**
     * Get the user that owns the quote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the person that owns the quote.
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass(), 'organization_id');
    }

    public function customerOrganization(): BelongsTo
    {
        return $this->organization();
    }

    public function proformaInvoices(): HasMany
    {
        return $this->hasMany(ProformaInvoiceProxy::modelClass(), 'quote_id');
    }

    /**
     * The leads that belong to the quote.
     */
    public function leads()
    {
        return $this->belongsToMany(LeadProxy::modelClass(), 'lead_quotes');
    }
}
