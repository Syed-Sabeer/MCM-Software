<?php

namespace Webkul\PurchaseOrder\Support;

use Illuminate\Support\Collection;
use Webkul\Product\Services\UnitConversionService;

class RequirementVendorAggregator
{
    public function __construct(protected UnitConversionService $unitConversionService)
    {
    }

    public function totals(Collection $requirements): Collection
    {
        return $requirements
            ->map(fn ($requirement) => $this->normalizeRequirement($requirement))
            ->groupBy(fn ($row) => $row['group_key'])
            ->map(function (Collection $rows) {
                $first = $rows->first();
                $required = $rows->sum('required_qty');
                $received = min($rows->sum('received_qty'), $required);
                $balance = max($required - $received, 0);

                return [
                    'requirement_id'       => $first['requirement_id'],
                    'requirement_ids'      => $rows->pluck('requirement_id')->filter()->unique()->values()->all(),
                    'material_reference_id'=> $first['material_reference_id'],
                    'material_name'        => $first['material_name'],
                    'color_name'           => $first['color_name'],
                    'color_code'           => $first['color_code'],
                    'color_label'          => $first['color_label'],
                    'required_qty'         => $required,
                    'received_qty'         => $received,
                    'balance_qty'          => $balance,
                    'unit'                 => $first['unit'],
                    'vendor_ids'           => $rows->flatMap(fn ($row) => $row['vendor_ids'])->filter()->unique()->values()->all(),
                ];
            })
            ->filter(fn ($row) => $row['required_qty'] > 0.00005 || $row['received_qty'] > 0.00005)
            ->sortBy([
                ['material_name', 'asc'],
                ['color_label', 'asc'],
                ['unit', 'asc'],
            ])
            ->values();
    }

    public function totalsForVendor(Collection $requirements, ?int $vendorId = null): Collection
    {
        $filtered = $vendorId
            ? $requirements->filter(fn ($requirement) => in_array($vendorId, array_map('intval', (array) ($requirement->vendor_ids ?? [])), true))
            : $requirements;

        return $this->totals($filtered->values());
    }

    protected function normalizeRequirement($requirement): array
    {
        $unit = trim((string) ($requirement->unit ?: 'PCS'));
        $conversion = $this->conversionFor($unit);
        $targetUnit = $conversion !== null ? 'METER' : strtoupper($unit ?: 'PCS');
        $multiplier = $conversion ?? 1;
        $materialName = trim((string) $requirement->material_name);
        $colorName = trim((string) ($requirement->color_name ?: ''));
        $colorCode = trim((string) ($requirement->color_code ?: ''));
        $colorLabel = $colorName !== '' ? $colorName : ($colorCode !== '' ? $colorCode : '-');

        return [
            'requirement_id'        => $requirement->id,
            'material_reference_id' => $requirement->material_reference_id,
            'material_name'         => $materialName,
            'color_name'            => $colorName !== '' ? $colorName : null,
            'color_code'            => $colorCode !== '' ? $colorCode : null,
            'color_label'           => $colorLabel,
            'required_qty'          => max((float) $requirement->required_qty - (float) $requirement->inventory_allocated_qty, 0) * $multiplier,
            'received_qty'          => min(
                (float) $requirement->received_qty,
                max((float) $requirement->required_qty - (float) $requirement->inventory_allocated_qty, 0)
            ) * $multiplier,
            'unit'                  => $targetUnit,
            'vendor_ids'            => array_map('intval', (array) ($requirement->vendor_ids ?? [])),
            'group_key'             => implode('|', [
                mb_strtolower($materialName),
                mb_strtolower($colorLabel),
                $targetUnit,
            ]),
        ];
    }

    protected function conversionFor(string $unit): ?float
    {
        return $this->unitConversionService->factor($unit, 'METER');
    }
}
