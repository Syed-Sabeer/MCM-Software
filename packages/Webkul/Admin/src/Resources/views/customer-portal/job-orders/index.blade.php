@extends('admin::customer-portal.layouts.app', ['title' => 'Orders & Production', 'subtitle' => 'Track confirmed orders, production state, and scheduled delivery dates.'])

@section('content')
    @include('admin::customer-portal.partials.list-toolbar', [
        'route' => 'customer_portal.job_orders.index',
        'placeholder' => 'Search order number, subject, or PO reference',
        'statuses' => ['open' => 'Order confirmed', 'in_progress' => 'In production', 'ready_to_ship' => 'Ready to ship', 'completed' => 'Completed', 'closed' => 'Closed', 'cancelled' => 'Cancelled'],
    ])

    <section class="portal-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
            <div><p class="portal-kicker">Production orders</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $records->total() }} order{{ $records->total() === 1 ? '' : 's' }}</p></div>
            <span class="portal-icon-box"><i class="icon-activity text-xl"></i></span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300"><tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Issued</th><th class="px-4 py-3">Delivery</th><th class="px-4 py-3">Progress</th><th class="px-4 py-3 text-right">Quantity</th><th class="w-12 px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse($records as $jobOrder)
                        <tr class="portal-row">
                            <td class="px-4 py-3.5"><a class="font-semibold text-brandColor hover:underline" href="{{ route('customer_portal.job_orders.view', $jobOrder->id) }}">{{ $jobOrder->job_order_number }}</a><p class="mt-0.5 max-w-[320px] truncate text-xs text-gray-500">{{ $jobOrder->subject ?: ($jobOrder->customer_po_reference ? 'PO '.$jobOrder->customer_po_reference : 'Customer order') }}</p></td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $jobOrder->issue_date?->format('M d, Y') ?: '-' }}</td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $jobOrder->required_delivery_date?->format('M d, Y') ?: 'Not scheduled' }}</td>
                            <td class="px-4 py-3.5">@include('admin::customer-portal.partials.status-badge', ['status' => $jobOrder->status])</td>
                            <td class="px-4 py-3.5 text-right font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::decimal($jobOrder->total_order_qty) }}</td>
                            <td class="px-4 py-3.5"><a href="{{ route('customer_portal.job_orders.view', $jobOrder->id) }}" class="inline-flex rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-brandColor dark:hover:bg-gray-800" aria-label="View {{ $jobOrder->job_order_number }}"><span class="icon-right-arrow text-xl"></span></a></td>
                        </tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 6, 'message' => 'No customer-visible orders match your search.'])
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-800 md:hidden">
            @forelse($records as $jobOrder)
                <a href="{{ route('customer_portal.job_orders.view', $jobOrder->id) }}" class="portal-row block p-4"><div class="flex items-start justify-between gap-3"><div><p class="font-semibold text-brandColor">{{ $jobOrder->job_order_number }}</p><p class="mt-1 text-xs text-gray-500">{{ $jobOrder->subject ?: 'Customer order' }}</p></div>@include('admin::customer-portal.partials.status-badge', ['status' => $jobOrder->status])</div><div class="mt-4 grid grid-cols-2 gap-3 text-xs"><div><p class="text-gray-500">Delivery</p><p class="mt-1 font-medium dark:text-gray-200">{{ $jobOrder->required_delivery_date?->format('M d, Y') ?: 'Not scheduled' }}</p></div><div class="text-right"><p class="text-gray-500">Quantity</p><p class="mt-1 font-semibold dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::decimal($jobOrder->total_order_qty) }}</p></div></div></a>
            @empty
                <p class="p-8 text-center text-sm text-gray-500">No customer-visible orders match your search.</p>
            @endforelse
        </div>
    </section>
    <div class="mt-4">{{ $records->links() }}</div>
@endsection
