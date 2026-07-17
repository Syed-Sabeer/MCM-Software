<?php

namespace Webkul\Quote\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\Quote\Models\Quote::class,
        \Webkul\Quote\Models\QuoteItem::class,
        \Webkul\Quote\Models\ProformaInvoice::class,
        \Webkul\Quote\Models\ProformaInvoiceItem::class,
        \Webkul\Quote\Models\ProformaReceipt::class,
        \Webkul\Quote\Models\Invoice::class,
        \Webkul\Quote\Models\InvoiceItem::class,
        \Webkul\Quote\Models\InvoiceReceipt::class,
    ];
}
