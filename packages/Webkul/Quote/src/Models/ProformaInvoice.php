<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Core\Traits\HasDocumentCharges;
use Webkul\Quote\Contracts\ProformaInvoice as ProformaInvoiceContract;
use Webkul\User\Models\UserProxy;

class ProformaInvoice extends Model implements ProformaInvoiceContract
{
    use HasDocumentCharges;

    protected $fillable = [
        'proforma_number',
        'quote_id',
        'organization_id',
        'person_id',
        'sales_owner_id',
        'subject',
        'issue_date',
        'due_date',
        'billing_address',
        'shipping_address',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'tax_amount',
        'adjustment_amount',
        'grand_total',
        'received_amount',
        'remaining_amount',
        'status',
        'notes',
        'terms',
        'payment_term',
        'customer_po_reference',
        'source_type',
        'converted_to_invoice_id',
        'created_by',
        'approved_by',
        'approved_at',
        'attachment_path',
    ];

    protected $casts = [
        'billing_address'    => 'array',
        'shipping_address'   => 'array',
        'issue_date'         => 'date',
        'due_date'           => 'date',
        'approved_at'        => 'datetime',
        'subtotal'           => 'decimal:4',
        'discount_percent'   => 'decimal:4',
        'discount_amount'    => 'decimal:4',
        'tax_amount'         => 'decimal:4',
        'adjustment_amount'  => 'decimal:4',
        'grand_total'        => 'decimal:4',
        'received_amount'    => 'decimal:4',
        'remaining_amount'   => 'decimal:4',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->proforma_number)) {
                $model->proforma_number = static::generateNextProformaNumber();
            }
        });
    }

    public static function generateNextProformaNumber(): string
    {
        $last = static::whereNotNull('proforma_number')->orderByDesc('id')->first();

        if (! $last || ! preg_match('/(\d+)$/', (string) $last->proforma_number, $matches, PREG_OFFSET_CAPTURE)) {
            return static::nextAvailableNumber('proforma_number', 'PF-', 1, 5);
        }

        $lastNumericPart = $matches[1][0];
        $paddingLength = max(strlen($lastNumericPart), 1);
        $numericOffset = $matches[1][1];
        $prefix = substr((string) $last->proforma_number, 0, $numericOffset);
        $next = ((int) $lastNumericPart) + 1;

        return static::nextAvailableNumber('proforma_number', $prefix, $next, $paddingLength);
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

    public function quote(): BelongsTo
    {
        return $this->belongsTo(QuoteProxy::modelClass(), 'quote_id');
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass(), 'organization_id');
    }

    public function customerOrganization(): BelongsTo
    {
        return $this->organization();
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass(), 'person_id');
    }

    public function salesOwner(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'sales_owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProformaInvoiceItemProxy::modelClass(), 'proforma_invoice_id')->orderBy('sort_order');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(ProformaReceiptProxy::modelClass(), 'proforma_invoice_id')->orderByDesc('payment_date');
    }
}
