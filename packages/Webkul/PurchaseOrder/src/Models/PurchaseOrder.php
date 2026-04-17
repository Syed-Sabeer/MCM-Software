<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\Contact\Models\PersonProxy;
use Webkul\Core\Traits\HasDocumentCharges;
use Webkul\User\Models\UserProxy;

class PurchaseOrder extends Model implements \Webkul\PurchaseOrder\Contracts\PurchaseOrder
{
    use HasDocumentCharges;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_number', 'job_number', 'job_order_id', 'vendor_quote_id', 'description', 'notes', 'terms', 'attachment_path',
        'completion_date', 'last_delivery_date', 'expected_receive_date', 'status', 'payment_term', 'shipping_method',
        'sales_tax_percent', 'freight', 'sub_total', 'tax_amount', 'grand_total', 'person_id', 'organization_id', 'billing_address', 'shipping_address', 'user_id', 'closed_at', 'closed_by',
    ];

    protected $casts = [
        'sales_tax_percent' => 'decimal:4',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'freight' => 'decimal:4',
        'sub_total' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'grand_total' => 'decimal:4',
        'completion_date' => 'date',
        'last_delivery_date' => 'date',
        'expected_receive_date' => 'date',
        'closed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            if (empty($purchaseOrder->po_number)) {
                $purchaseOrder->po_number = static::generateNextPoNumber();
            }

            if (empty($purchaseOrder->status)) {
                $purchaseOrder->status = 'draft';
            }
        });
    }

    public static function generateNextPoNumber(): string
    {
        $lastPo = static::whereNotNull('po_number')->orderByDesc('id')->first();

        if (! $lastPo || ! preg_match('/(\d+)$/', (string) $lastPo->po_number, $matches, PREG_OFFSET_CAPTURE)) {
            return static::nextAvailableNumber('po_number', 'PO-', 1, 5);
        }

        $lastNumericPart = $matches[1][0];
        $paddingLength = max(strlen($lastNumericPart), 1);
        $numericOffset = $matches[1][1];
        $prefix = substr((string) $lastPo->po_number, 0, $numericOffset);
        $next = ((int) $lastNumericPart) + 1;

        return static::nextAvailableNumber('po_number', $prefix, $next, $paddingLength);
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

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItemProxy::modelClass());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass());
    }

    public function jobOrder(): BelongsTo
    {
        return $this->belongsTo(JobOrder::class);
    }

    public function vendorQuote(): BelongsTo
    {
        return $this->belongsTo(VendorQuote::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class)->orderByDesc('receipt_date');
    }

    public function payables(): HasMany
    {
        return $this->hasMany(VendorPayable::class)->orderByDesc('payable_date');
    }
}
