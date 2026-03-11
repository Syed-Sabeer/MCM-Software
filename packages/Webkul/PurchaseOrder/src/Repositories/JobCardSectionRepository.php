<?php

namespace Webkul\PurchaseOrder\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\PurchaseOrder\Models\JobCardSection;

class JobCardSectionRepository extends Repository
{
    public function model(): string
    {
        return JobCardSection::class;
    }
}
