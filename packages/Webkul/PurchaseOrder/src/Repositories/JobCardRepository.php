<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\JobCard;
use Webkul\PurchaseOrder\Models\JobOrder;

class JobCardRepository extends Repository
{
    public function __construct(
        protected JobCardSectionRepository $jobCardSectionRepository,
        Container $container
    ) {
        parent::__construct($container);
    }

    public function model(): string
    {
        return JobCard::class;
    }

    public function regenerateForJobOrder(JobOrder $jobOrder): void
    {
        DB::transaction(function () use ($jobOrder) {
            $jobOrder->jobCards()->delete();

            $jobOrder->loadMissing('items');

            foreach ($jobOrder->items as $jobOrderItem) {
                $jobCard = $this->create([
                    'job_order_id' => $jobOrder->id,
                    'job_order_item_id' => $jobOrderItem->id,
                    'product_id' => $jobOrderItem->product_id,
                    'title' => trim(($jobOrder->job_order_number ?: 'JO') . ' - ' . $jobOrderItem->display_name),
                    'status' => 'open',
                    'created_by' => auth()->id(),
                ]);

                if (! $jobOrderItem->product_id) {
                    continue;
                }

                $product = Product::with('productionSections.items')->find($jobOrderItem->product_id);

                foreach ($product?->productionSections ?? [] as $sectionIndex => $section) {
                    $jobCardSection = $this->jobCardSectionRepository->create([
                        'job_card_id' => $jobCard->id,
                        'source_product_section_id' => $section->id,
                        'section_name' => $section->section_name,
                        'sort_order' => $sectionIndex,
                        'status' => 'not_started',
                    ]);

                    foreach ($section->items as $itemIndex => $item) {
                        $jobCardSection->items()->create([
                            'source_product_section_item_id' => $item->id,
                            'name' => $item->name,
                            'qty' => $item->qty,
                            'unit' => $item->unit,
                            'sort_order' => $itemIndex,
                        ]);
                    }
                }
            }
        });
    }
}
