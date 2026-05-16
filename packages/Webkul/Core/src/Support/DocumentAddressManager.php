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

        $billingParts = [
            'street'   => $organization->billing_street,
            'city'     => $organization->billing_city,
            'state'    => $organization->billing_state,
            'postcode' => $organization->billing_postcode,
            'country'  => $organization->billing_country,
        ];

        $billingAddress = $this->formatStructuredAddress(array_values($billingParts));

        if ($billingAddress !== '') {
            $options[] = $this->buildAddressOption('billing', 'Billing Address', 'billing', $billingAddress, $billingParts);
        }

        $shippingParts = [
            'street'   => $organization->shipping_street,
            'city'     => $organization->shipping_city,
            'state'    => $organization->shipping_state,
            'postcode' => $organization->shipping_postcode,
            'country'  => $organization->shipping_country,
        ];

        $shippingAddress = $this->formatStructuredAddress(array_values($shippingParts));

        if ($shippingAddress !== '') {
            $options[] = $this->buildAddressOption('shipping', 'Shipping Address', 'shipping', $shippingAddress, $shippingParts);
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

            $options[] = $this->buildAddressOption('extra_' . $index, $this->buildExtraAddressLabel($row, $index), $type ?: 'other', $address, [
                'street'   => $row['street'] ?? $row['address'] ?? null,
                'city'     => $row['city'] ?? null,
                'state'    => $row['state'] ?? null,
                'postcode' => $row['postcode'] ?? null,
                'country'  => $row['country'] ?? null,
            ]);
        }

        return $options;
    }

    public function getDefaultAddress(?Organization $organization, string $preferredType = 'billing'): array
    {
        $options = $this->getOptions($organization);

        if (empty($options)) {
            return [
                'key'      => null,
                'label'    => null,
                'type'     => $preferredType,
                'address'  => '',
                'street'   => null,
                'city'     => null,
                'state'    => null,
                'postcode' => null,
                'country'  => null,
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
                'key'      => $selectedKey ?: null,
                'label'    => $value['label'] ?? ucfirst($preferredType) . ' Address',
                'type'     => $value['type'] ?? $preferredType,
                'address'  => $manualAddress,
                'street'   => $value['street'] ?? null,
                'city'     => $value['city'] ?? null,
                'state'    => $value['state'] ?? null,
                'postcode' => $value['postcode'] ?? null,
                'country'  => $value['country'] ?? null,
            ];
        }

        return $this->getDefaultAddress($organization, $preferredType);
    }

    protected function buildAddressOption(string $key, string $label, string $type, string $address, array $parts): array
    {
        return [
            'key'      => $key,
            'label'    => $label,
            'type'     => $type,
            'address'  => $address,
            'street'   => $parts['street'] ?? null,
            'city'     => $parts['city'] ?? null,
            'state'    => $parts['state'] ?? null,
            'postcode' => $parts['postcode'] ?? null,
            'country'  => $parts['country'] ?? null,
        ];
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
