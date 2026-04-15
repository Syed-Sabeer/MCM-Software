<?php

namespace Webkul\Core\Support;

use Illuminate\Database\Eloquent\Model;

class DocumentChargeManager
{
    public function normalize(array $charges, float $subTotal): array
    {
        $normalized = [];
        $sortOrder = 0;

        foreach ($charges as $charge) {
            if (! is_array($charge)) {
                continue;
            }

            $name = trim((string) ($charge['name'] ?? ''));
            $type = strtolower((string) ($charge['type'] ?? 'value')) === 'percentage'
                ? 'percentage'
                : 'value';
            $value = round((float) ($charge['value'] ?? 0), 4);

            if ($name === '') {
                continue;
            }

            $amount = $type === 'percentage'
                ? round($subTotal * ($value / 100), 4)
                : round($value, 4);

            $normalized[] = [
                'name'       => $name,
                'type'       => $type,
                'value'      => $value,
                'amount'     => $amount,
                'sort_order' => $sortOrder++,
            ];
        }

        return $normalized;
    }

    public function summarize(string $documentType, array $charges): array
    {
        $taxAmount = 0.0;
        $otherAmount = 0.0;
        $taxPercent = 0.0;
        $otherPercent = 0.0;

        foreach ($charges as $charge) {
            $amount = round((float) ($charge['amount'] ?? 0), 4);
            $value = round((float) ($charge['value'] ?? 0), 4);
            $isTax = $this->isTaxChargeName((string) ($charge['name'] ?? ''));

            if ($isTax) {
                $taxAmount += $amount;

                if ($taxPercent <= 0 && ($charge['type'] ?? 'value') === 'percentage') {
                    $taxPercent = $value;
                }
            } else {
                $otherAmount += $amount;

                if ($otherPercent <= 0 && ($charge['type'] ?? 'value') === 'percentage') {
                    $otherPercent = $value;
                }
            }
        }

        $summary = [
            'charge_total' => round($taxAmount + $otherAmount, 4),
        ];

        return match ($documentType) {
            'quote', 'proforma' => array_merge($summary, [
                'tax_amount'        => round($taxAmount, 4),
                'adjustment_amount' => round($otherAmount, 4),
                'tariff_percent'    => round($taxPercent, 4),
                'freight_percent'   => round($otherPercent, 4),
            ]),
            'vendor_quote' => array_merge($summary, [
                'sales_tax_percent' => round($taxPercent, 4),
                'sales_tax_amount'  => round($taxAmount, 4),
                'freight'           => round($otherAmount, 4),
            ]),
            'purchase_order' => array_merge($summary, [
                'sales_tax_percent' => round($taxPercent, 4),
                'tax_amount'        => round($taxAmount, 4),
                'freight'           => round($otherAmount, 4),
            ]),
            default => $summary,
        };
    }

    public function sync(Model $document, array $charges): void
    {
        $document->additionalCharges()->delete();

        if ($charges === []) {
            return;
        }

        $document->additionalCharges()->createMany($charges);
    }

    public function extract(Model $document, string $documentType): array
    {
        $relation = method_exists($document, 'additionalCharges')
            ? $document->additionalCharges
            : collect();

        if ($relation && $relation->count()) {
            return $relation
                ->map(fn ($charge) => [
                    'name'   => $charge->name,
                    'type'   => $charge->type,
                    'value'  => (float) $charge->value,
                    'amount' => (float) $charge->amount,
                ])
                ->values()
                ->all();
        }

        return $this->legacyCharges($document, $documentType);
    }

    public function legacyCharges(Model $document, string $documentType): array
    {
        return match ($documentType) {
            'quote', 'proforma' => $this->quoteLikeLegacyCharges($document),
            'vendor_quote'      => $this->vendorQuoteLegacyCharges($document),
            'purchase_order'    => $this->purchaseOrderLegacyCharges($document),
            default             => [],
        };
    }

    protected function quoteLikeLegacyCharges(Model $document): array
    {
        $charges = [];
        $taxAmount = (float) ($document->tax_amount ?? 0);
        $adjustmentAmount = (float) ($document->adjustment_amount ?? 0);
        $tariffPercent = (float) ($document->tariff_percent ?? 0);
        $freightPercent = (float) ($document->freight_percent ?? 0);

        if ($taxAmount > 0 || $tariffPercent > 0) {
            $charges[] = [
                'name'   => 'Tariff',
                'type'   => $tariffPercent > 0 ? 'percentage' : 'value',
                'value'  => $tariffPercent > 0 ? $tariffPercent : $taxAmount,
                'amount' => $taxAmount,
            ];
        }

        if ($adjustmentAmount > 0 || $freightPercent > 0) {
            $charges[] = [
                'name'   => 'Freight',
                'type'   => $freightPercent > 0 ? 'percentage' : 'value',
                'value'  => $freightPercent > 0 ? $freightPercent : $adjustmentAmount,
                'amount' => $adjustmentAmount,
            ];
        }

        return $charges;
    }

    protected function vendorQuoteLegacyCharges(Model $document): array
    {
        $charges = [];
        $taxAmount = (float) ($document->sales_tax_amount ?? 0);
        $taxPercent = (float) ($document->sales_tax_percent ?? 0);
        $freight = (float) ($document->freight ?? 0);

        if ($taxAmount > 0 || $taxPercent > 0) {
            $charges[] = [
                'name'   => 'Sales Tax',
                'type'   => $taxPercent > 0 ? 'percentage' : 'value',
                'value'  => $taxPercent > 0 ? $taxPercent : $taxAmount,
                'amount' => $taxAmount,
            ];
        }

        if ($freight > 0) {
            $charges[] = [
                'name'   => 'Freight',
                'type'   => 'value',
                'value'  => $freight,
                'amount' => $freight,
            ];
        }

        return $charges;
    }

    protected function purchaseOrderLegacyCharges(Model $document): array
    {
        $charges = [];
        $taxAmount = (float) ($document->tax_amount ?? 0);
        $taxPercent = (float) ($document->sales_tax_percent ?? 0);
        $freight = (float) ($document->freight ?? 0);

        if ($taxAmount > 0 || $taxPercent > 0) {
            $charges[] = [
                'name'   => 'Sales Tax',
                'type'   => $taxPercent > 0 ? 'percentage' : 'value',
                'value'  => $taxPercent > 0 ? $taxPercent : $taxAmount,
                'amount' => $taxAmount,
            ];
        }

        if ($freight > 0) {
            $charges[] = [
                'name'   => 'Freight',
                'type'   => 'value',
                'value'  => $freight,
                'amount' => $freight,
            ];
        }

        return $charges;
    }

    protected function isTaxChargeName(string $name): bool
    {
        $normalized = strtolower(trim($name));

        foreach (['tax', 'tariff', 'tarrif', 'duty', 'vat', 'gst'] as $needle) {
            if (str_contains($normalized, $needle)) {
                return true;
            }
        }

        return false;
    }
}
