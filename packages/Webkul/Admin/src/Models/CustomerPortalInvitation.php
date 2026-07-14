<?php

namespace Webkul\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPortalInvitation extends Model
{
    protected $fillable = ['customer_portal_user_id', 'token_hash', 'expires_at', 'used_at'];

    protected $casts = ['expires_at' => 'datetime', 'used_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(CustomerPortalUser::class, 'customer_portal_user_id');
    }

    public function isUsable(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }
}
