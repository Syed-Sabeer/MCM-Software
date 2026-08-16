<?php

namespace Webkul\Product\Services;

use Illuminate\Support\Collection;
use Webkul\Product\Models\UnitReference;

class UnitConversionService
{
    protected ?Collection $units = null;

    public function factor(string $fromUnit, string $toUnit): ?float
    {
        $from = $this->normalize($fromUnit);
        $to = $this->normalize($toUnit);

        if ($from === '' || $to === '') {
            return null;
        }

        if ($from === $to) {
            return 1.0;
        }

        $fromReference = $this->units()->get($from);
        $toReference = $this->units()->get($to);

        if (! $fromReference || ! $toReference) {
            return null;
        }

        $fromMeters = $fromReference->meter_conversion;
        $toMeters = $toReference->meter_conversion;

        if ($fromMeters === null || $toMeters === null || (float) $toMeters <= 0) {
            return null;
        }

        return (float) $fromMeters / (float) $toMeters;
    }

    public function convert(float $quantity, string $fromUnit, string $toUnit): ?float
    {
        $factor = $this->factor($fromUnit, $toUnit);

        return $factor === null ? null : $quantity * $factor;
    }

    public function canonicalName(string $unit): ?string
    {
        return $this->units()->get($this->normalize($unit))?->name;
    }

    public function clearCache(): void
    {
        $this->units = null;
    }

    protected function normalize(string $unit): string
    {
        return strtoupper(trim($unit));
    }

    protected function units(): Collection
    {
        return $this->units ??= UnitReference::query()
            ->get(['name', 'meter_conversion'])
            ->keyBy(fn (UnitReference $unit) => $this->normalize($unit->name));
    }
}
