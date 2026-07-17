<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Quote\Contracts\InvoiceReceipt as InvoiceReceiptContract;
use Webkul\User\Models\UserProxy;

class InvoiceReceipt extends Model implements InvoiceReceiptContract
{
    protected $fillable = [
        'invoice_id', 'receipt_number', 'payment_date', 'amount', 'payment_method',
        'reference_no', 'notes', 'received_by', 'attachment_path',
    ];

    protected $casts = ['payment_date' => 'date', 'amount' => 'decimal:4'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (InvoiceReceipt $receipt) {
            if (empty($receipt->receipt_number)) {
                $receipt->receipt_number = static::generateNextNumber();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $next = ((int) static::max('id')) + 1;

        do {
            $number = 'INV-RCP-'.str_pad((string) $next++, 6, '0', STR_PAD_LEFT);
        } while (static::where('receipt_number', $number)->exists());

        return $number;
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceProxy::modelClass());
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'received_by');
    }
}
