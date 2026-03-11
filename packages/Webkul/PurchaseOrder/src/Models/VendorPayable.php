<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\PurchaseOrder\Contracts\VendorPayable as VendorPayableContract;

class VendorPayable extends Model implements VendorPayableContract
{
    protected $fillable = ['purchase_order_id', 'goods_receipt_id', 'organization_id', 'payable_number', 'payable_date', 'total_amount', 'paid_amount', 'remaining_amount', 'status', 'notes'];

    protected $casts = ['payable_date' => 'date', 'total_amount' => 'decimal:4', 'paid_amount' => 'decimal:4', 'remaining_amount' => 'decimal:4'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->payable_number)) {
                $model->payable_number = static::generateNextNumber();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $last = static::orderByDesc('id')->first();
        $next = 1;
        if ($last && preg_match('/(\d+)$/', (string) $last->payable_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'VP-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass(), 'organization_id');
    }
}
