<?php

namespace Webkul\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'email', 'account_type', 'otp', 'otp_hash', 'attempts', 'expires_at', 'expires_at_epoch', 'verified_at',
    ];

    protected $hidden = ['otp_hash'];

    protected $casts = [
        'attempts'         => 'integer',
        'expires_at'       => 'datetime',
        'expires_at_epoch' => 'integer',
        'verified_at'      => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at_epoch
            ? time() >= $this->expires_at_epoch
            : $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null && ! $this->isExpired();
    }
}
