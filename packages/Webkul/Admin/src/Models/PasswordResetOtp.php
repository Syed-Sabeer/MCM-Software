<?php

namespace Webkul\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    protected $fillable = [
        'email', 'account_type', 'otp', 'otp_hash', 'attempts', 'expires_at', 'verified_at',
    ];

    protected $hidden = ['otp_hash'];

    protected $casts = [
        'attempts'    => 'integer',
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null && ! $this->isExpired();
    }
}
