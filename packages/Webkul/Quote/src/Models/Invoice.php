<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Core\Traits\HasDocumentCharges;
use Webkul\Quote\Contracts\Invoice as InvoiceContract;
use Webkul\User\Models\UserProxy;

class Invoice extends Model implements InvoiceContract
{
    use HasDocumentCharges;

    protected $fillable = [
        'invoice_number', 'proforma_invoice_id', 'quote_id', 'organization_id', 'person_id',
        'sales_owner_id', 'subject', 'issue_date', 'due_date', 'billing_address', 'shipping_address',
        'subtotal', 'tax_amount', 'adjustment_amount', 'grand_total', 'advance_applied',
        'received_amount', 'remaining_amount', 'status', 'payment_term', 'customer_po_reference',
        'notes', 'terms', 'attachment_path', 'created_by', 'customer_visible_at',
    ];

    protected $casts = [
        'issue_date'          => 'date',
        'due_date'            => 'date',
        'billing_address'     => 'array',
        'shipping_address'    => 'array',
        'subtotal'            => 'decimal:4',
        'tax_amount'          => 'decimal:4',
        'adjustment_amount'   => 'decimal:4',
        'grand_total'         => 'decimal:4',
        'advance_applied'     => 'decimal:4',
        'received_amount'     => 'decimal:4',
        'remaining_amount'    => 'decimal:4',
        'customer_visible_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateNextNumber();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $last = static::latest('id')->value('invoice_number');
        $next = preg_match('/(\d+)$/', (string) $last, $matches) ? ((int) $matches[1]) + 1 : 1;

        do {
            $number = 'INV-'.str_pad((string) $next++, 5, '0', STR_PAD_LEFT);
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }

    public function scopeVisibleToCustomer($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId)
            ->whereNotNull('customer_visible_at')
            ->where('status', '!=', 'draft');
    }

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoiceProxy::modelClass());
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(QuoteProxy::modelClass());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }

    public function salesOwner(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'sales_owner_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItemProxy::modelClass())->orderBy('sort_order');
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(InvoiceReceiptProxy::modelClass())->latest('payment_date')->latest('id');
    }
}
