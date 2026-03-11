<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\OrganizationProxy;
use Webkul\PurchaseOrder\Contracts\GoodsReceipt as GoodsReceiptContract;
use Webkul\User\Models\UserProxy;

class GoodsReceipt extends Model implements GoodsReceiptContract
{
    protected $fillable = ['goods_receipt_number', 'purchase_order_id', 'vendor_id', 'receipt_date', 'received_by', 'notes', 'attachment_path', 'status'];

    protected $casts = ['receipt_date' => 'date'];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->goods_receipt_number)) {
                $model->goods_receipt_number = static::generateNextNumber();
            }
        });
    }

    public static function generateNextNumber(): string
    {
        $last = static::orderByDesc('id')->first();
        $next = 1;
        if ($last && preg_match('/(\d+)$/', (string) $last->goods_receipt_number, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'GR-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(OrganizationProxy::modelClass(), 'vendor_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass(), 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
}
