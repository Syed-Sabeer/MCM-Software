<?php

namespace Webkul\PurchaseOrder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Contact\Models\PersonProxy;
use Webkul\PurchaseOrder\Contracts\PurchaseOrder as PurchaseOrderContract;
use Webkul\User\Models\UserProxy;

class PurchaseOrder extends Model implements PurchaseOrderContract
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_number',
        'job_number',
        'description',
        'notes',
        'completion_date',
        'last_delivery_date',
        'payment_term',
        'shipping_method',
        'sales_tax_percent',
        'freight',
        'sub_total',
        'tax_amount',
        'grand_total',
        'person_id',
        'user_id',
    ];

    protected $casts = [
        'sales_tax_percent'   => 'decimal:4',
        'freight'             => 'decimal:4',
        'sub_total'           => 'decimal:4',
        'tax_amount'          => 'decimal:4',
        'grand_total'         => 'decimal:4',
        'completion_date'     => 'date',
        'last_delivery_date'  => 'date',
    ];

    /**
     * Get the items for the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItemProxy::modelClass());
    }

    /**
     * Get the user (sales owner).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Get the person (contact).
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(PersonProxy::modelClass());
    }

    /**
     * Boot method to auto-generate PO number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            if (empty($purchaseOrder->po_number)) {
                $purchaseOrder->po_number = static::generateNextPoNumber();
            }
        });
    }

    /**
     * Generate the next sequential PO number.
     */
    public static function generateNextPoNumber(): string
    {
        $lastPo = static::orderByRaw('CAST(po_number AS UNSIGNED) DESC')->first();

        if ($lastPo && is_numeric($lastPo->po_number)) {
            $nextNumber = intval($lastPo->po_number) + 1;
        } else {
            $nextNumber = 1;
        }

        return str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
