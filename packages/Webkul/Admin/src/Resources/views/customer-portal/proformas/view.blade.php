@extends('admin::customer-portal.layouts.app', ['title' => 'Proforma '.$proforma->proforma_number])

@section('content')
    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p><p class="mt-1"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium capitalize text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ $proforma->status ?: '-' }}</span></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Grand Total</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->grand_total) }}</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Received</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->received_amount) }}</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Balance</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->remaining_amount) }}</p></div>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3">Rate</th><th class="px-4 py-3">Total</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse ($proforma->items as $item)
                        <tr><td class="px-4 py-3 text-gray-800 dark:text-white">{{ $item->item_code ?: $item->item_name }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->description ?: '-' }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ rtrim(rtrim(number_format((float) $item->qty, 3, '.', ''), '0'), '.') }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($item->unit_price) }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($item->line_total) }}</td></tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="mb-3 text-base font-semibold text-gray-800 dark:text-white">Receipts</p>
        <div class="grid gap-2">
            @forelse ($proforma->receipts as $receipt)
                <p class="rounded border border-gray-100 px-3 py-2 text-sm text-gray-700 dark:border-gray-800 dark:text-gray-300">{{ optional($receipt->payment_date)->format('Y-m-d') ?: '-' }} | {{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($receipt->amount) }} | {{ $receipt->payment_method ?: '-' }}</p>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-300">No receipts recorded yet.</p>
            @endforelse
        </div>
    </div>
@endsection
