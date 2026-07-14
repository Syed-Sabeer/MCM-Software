<?php

namespace Webkul\Contact\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Activity\Models\ActivityProxy;
use Webkul\Admin\Models\CustomerPortalUser;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Contracts\Organization as OrganizationContract;
use Webkul\Product\Models\ProductProxy;
use Webkul\Quote\Models\ProformaInvoiceProxy;
use Webkul\Quote\Models\QuoteProxy;
use Webkul\User\Models\UserProxy;

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

    /**
     * Get the activities.
     */
    public function activities()
    {
        return $this->belongsToMany(ActivityProxy::modelClass(), 'organization_activities');
    }

    /**
     * Products where this organization is selected as customer owner.
     */
    public function productsAsCustomer()
    {
        return $this->hasMany(ProductProxy::modelClass(), 'customer_organization_id');
    }

    /**
     * Quotes for this organization as customer.
     */
    public function quotes()
    {
        return $this->hasMany(QuoteProxy::modelClass(), 'organization_id');
    }

    /**
     * Proforma invoices for this organization as customer.
     */
    public function proformaInvoices()
    {
        return $this->hasMany(ProformaInvoiceProxy::modelClass(), 'organization_id');
    }

    public function portalUsers()
    {
        return $this->hasMany(CustomerPortalUser::class);
    }
}
