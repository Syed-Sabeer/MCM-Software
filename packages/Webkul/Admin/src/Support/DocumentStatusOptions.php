<?php

namespace Webkul\Admin\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DocumentStatusOptions
{
    public const TYPES = [
        'proforma_invoice' => [
            'draft',
            'issued',
            'partially_paid',
            'fully_paid',
            'cancelled',
            'ready_for_job_order',
            'converted',
        ],
        'job_order' => [
            'draft',
            'open',
            'in_progress',
            'ready_to_ship',
            'completed',
            'closed',
            'cancelled',
        ],
        'vendor_quote' => [
            'draft',
            'requested',
            'received',
            'selected',
            'rejected',
            'cancelled',
        ],
        'purchase_order' => [
            'draft',
            'issued',
            'partially_received',
            'fully_received',
            'closed',
            'cancelled',
        ],
    ];

    public static function all(string $type): array
    {
        return collect(static::stored($type))->values()->all();
    }

    public static function defaults(string $type): array
    {
        return collect(static::TYPES[$type] ?? [])
            ->map(fn ($value, $index) => [
                'id'         => null,
                'name'       => Str::headline($value),
                'value'      => $value,
                'sort_order' => $index + 1,
            ])
            ->all();
    }

    public static function allowedTypes(): array
    {
        return array_keys(static::TYPES);
    }

    public static function label(string $type, ?string $value): string
    {
        $value = $value ?: 'draft';
        $status = collect(static::all($type))->firstWhere('value', $value);

        return $status['name'] ?? Str::headline($value);
    }

    public static function filterOptions(string $type): array
    {
        return collect(static::all($type))
            ->map(fn ($status) => [
                'label' => $status['name'],
                'value' => $status['value'],
            ])
            ->values()
            ->all();
    }

    protected static function stored(string $type): array
    {
        if (! in_array($type, static::allowedTypes(), true) || ! Schema::hasTable('document_statuses')) {
            return [];
        }

        return DB::table('document_statuses')
            ->where('type', $type)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'value'])
            ->map(fn ($status) => [
                'id'    => $status->id,
                'name'  => $status->name,
                'value' => $status->value,
            ])
            ->all();
    }
}
