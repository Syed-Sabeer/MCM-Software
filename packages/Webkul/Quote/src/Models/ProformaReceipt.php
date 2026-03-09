<?php

namespace Webkul\Quote\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Quote\Contracts\ProformaReceipt as ProformaReceiptContract;
use Webkul\User\Models\UserProxy;

class ProformaReceipt extends Model implements ProformaReceiptContract
{
    protected $fillable = [
        'proforma_invoice_id',
        'receipt_number',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'notes',
        'received_by',
        'attachment_path',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:4',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->receipt_number)) {
                $model->receipt_number = static::generateNextReceiptNumber();
            }
        });
    }

    public static function generateNextReceiptNumber(): string
    {
        $last = static::orderBy('id', 'desc')->first();
        $next = $last ? $last->id + 1 : 1;

        return 'RCP-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function proformaInvoice(): BelongsTo
    {
        return $this->belongsTo(ProformaInvoiceProxy::modelClass(), 'proforma_invoice_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'received_by');
    }
}
