<?php

namespace Webkul\PurchaseOrder\Repositories;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Webkul\Core\Eloquent\Repository;
use Webkul\Product\Models\Product;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\JobOrderRequirement;

class JobOrderRequirementRepository extends Repository
{
    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    public function model(): string
    {
        return JobOrderRequirement::class;
    }

    public function regenerateForJobOrder(JobOrder $jobOrder): void
    {
        DB::transaction(function () use ($jobOrder) {
            $jobOrder->requirements()->delete();
            $jobOrder->loadMissing('items');

            $sortOrder = 0;

            foreach ($jobOrder->items as $jobOrderItem) {
                if (! $jobOrderItem->product_id) {
                    continue;
                }

                $product = Product::with('consumptions')->find($jobOrderItem->product_id);

                foreach ($product?->consumptions ?? [] as $consumption) {
                    $orderedQty = (float) $jobOrderItem->qty;
                    $qtyPerUnit = (float) $consumption->qty;
                    $requiredQty = $qtyPerUnit * $orderedQty;

                    $this->create([
                        'job_order_id' => $jobOrder->id,
                        'job_order_item_id' => $jobOrderItem->id,
                        'product_id' => $jobOrderItem->product_id,
                        'material_name' => $consumption->name,
                        'unit' => $consumption->unit,
                        'qty_per_unit' => $qtyPerUnit,
                        'ordered_qty' => $orderedQty,
                        'required_qty' => $requiredQty,
                        'received_qty' => 0,
                        'balance_qty' => $requiredQty,
                        'status' => 'pending',
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        });
    }

    public function applyReceivedQuantity(int $requirementId, float $receivedQty): void
    {
        $requirement = $this->findOrFail($requirementId);
        $newReceived = min((float) $requirement->required_qty, (float) $requirement->received_qty + $receivedQty);
        $balance = max((float) $requirement->required_qty - $newReceived, 0);

        $status = $balance <= 0 ? 'fulfilled' : ($newReceived > 0 ? 'partial' : 'pending');

        $this->update([
            'received_qty' => $newReceived,
            'balance_qty' => $balance,
            'status' => $status,
        ], $requirementId);
    }
}
