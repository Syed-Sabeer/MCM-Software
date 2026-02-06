<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Contracts\Organization as OrganizationContract;
use Webkul\User\Models\UserProxy;
use Webkul\Contact\Models\OrganizationFile;

class Organization extends Model implements OrganizationContract
{
    use CustomAttribute;

    protected $casts = [
        'address' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'user_id',

        'parent_organization_id',

        'phone',
        'fax',
        'website',

        'type',
        'industry',
        'employees',
        'annual_revenue',
        'description',

        'billing_street',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country',

        'shipping_street',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
    ];

    /**
     * Get persons.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function persons()
    {
        return $this->hasMany(PersonProxy::modelClass());
    }

    /**
     * Get the user that owns the lead.
     */
    public function user()
    {
        return $this->belongsTo(UserProxy::modelClass());
    }

    /**
     * Files attached to this organization.
     */
    public function files()
    {
        return $this->hasMany(OrganizationFile::class);
    }
}
