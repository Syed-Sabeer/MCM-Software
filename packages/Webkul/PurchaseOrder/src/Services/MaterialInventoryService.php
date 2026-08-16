<?php

namespace Webkul\PurchaseOrder\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Product\Models\MaterialReference;
use Webkul\Product\Services\UnitConversionService;
use Webkul\PurchaseOrder\Models\GoodsReceipt;
use Webkul\PurchaseOrder\Models\JobOrder;
use Webkul\PurchaseOrder\Models\JobOrderRequirement;
use Webkul\PurchaseOrder\Models\MaterialInventory;
use Webkul\PurchaseOrder\Models\MaterialInventoryTransaction;

class MaterialInventoryService
{
    public const MANUAL_TYPES = [
        'stock_in'    => 'Stock In',
        'stock_out'   => 'Stock Out',
        'set_balance' => 'Set Balance',
    ];

    public function __construct(protected UnitConversionService $unitConversionService)
    {
    }

    public function recordManual(
        int $materialReferenceId,
        string $type,
        float $quantity,
        ?float $unitCost = null,
        ?string $notes = null,
        $occurredAt = null,
        ?int $createdBy = null
    ): MaterialInventoryTransaction {
        if (! array_key_exists($type, self::MANUAL_TYPES)) {
            throw ValidationException::withMessages(['type' => 'Select a valid inventory movement type.']);
        }

        return DB::transaction(function () use ($materialReferenceId, $type, $quantity, $unitCost, $notes, $occurredAt, $createdBy) {
            $inventory = $this->lockedInventory($materialReferenceId);

            if ($type === 'set_balance') {
                if ($unitCost === null) {
                    throw ValidationException::withMessages(['unit_cost' => 'Enter the new average unit cost.']);
                }

                return $this->setBalance(
                    $inventory,
                    max($quantity, 0),
                    max($unitCost, 0),
                    $notes,
                    $occurredAt,
                    $createdBy
                );
            }

            $movementQuantity = match ($type) {
                'stock_in'  => abs($quantity),
                'stock_out' => -min(abs($quantity), max((float) $inventory->on_hand, 0)),
                default     => 0,
            };

            if (abs($movementQuantity) < 0.00005) {
                throw ValidationException::withMessages(['quantity' => 'The new quantity is the same as the current stock balance.']);
            }

            return $this->postMovement(
                $inventory,
                $type,
                $movementQuantity,
                $unitCost,
                'manual',
                null,
                null,
                $notes,
                $occurredAt,
                $createdBy
            );
        });
    }

    protected function setBalance(
        MaterialInventory $inventory,
        float $newQuantity,
        float $newUnitCost,
        ?string $notes,
        $occurredAt,
        ?int $createdBy
    ): MaterialInventoryTransaction {
        $oldQuantity = (float) $inventory->on_hand;
        $oldUnitCost = (float) $inventory->average_unit_cost;
        $quantityDifference = $newQuantity - $oldQuantity;
        $valueDifference = ($newQuantity * $newUnitCost) - ($oldQuantity * $oldUnitCost);

        if (abs($quantityDifference) < 0.00005 && abs($newUnitCost - $oldUnitCost) < 0.00005) {
            throw ValidationException::withMessages(['quantity' => 'Change the quantity or unit cost before saving.']);
        }

        $inventory->update([
            'on_hand'           => round($newQuantity, 4),
            'average_unit_cost' => round($newUnitCost, 4),
        ]);

        return MaterialInventoryTransaction::query()->create([
            'material_inventory_id' => $inventory->id,
            'material_reference_id' => $inventory->material_reference_id,
            'type'                   => 'set_balance',
            'quantity'               => round($quantityDifference, 4),
            'unit_cost'              => round($newUnitCost, 4),
            'total_value'            => round($valueDifference, 4),
            'balance_after'          => round($newQuantity, 4),
            'average_cost_after'     => round($newUnitCost, 4),
            'reference_type'         => 'manual',
            'notes'                  => $notes,
            'occurred_at'            => $occurredAt ?: now(),
            'created_by'             => $createdBy,
        ]);
    }

    public function syncJobOrder(JobOrder $jobOrder, bool $reverse = false): void
    {
        DB::transaction(function () use ($jobOrder, $reverse) {
            JobOrder::query()->whereKey($jobOrder->id)->lockForUpdate()->firstOrFail();
            $jobOrder->load('requirements');

            $requirementsByMaterial = $reverse
                ? collect()
                : $jobOrder->requirements
                    ->filter(fn ($requirement) => (int) $requirement->material_reference_id > 0)
                    ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
                    ->groupBy(fn ($requirement) => (int) $requirement->material_reference_id);

            $existing = MaterialInventoryTransaction::query()
                ->where('reference_type', 'job_order')
                ->where('reference_id', $jobOrder->id)
                ->selectRaw('material_reference_id, SUM(quantity) as quantity')
                ->groupBy('material_reference_id')
                ->pluck('quantity', 'material_reference_id');

            $materialIds = $requirementsByMaterial->keys()
                ->merge($existing->keys())
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique();

            foreach ($materialIds as $materialId) {
                $inventory = $this->lockedInventory($materialId);
                $currentIssue = (float) ($existing[$materialId] ?? 0);
                $availableBeforeJob = max((float) $inventory->on_hand - $currentIssue, 0);
                $requirements = $requirementsByMaterial->get($materialId, collect());
                $remainingNeed = $requirements->sum(fn ($requirement) => max(
                    (float) $requirement->required_qty - (float) $requirement->received_qty,
                    0
                ));
                $allocatedTotal = $reverse ? 0 : min($remainingNeed, $availableBeforeJob);
                $desiredIssue = -$allocatedTotal;
                $delta = $desiredIssue - $currentIssue;

                if (abs($delta) >= 0.00005) {
                    $this->postMovement(
                        $inventory,
                        abs($currentIssue) < 0.00005 ? 'job_order_issue' : 'job_order_adjustment',
                        $delta,
                        null,
                        'job_order',
                        $jobOrder->id,
                        $materialId,
                        'Material consumption for '.$jobOrder->job_order_number,
                        now(),
                        auth()->id()
                    );
                }

                $remainingAllocation = $allocatedTotal;

                foreach ($requirements as $requirement) {
                    $required = (float) $requirement->required_qty;
                    $received = min((float) $requirement->received_qty, $required);
                    $allocatableNeed = max($required - $received, 0);
                    $allocated = min($allocatableNeed, $remainingAllocation);
                    $remainingAllocation -= $allocated;
                    $balance = max($required - $received - $allocated, 0);

                    $requirement->update([
                        'inventory_allocated_qty' => round($allocated, 4),
                        'received_qty'            => round($received, 4),
                        'balance_qty'             => round($balance, 4),
                        'status'                  => $balance <= 0 ? 'fulfilled' : ($received > 0 || $allocated > 0 ? 'partial' : 'pending'),
                    ]);
                }
            }
        });
    }

    public function syncGoodsReceipt(GoodsReceipt $receipt, bool $reverse = false): void
    {
        DB::transaction(function () use ($receipt, $reverse) {
            GoodsReceipt::query()->whereKey($receipt->id)->lockForUpdate()->firstOrFail();
            $quantities = collect();
            $costTotals = collect();

            if (! $reverse) {
                $receipt->loadMissing('items');
                $requirementIds = $receipt->items->pluck('requirement_id')->filter()->map(fn ($id) => (int) $id)->unique();
                $requirements = JobOrderRequirement::query()
                    ->whereIn('id', $requirementIds)
                    ->get(['id', 'material_reference_id', 'unit'])
                    ->keyBy('id');
                $materialNames = MaterialReference::query()
                    ->get(['id', 'name', 'unit'])
                    ->keyBy(fn ($material) => mb_strtolower(trim($material->name)));

                foreach ($receipt->items as $item) {
                    $requirement = $requirements->get((int) $item->requirement_id);
                    $materialId = (int) ($requirement?->material_reference_id ?? 0);
                    $material = null;

                    if ($materialId <= 0) {
                        $material = $materialNames->get(mb_strtolower(trim((string) $item->material_name)));
                        $materialId = (int) ($material?->id ?? 0);
                    }

                    if ($materialId <= 0) {
                        continue;
                    }

                    $material ??= MaterialReference::query()->find($materialId, ['id', 'unit']);
                    $targetUnit = (string) ($material?->unit ?: $requirement?->unit ?: $item->unit);
                    $quantity = $this->unitConversionService->convert(
                        (float) $item->received_qty,
                        (string) $item->unit,
                        $targetUnit
                    );

                    if ($quantity === null) {
                        throw ValidationException::withMessages([
                            'items' => "The received unit {$item->unit} cannot be converted to the material stock unit {$targetUnit}.",
                        ]);
                    }

                    $quantities[$materialId] = (float) ($quantities[$materialId] ?? 0) + $quantity;
                    $costTotals[$materialId] = (float) ($costTotals[$materialId] ?? 0)
                        + ((float) $item->received_qty * (float) $item->unit_price);
                }
            }

            $costs = $quantities->mapWithKeys(function ($quantity, $materialId) use ($costTotals) {
                return [$materialId => $quantity > 0 ? (float) $costTotals[$materialId] / $quantity : 0];
            });

            $this->syncSource(
                'goods_receipt',
                $receipt->id,
                $quantities,
                $costs,
                'goods_receipt',
                'goods_receipt_adjustment',
                'Goods receipt '.$receipt->goods_receipt_number
            );
        });
    }

    public function updateReorderLevel(int $materialReferenceId, float $reorderLevel): MaterialInventory
    {
        return DB::transaction(function () use ($materialReferenceId, $reorderLevel) {
            $inventory = $this->lockedInventory($materialReferenceId);
            $inventory->update(['reorder_level' => max($reorderLevel, 0)]);

            return $inventory->fresh();
        });
    }

    protected function syncSource(
        string $referenceType,
        int $referenceId,
        Collection $desiredQuantities,
        Collection $unitCosts,
        string $initialType,
        string $adjustmentType,
        string $notes
    ): void {
        $existing = MaterialInventoryTransaction::query()
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->selectRaw('material_reference_id, SUM(quantity) as quantity')
            ->groupBy('material_reference_id')
            ->pluck('quantity', 'material_reference_id');

        $materialIds = $desiredQuantities->keys()
            ->merge($existing->keys())
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique();

        foreach ($materialIds as $materialId) {
            $desired = (float) ($desiredQuantities[$materialId] ?? 0);
            $current = (float) ($existing[$materialId] ?? 0);
            $delta = $desired - $current;

            if (abs($delta) < 0.00005) {
                continue;
            }

            $inventory = $this->lockedInventory($materialId);

            if ($delta < 0) {
                $delta = -min(abs($delta), max((float) $inventory->on_hand, 0));
            }

            if (abs($delta) < 0.00005) {
                continue;
            }

            $this->postMovement(
                $inventory,
                abs($current) < 0.00005 ? $initialType : $adjustmentType,
                $delta,
                $unitCosts->has($materialId) ? (float) $unitCosts[$materialId] : null,
                $referenceType,
                $referenceId,
                $materialId,
                $notes,
                now(),
                auth()->id()
            );
        }
    }

    protected function lockedInventory(int $materialReferenceId): MaterialInventory
    {
        MaterialReference::query()->findOrFail($materialReferenceId);
        MaterialInventory::query()->firstOrCreate(
            ['material_reference_id' => $materialReferenceId],
            ['on_hand' => 0, 'average_unit_cost' => 0, 'reorder_level' => 0]
        );

        return MaterialInventory::query()
            ->where('material_reference_id', $materialReferenceId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    protected function postMovement(
        MaterialInventory $inventory,
        string $type,
        float $quantity,
        ?float $unitCost,
        ?string $referenceType,
        ?int $referenceId,
        ?int $referenceLineId,
        ?string $notes,
        $occurredAt,
        ?int $createdBy
    ): MaterialInventoryTransaction {
        $oldQuantity = (float) $inventory->on_hand;
        $oldCost = (float) $inventory->average_unit_cost;
        $movementCost = max((float) ($unitCost ?? $oldCost), 0);
        if ($quantity < 0) {
            $quantity = -min(abs($quantity), max($oldQuantity, 0));
        }

        $newQuantity = $oldQuantity + $quantity;
        $newAverageCost = $oldCost;

        if ($quantity > 0) {
            if ($newQuantity > 0 && $oldQuantity > 0) {
                $newAverageCost = (($oldQuantity * $oldCost) + ($quantity * $movementCost)) / $newQuantity;
            } elseif ($newQuantity > 0) {
                $newAverageCost = $movementCost;
            }
        }

        $inventory->update([
            'on_hand'          => round($newQuantity, 4),
            'average_unit_cost'=> round(max($newAverageCost, 0), 4),
        ]);

        return MaterialInventoryTransaction::query()->create([
            'material_inventory_id' => $inventory->id,
            'material_reference_id' => $inventory->material_reference_id,
            'type'                   => $type,
            'quantity'               => round($quantity, 4),
            'unit_cost'              => round($movementCost, 4),
            'total_value'            => round($quantity * $movementCost, 4),
            'balance_after'          => round($newQuantity, 4),
            'average_cost_after'     => round(max($newAverageCost, 0), 4),
            'reference_type'         => $referenceType,
            'reference_id'           => $referenceId,
            'reference_line_id'      => $referenceLineId,
            'notes'                  => $notes,
            'occurred_at'            => $occurredAt ?: now(),
            'created_by'             => $createdBy,
        ]);
    }
}
