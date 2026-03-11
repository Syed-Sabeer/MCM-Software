<?php

namespace Webkul\PurchaseOrder\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\GoodsReceiptItem;

class GoodsReceiptItemRepository extends Repository
{
    public function model(): string
    {
        return GoodsReceiptItem::class;
    }
}
