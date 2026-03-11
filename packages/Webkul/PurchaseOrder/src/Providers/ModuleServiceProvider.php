<?php

namespace Webkul\PurchaseOrder\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\PurchaseOrder\Models\PurchaseOrder::class,
        \Webkul\PurchaseOrder\Models\PurchaseOrderItem::class,
        \Webkul\PurchaseOrder\Models\JobOrder::class,
        \Webkul\PurchaseOrder\Models\JobOrderItem::class,
        \Webkul\PurchaseOrder\Models\JobCard::class,
        \Webkul\PurchaseOrder\Models\JobCardSection::class,
        \Webkul\PurchaseOrder\Models\JobCardSectionItem::class,
        \Webkul\PurchaseOrder\Models\JobOrderRequirement::class,
        \Webkul\PurchaseOrder\Models\VendorQuote::class,
        \Webkul\PurchaseOrder\Models\VendorQuoteItem::class,
        \Webkul\PurchaseOrder\Models\GoodsReceipt::class,
        \Webkul\PurchaseOrder\Models\GoodsReceiptItem::class,
        \Webkul\PurchaseOrder\Models\VendorPayable::class,
    ];
}
