<?php

namespace Webkul\PurchaseOrder\Repositories;

use Webkul\Core\Eloquent\Repository;

class PurchaseOrderItemRepository extends Repository
{
    /**
     * Specify Model class name.
     */
    public function model(): string
    {
        return 'Webkul\PurchaseOrder\Contracts\PurchaseOrderItem';
    }
}
