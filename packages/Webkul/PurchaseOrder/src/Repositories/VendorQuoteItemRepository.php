<?php

namespace Webkul\PurchaseOrder\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\VendorQuoteItem;

class VendorQuoteItemRepository extends Repository
{
    public function model(): string
    {
        return VendorQuoteItem::class;
    }
}
