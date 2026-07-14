@php
    $normalizedStatus = strtolower((string) ($status ?? ''));
    $statusLabel = \Illuminate\Support\Str::headline($normalizedStatus ?: 'Not set');
    $statusClass = match ($normalizedStatus) {
        'completed', 'closed', 'approved', 'paid', 'issued' => 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-300',
        'cancelled', 'rejected', 'expired' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-300',
        'ready_to_ship' => 'bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-300',
        'in_progress' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300',
        default => 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-300',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
    {{ $statusLabel }}
</span>
