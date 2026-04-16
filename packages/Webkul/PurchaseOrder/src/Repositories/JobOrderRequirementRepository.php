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
            $aggregatedRequirements = [];

            foreach ($jobOrder->items as $jobOrderItem) {
                if (! $jobOrderItem->product_id) {
                    continue;
                }

                $product = Product::with('consumptions')->find($jobOrderItem->product_id);

                foreach ($product?->consumptions ?? [] as $consumption) {
                    $orderedQty = (float) $jobOrderItem->qty;
                    $qtyPerUnit = (float) $consumption->qty;
                    $requiredQty = $qtyPerUnit * $orderedQty;
                    $displayName = $this->formatMaterialName(
                        (string) $consumption->name,
                        $consumption->color_name,
                        $consumption->color_code
                    );
                    $key = mb_strtolower(trim($displayName)) . '|' . mb_strtolower((string) $consumption->unit);

                    if (! isset($aggregatedRequirements[$key])) {
                        $aggregatedRequirements[$key] = [
                            'job_order_id' => $jobOrder->id,
                            'job_order_item_id' => $jobOrderItem->id,
                            'product_id' => $jobOrderItem->product_id,
                            'item_codes' => [],
                            'material_reference_id' => $consumption->material_reference_id,
                            'material_name' => $displayName,
                            'unit' => $consumption->unit,
                            'qty_per_unit' => 0,
                            'ordered_qty' => 0,
                            'required_qty' => 0,
                            'received_qty' => 0,
                            'balance_qty' => 0,
                            'vendor_ids' => [],
                            'color_name' => $consumption->color_name,
                            'color_code' => $consumption->color_code,
                            'status' => 'pending',
                        ];
                    }

                    $aggregatedRequirements[$key]['qty_per_unit'] += $qtyPerUnit;
                    $aggregatedRequirements[$key]['ordered_qty'] += $orderedQty;
                    $aggregatedRequirements[$key]['required_qty'] += $requiredQty;
                    $aggregatedRequirements[$key]['balance_qty'] += $requiredQty;
                    $aggregatedRequirements[$key]['item_codes'][] = (string) ($jobOrderItem->display_code ?: $jobOrderItem->item_code ?: '');
                    $aggregatedRequirements[$key]['vendor_ids'] = array_values(array_unique(array_merge(
                        $aggregatedRequirements[$key]['vendor_ids'],
                        array_map('intval', (array) ($consumption->vendor_ids ?? []))
                    )));
                }
            }

            foreach ($aggregatedRequirements as $requirement) {
                $this->create([
                    'job_order_id' => $requirement['job_order_id'],
                    'job_order_item_id' => $requirement['job_order_item_id'],
                    'product_id' => $requirement['product_id'],
                    'item_codes' => collect($requirement['item_codes'])->filter()->unique()->implode(', '),
                    'material_reference_id' => $requirement['material_reference_id'],
                    'material_name' => $requirement['material_name'],
                    'unit' => $requirement['unit'],
                    'qty_per_unit' => $requirement['qty_per_unit'],
                    'ordered_qty' => $requirement['ordered_qty'],
                    'required_qty' => $requirement['required_qty'],
                    'received_qty' => 0,
                    'balance_qty' => $requirement['balance_qty'],
                    'vendor_ids' => $requirement['vendor_ids'] ?: null,
                    'color_name' => $requirement['color_name'],
                    'color_code' => $requirement['color_code'],
                    'status' => 'pending',
                    'sort_order' => $sortOrder++,
                ]);
            }
        });
    }

    protected function formatMaterialName(string $name, ?string $colorName = null, ?string $colorCode = null): string
    {
        $name = trim($name);
        $label = trim((string) ($colorName ?: $colorCode ?: ''));

        if ($label === '') {
            return $name;
        }

        return sprintf('%s (%s)', $name, $label);
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
