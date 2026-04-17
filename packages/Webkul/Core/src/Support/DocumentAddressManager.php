<?php

namespace Webkul\Core\Support;

use Webkul\Contact\Models\Organization;

class DocumentAddressManager
{
    public function getOptions(?Organization $organization): array
    {
        if (! $organization) {
            return [];
        }

        $options = [];

        $billingAddress = $this->formatStructuredAddress([
            $organization->billing_street,
            $organization->billing_city,
            $organization->billing_state,
            $organization->billing_postcode,
            $organization->billing_country,
        ]);

        if ($billingAddress !== '') {
            $options[] = [
                'key'     => 'billing',
                'label'   => 'Billing Address',
                'type'    => 'billing',
                'address' => $billingAddress,
            ];
        }

        $shippingAddress = $this->formatStructuredAddress([
            $organization->shipping_street,
            $organization->shipping_city,
            $organization->shipping_state,
            $organization->shipping_postcode,
            $organization->shipping_country,
        ]);

        if ($shippingAddress !== '') {
            $options[] = [
                'key'     => 'shipping',
                'label'   => 'Shipping Address',
                'type'    => 'shipping',
                'address' => $shippingAddress,
            ];
        }

        foreach (collect($organization->address ?? [])->values() as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $type = strtolower(trim((string) ($row['type'] ?? 'other')));

            if (in_array($type, ['billing', 'shipping'], true)) {
                continue;
            }

            $address = $this->formatStructuredAddress([
                $row['street'] ?? $row['address'] ?? null,
                $row['city'] ?? null,
                $row['state'] ?? null,
                $row['postcode'] ?? null,
                $row['country'] ?? null,
            ]);

            if ($address === '') {
                continue;
            }

            $options[] = [
                'key'     => 'extra_' . $index,
                'label'   => $this->buildExtraAddressLabel($row, $index),
                'type'    => $type ?: 'other',
                'address' => $address,
            ];
        }

        return $options;
    }

    public function getDefaultAddress(?Organization $organization, string $preferredType = 'billing'): array
    {
        $options = $this->getOptions($organization);

        if (empty($options)) {
            return [
                'key'     => null,
                'label'   => null,
                'type'    => $preferredType,
                'address' => '',
            ];
        }

        $preferred = collect($options)->firstWhere('type', $preferredType);

        return $preferred ?: $options[0];
    }

    public function normalize(?Organization $organization, array|string|null $value, string $preferredType = 'billing'): array
    {
        if (is_string($value)) {
            $value = ['address' => $value];
        }

        $value = is_array($value) ? $value : [];
        $options = $this->getOptions($organization);
        $selectedKey = trim((string) ($value['key'] ?? $value['selected_key'] ?? ''));

        if ($selectedKey !== '') {
            $matched = collect($options)->firstWhere('key', $selectedKey);

            if ($matched) {
                return $matched;
            }
        }

        $manualAddress = trim((string) ($value['address'] ?? ''));

        if ($manualAddress !== '') {
            return [
                'key'     => $selectedKey ?: null,
                'label'   => $value['label'] ?? ucfirst($preferredType) . ' Address',
                'type'    => $value['type'] ?? $preferredType,
                'address' => $manualAddress,
            ];
        }

        return $this->getDefaultAddress($organization, $preferredType);
    }

    protected function buildExtraAddressLabel(array $row, int $index): string
    {
        $label = trim((string) ($row['label'] ?? $row['name'] ?? ''));

        if ($label !== '') {
            return $label;
        }

        $type = trim((string) ($row['type'] ?? 'Other'));

        return ucfirst($type !== '' ? $type : 'Other') . ' Address ' . ($index + 1);
    }

    protected function formatStructuredAddress(array $parts): string
    {
        $street = trim((string) ($parts[0] ?? ''));
        $summary = collect(array_slice($parts, 1))
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->implode(', ');

        return collect([$street, $summary])
            ->filter()
            ->implode("\n");
    }
}
