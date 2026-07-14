<?php

namespace Webkul\Admin\Services\CustomerPortal;

class OrderProgressPresenter
{
    private const STEPS = ['Order Confirmed', 'In Production', 'Ready to Ship', 'Completed'];

    private const INDEXES = [
        'draft'     => 0, 'open' => 0, 'in_progress' => 1, 'ready_to_ship' => 2,
        'completed' => 3, 'closed' => 3, 'cancelled' => -1,
    ];

    public function present(?string $status): array
    {
        $status = strtolower((string) $status);
        $current = self::INDEXES[$status] ?? 0;

        return [
            'label'     => $status === 'cancelled' ? 'Cancelled' : self::STEPS[max(0, $current)],
            'cancelled' => $status === 'cancelled',
            'steps'     => collect(self::STEPS)->map(fn ($label, $index) => [
                'label' => $label,
                'done'  => $current >= $index && $current >= 0,
            ])->all(),
        ];
    }
}
