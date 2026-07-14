<?php

namespace Webkul\Admin\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Webkul\Admin\Notifications\CustomerPortalResetPassword;
use Webkul\Contact\Models\Organization;
use Webkul\Contact\Models\Person;

class CustomerPortalUser extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, HasFactory, Notifiable;

    protected $fillable = [
        'organization_id', 'person_id', 'name', 'email', 'password', 'status', 'role',
        'permissions', 'email_verified_at', 'invited_at', 'last_login_at', 'must_change_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
        'invited_at'           => 'datetime',
        'last_login_at'        => 'datetime',
        'must_change_password' => 'boolean',
    ];

    public static function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }

    public function setEmailAttribute(string $email): void
    {
        $this->attributes['email'] = static::normalizeEmail($email);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function invitations()
    {
        return $this->hasMany(CustomerPortalInvitation::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasPortalPermission(string $permission): bool
    {
        return $this->role === 'organization_admin'
            || in_array($permission, $this->permissions ?? [], true);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomerPortalResetPassword($token));
    }
}
