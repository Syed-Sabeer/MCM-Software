<?php

namespace Webkul\Quote\Repositories;

use Webkul\Core\Eloquent\Repository;

class ProformaReceiptRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Quote\Contracts\ProformaReceipt';
    }
}
