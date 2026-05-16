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
            $existingByKey = $jobOrder->requirements()
                ->get()
                ->mapWithKeys(function ($requirement) {
                    $key = implode('|', [
                        (string) ($requirement->material_reference_id ?: ''),
                        mb_strtolower(trim((string) ($requirement->material_name ?: ''))),
                        mb_strtolower(trim((string) ($requirement->color_name ?: ''))),
                        mb_strtolower(trim((string) ($requirement->color_code ?: ''))),
                        mb_strtolower(trim((string) ($requirement->unit ?: ''))),
                        number_format((float) $requirement->qty_per_unit, 4, '.', ''),
                    ]);

                    return [$key => (float) $requirement->received_qty];
                });

            $jobOrder->requirements()->delete();
            $jobOrder->loadMissing('items.proformaInvoiceItem');

            $sortOrder = 0;
            $aggregatedRequirements = [];

            foreach ($jobOrder->items as $jobOrderItem) {
                if (! $jobOrderItem->product_id) {
                    continue;
                }

                $product = Product::with('consumptions')->find($jobOrderItem->product_id);
                $itemColorName = trim((string) ($jobOrderItem->color_variant_name ?: ''));

                foreach ($product?->consumptions ?? [] as $consumption) {
                    $orderedQty = (float) $jobOrderItem->qty;
                    $qtyPerUnit = (float) $consumption->qty;
                    $requiredQty = $qtyPerUnit * $orderedQty;
                    $materialName = trim((string) $consumption->name);
                    $colorName = trim((string) ($consumption->color_name ?: ''));
                    if ($colorName === '' && $itemColorName !== '') {
                        $colorName = $itemColorName;
                    }
                    $colorCode = trim((string) ($consumption->color_code ?: ''));
                    $key = implode('|', [
                        (string) ($consumption->material_reference_id ?: ''),
                        mb_strtolower($materialName),
                        mb_strtolower($colorName),
                        mb_strtolower($colorCode),
                        mb_strtolower((string) $consumption->unit),
                        number_format($qtyPerUnit, 4, '.', ''),
                    ]);

                    if (! isset($aggregatedRequirements[$key])) {
                        $existingReceivedQty = (float) ($existingByKey[$key] ?? 0);

                        $aggregatedRequirements[$key] = [
                            'job_order_id' => $jobOrder->id,
                            'job_order_item_id' => $jobOrderItem->id,
                            'product_id' => $jobOrderItem->product_id,
                            'item_codes' => [],
                            'material_reference_id' => $consumption->material_reference_id,
                            'material_name' => $materialName,
                            'unit' => $consumption->unit,
                            'qty_per_unit' => $qtyPerUnit,
                            'ordered_qty' => 0,
                            'required_qty' => 0,
                            'received_qty' => $existingReceivedQty,
                            'balance_qty' => 0,
                            'vendor_ids' => [],
                            'color_name' => $colorName !== '' ? $colorName : null,
                            'color_code' => $colorCode !== '' ? $colorCode : null,
                            'status' => 'pending',
                        ];
                    }

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
                $requiredQty = (float) $requirement['required_qty'];
                $receivedQty = min((float) $requirement['received_qty'], $requiredQty);
                $balanceQty = max($requiredQty - $receivedQty, 0);
                $status = $balanceQty <= 0
                    ? 'fulfilled'
                    : ($receivedQty > 0 ? 'partial' : 'pending');

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
                    'required_qty' => $requiredQty,
                    'received_qty' => $receivedQty,
                    'balance_qty' => $balanceQty,
                    'vendor_ids' => $requirement['vendor_ids'] ?: null,
                    'color_name' => $requirement['color_name'],
                    'color_code' => $requirement['color_code'],
                    'status' => $status,
                    'sort_order' => $sortOrder++,
                ]);
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
