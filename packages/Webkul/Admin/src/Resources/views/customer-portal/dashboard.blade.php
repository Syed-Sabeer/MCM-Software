@extends('admin::customer-portal.layouts.app', [
    'title' => 'Hi '.auth()->guard('customer')->user()->name,
    'subtitle' => 'Welcome back. Here is the latest activity for '.$organization->name.'.',
])

@section('content')
    @php
        $statItems = [
            ['label' => 'Visible Quotes', 'value' => $stats['quotes'], 'description' => 'Commercial proposals available', 'route' => 'customer_portal.quotes.index', 'icon' => 'icon-quote', 'permission' => $canViewDocuments],
            ['label' => 'Proforma Invoices', 'value' => $stats['proformas'], 'description' => 'Issued payment documents', 'route' => 'customer_portal.proformas.index', 'icon' => 'icon-dollar', 'permission' => $canViewDocuments],
            ['label' => 'Active Orders', 'value' => $stats['jobOrders'], 'description' => 'Orders currently in progress', 'route' => 'customer_portal.job_orders.index', 'icon' => 'icon-activity', 'permission' => $canViewDocuments],
            ['label' => 'Customer Products', 'value' => $stats['products'], 'description' => 'Products assigned to your company', 'route' => 'customer_portal.products.index', 'icon' => 'icon-product', 'permission' => $canViewProducts],
            ['label' => 'Upcoming Deliveries', 'value' => $stats['deliveries'], 'description' => 'Scheduled delivery dates ahead', 'route' => 'customer_portal.job_orders.index', 'icon' => 'icon-calendar', 'permission' => $canViewDocuments],
            ['label' => 'Outstanding Balance', 'value' => \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($stats['outstanding']), 'description' => 'Remaining proforma balance', 'route' => 'customer_portal.proformas.index', 'icon' => 'icon-stats-up', 'permission' => $canViewDocuments],
        ];
    @endphp

    <div class="grid gap-4">
        <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($statItems as $stat)
                @continue(! $stat['permission'])
                <a href="{{ route($stat['route']) }}" class="portal-stat p-4 pl-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="portal-kicker">{{ $stat['label'] }}</p>
                            <p class="mt-2 truncate text-2xl font-semibold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $stat['description'] }}</p>
                        </div>
                        <span class="portal-icon-box shrink-0"><i class="{{ $stat['icon'] }} text-xl"></i></span>
                    </div>
                </a>
            @endforeach
        </section>

        @if($canViewDocuments)
            <section class="portal-card p-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="portal-kicker">Production</p>
                        <h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Active order progress</h2>
                    </div>
                    <a href="{{ route('customer_portal.job_orders.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brandColor hover:underline">View all <span class="icon-right-arrow text-lg"></span></a>
                </div>

                <div class="mt-4 grid gap-3 lg:grid-cols-2">
                    @forelse($activeOrders as $orderData)
                        @php
                            $order = $orderData['record'];
                            $progress = $orderData['progress'];
                            $completedSteps = collect($progress['steps'])->where('done', true)->count();
                            $progressPercent = $progress['cancelled'] ? 0 : max(15, (int) round(($completedSteps / count($progress['steps'])) * 100));
                        @endphp
                        <a href="{{ route('customer_portal.job_orders.view', $order->id) }}" class="rounded-md border border-gray-200 p-4 transition hover:border-brandColor dark:border-gray-700">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $order->job_order_number }}</p>
                                    <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $order->subject ?: 'Customer order' }}</p>
                                </div>
                                @include('admin::customer-portal.partials.status-badge', ['status' => $order->status])
                            </div>
                            <div class="mt-4 portal-progress-track"><div class="portal-progress-fill" style="width: {{ $progressPercent }}%"></div></div>
                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $progress['label'] }}</span>
                                <span>Delivery {{ $order->required_delivery_date?->format('M d, Y') ?: 'not scheduled' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-md border border-dashed border-gray-300 px-5 py-8 text-center dark:border-gray-700">
                            <span class="icon-activity text-3xl text-gray-300 dark:text-gray-600"></span>
                            <p class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">No active orders</p>
                            <p class="mt-1 text-xs text-gray-500">New production orders will appear here.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <div class="grid gap-4 xl:grid-cols-2">
                <section class="portal-card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                        <div><p class="portal-kicker">Commercial</p><h2 class="mt-1 font-semibold text-gray-900 dark:text-white">Recent quotations</h2></div>
                        <a href="{{ route('customer_portal.quotes.index') }}" class="text-xs font-semibold text-brandColor hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($recentQuotes as $quote)
                            <a href="{{ route('customer_portal.quotes.view', $quote->id) }}" class="portal-row flex items-center justify-between gap-4 px-5 py-3.5">
                                <div class="min-w-0"><p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $quote->quote_number }}</p><p class="mt-0.5 truncate text-xs text-gray-500">{{ $quote->subject ?: 'Quotation' }}</p></div>
                                <div class="text-right"><p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($quote->grand_total) }}</p><p class="mt-0.5 text-xs text-gray-500">{{ $quote->quote_date?->format('M d, Y') ?: '-' }}</p></div>
                            </a>
                        @empty
                            <p class="px-5 py-8 text-center text-sm text-gray-500">No quotations available.</p>
                        @endforelse
                    </div>
                </section>

                <section class="portal-card overflow-hidden">
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                        <div><p class="portal-kicker">Payments</p><h2 class="mt-1 font-semibold text-gray-900 dark:text-white">Recent proformas</h2></div>
                        <a href="{{ route('customer_portal.proformas.index') }}" class="text-xs font-semibold text-brandColor hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($recentProformas as $proforma)
                            <a href="{{ route('customer_portal.proformas.view', $proforma->id) }}" class="portal-row flex items-center justify-between gap-4 px-5 py-3.5">
                                <div class="min-w-0"><p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $proforma->proforma_number }}</p><p class="mt-0.5 truncate text-xs text-gray-500">{{ $proforma->subject ?: 'Proforma invoice' }}</p></div>
                                <div class="text-right"><p class="text-sm font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->remaining_amount) }}</p><p class="mt-0.5 text-xs text-gray-500">Balance remaining</p></div>
                            </a>
                        @empty
                            <p class="px-5 py-8 text-center text-sm text-gray-500">No proforma invoices available.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif

        <section class="portal-card flex flex-wrap items-center justify-between gap-4 p-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="portal-icon-box shrink-0"><i class="icon-organization text-xl"></i></span>
                <div class="min-w-0"><p class="portal-kicker">Your company</p><h2 class="mt-1 truncate text-lg font-semibold text-gray-900 dark:text-white">{{ $organization->name }}</h2><p class="mt-1 truncate text-sm text-gray-500">{{ collect([$organization->billing_city, $organization->billing_state, $organization->billing_country])->filter()->implode(', ') ?: 'Company profile' }}</p></div>
            </div>
            <a href="{{ route('customer_portal.company') }}" class="secondary-button inline-flex items-center gap-2"><span class="icon-organization"></span> View company</a>
        </section>
    </div>
@endsection
