<?php

namespace Webkul\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Activity\Models\ActivityProxy;
use Webkul\Activity\Traits\LogsActivity;
use Webkul\Attribute\Traits\CustomAttribute;
use Webkul\Contact\Models\Organization;
use Webkul\Product\Contracts\Product as ProductContract;
use Webkul\Tag\Models\TagProxy;
use Webkul\Warehouse\Models\LocationProxy;
use Webkul\Warehouse\Models\WarehouseProxy;

class Product extends Model implements ProductContract
{
    use CustomAttribute, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'internal_code',
        'customer_organization_id',
        'description',
        'quantity',
        'price',
        'cost_price',
        'selling_price',
        'category_id',
        'style',
        'size',
        'cover_image',
        'additional_info',
        'shipping_info',
        'publish_on_website',
    ];

    /**
     * Cast publish_on_website to boolean.
     */
    protected $casts = [
        'publish_on_website' => 'boolean',
        'price'              => 'decimal:4',
        'cost_price'         => 'decimal:4',
        'selling_price'      => 'decimal:4',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Customer organization owning this product (optional).
     */
    public function customerOrganization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'customer_organization_id');
    }

    /**
     * Get the product other images.
     */
    public function otherImages(): HasMany
    {
        return $this->hasMany(ProductOtherImage::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Get the product colors.
     */
    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Get the product key points (key_heading + key_point pairs).
     */
    public function keyPoints(): HasMany
    {
        return $this->hasMany(ProductKeyPoint::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Get the product pricing charts (each with heading, type, and tiers).
     */
    public function pricingCharts(): HasMany
    {
        return $this->hasMany(ProductPricingChart::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Material consumption rows (BOM-like).
     */
    public function consumptions(): HasMany
    {
        return $this->hasMany(ProductConsumption::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Production sections for job card template.
     */
    public function productionSections(): HasMany
    {
        return $this->hasMany(ProductProductionSection::class, 'product_id')->orderBy('sort_order');
    }

    /**
     * Get the product warehouses that owns the product.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(WarehouseProxy::modelClass(), 'product_inventories');
    }

    /**
     * Get the product locations that owns the product.
     */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(LocationProxy::modelClass(), 'product_inventories', 'product_id', 'warehouse_location_id');
    }

    /**
     * Get the product inventories that owns the product.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(ProductInventoryProxy::modelClass());
    }

    /**
     * The tags that belong to the Products.
     */
    public function tags()
    {
        return $this->belongsToMany(TagProxy::modelClass(), 'product_tags');
    }

    /**
     * Get the activities.
     */
    public function activities()
    {
        return $this->belongsToMany(ActivityProxy::modelClass(), 'product_activities');
    }
}
