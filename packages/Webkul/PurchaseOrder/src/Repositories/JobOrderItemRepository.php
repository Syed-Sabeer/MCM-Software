<?php

namespace Webkul\PurchaseOrder\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\JobOrderItem;

class JobOrderItemRepository extends Repository
{
    public function model(): string
    {
        return JobOrderItem::class;
    }
}
