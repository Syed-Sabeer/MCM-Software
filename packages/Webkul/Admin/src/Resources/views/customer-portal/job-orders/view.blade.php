@extends('admin::customer-portal.layouts.app', ['title' => 'Job Order '.$jobOrder->job_order_number])

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p><p class="mt-1"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium capitalize text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ str_replace('_', ' ', $jobOrder->status ?: '-') }}</span></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Issue Date</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ optional($jobOrder->issue_date)->format('Y-m-d') ?: '-' }}</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">ETD</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ optional($jobOrder->required_delivery_date)->format('Y-m-d') ?: '-' }}</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Qty</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">{{ rtrim(rtrim(number_format((float) $jobOrder->total_order_qty, 2, '.', ''), '0'), '.') }}</p></div>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="px-4 py-3">Item Code</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3">Unit</th><th class="px-4 py-3">Line Total</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse ($jobOrder->items as $item)
                        <tr><td class="px-4 py-3 text-gray-800 dark:text-white">{{ $item->item_code ?: '-' }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->description ?: $item->item_name ?: '-' }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ rtrim(rtrim(number_format((float) $item->qty, 2, '.', ''), '0'), '.') }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->unit ?: '-' }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($item->line_total) }}</td></tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
