<?php

namespace Webkul\Quote\Repositories;

use Webkul\Core\Eloquent\Repository;

class ProformaInvoiceItemRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Quote\Contracts\ProformaInvoiceItem';
    }
}
