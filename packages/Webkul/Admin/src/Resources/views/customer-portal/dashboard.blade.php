@extends('admin::customer-portal.layouts.app', [
    'title' => 'Dashboard',
    'subtitle' => $organization ? 'Overview for '.$organization->name : 'Ask your administrator to link your user to a customer company.',
])

@section('content')
    @if (! $organization)
        <div class="rounded-lg border border-gray-200 bg-white p-10 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            No customer company is linked to your portal user yet.
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Quotes', 'value' => $stats['quotes'], 'route' => 'customer_portal.quotes.index'],
                ['label' => 'Proformas', 'value' => $stats['proformas'], 'route' => 'customer_portal.proformas.index'],
                ['label' => 'Job Orders', 'value' => $stats['jobOrders'], 'route' => 'customer_portal.job_orders.index'],
                ['label' => 'Products', 'value' => $stats['products'], 'route' => 'customer_portal.products.index'],
            ] as $stat)
                <a href="{{ route($stat['route']) }}" class="rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-brandColor dark:border-gray-800 dark:bg-gray-900">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-4 grid gap-4 xl:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Recent Quotes</p>
                <div class="grid gap-2">
                    @forelse ($recentQuotes as $quote)
                        <a href="{{ route('customer_portal.quotes.view', $quote->id) }}" class="flex items-center justify-between gap-4 rounded border border-gray-100 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950">
                            <span class="font-medium text-brandColor">{{ $quote->quote_number }}</span>
                            <span class="text-gray-500 dark:text-gray-300">{{ optional($quote->quote_date)->format('Y-m-d') }} | {{ ucfirst($quote->status ?: '-') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-300">No quotes yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Recent Proformas</p>
                <div class="grid gap-2">
                    @forelse ($recentProformas as $proforma)
                        <a href="{{ route('customer_portal.proformas.view', $proforma->id) }}" class="flex items-center justify-between gap-4 rounded border border-gray-100 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950">
                            <span class="font-medium text-brandColor">{{ $proforma->proforma_number }}</span>
                            <span class="text-gray-500 dark:text-gray-300">{{ optional($proforma->issue_date)->format('Y-m-d') }} | {{ ucfirst($proforma->status ?: '-') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-300">No proformas yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Recent Job Orders</p>
                <div class="grid gap-2">
                    @forelse ($recentJobOrders as $jobOrder)
                        <a href="{{ route('customer_portal.job_orders.view', $jobOrder->id) }}" class="flex items-center justify-between gap-4 rounded border border-gray-100 px-3 py-2 text-sm hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-950">
                            <span class="font-medium text-brandColor">{{ $jobOrder->job_order_number }}</span>
                            <span class="text-gray-500 dark:text-gray-300">{{ optional($jobOrder->issue_date)->format('Y-m-d') }} | {{ ucfirst(str_replace('_', ' ', $jobOrder->status ?: '-')) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-300">No job orders yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Company</p>
                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $organization->name }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                    {{ collect([$organization->billing_street, $organization->billing_city, $organization->billing_state, $organization->billing_postcode, $organization->billing_country])->filter()->implode(', ') ?: '-' }}
                </p>
            </div>
        </div>
    @endif
@endsection
