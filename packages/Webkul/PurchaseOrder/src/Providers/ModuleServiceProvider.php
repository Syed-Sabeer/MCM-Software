<?php

namespace Webkul\PurchaseOrder\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\PurchaseOrder\Models\PurchaseOrder::class,
        \Webkul\PurchaseOrder\Models\PurchaseOrderItem::class,
    ];
}
