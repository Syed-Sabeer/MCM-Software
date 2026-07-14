@extends('admin::customer-portal.layouts.app', ['title' => 'Job Order '.$jobOrder->job_order_number, 'subtitle' => $jobOrder->subject ?: 'Production progress, delivery schedule, and order quantities.'])

@section('content')
    <div class="portal-card p-4">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</p><p class="mt-1"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-300">{{ $progress['label'] }}</span></p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Issue Date</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ optional($jobOrder->issue_date)->format('Y-m-d') ?: '-' }}</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">ETD</p><p class="mt-1 text-sm text-gray-800 dark:text-white">{{ optional($jobOrder->required_delivery_date)->format('Y-m-d') ?: '-' }}</p></div>
            <div><p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Qty</p><p class="mt-1 text-sm font-semibold text-gray-800 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::decimal($jobOrder->total_order_qty) }}</p></div>
        </div>
        <ol class="mt-5 grid gap-2 sm:grid-cols-4" aria-label="Order progress">
            @foreach($progress['steps'] as $step)<li class="border-t-4 pt-2 text-xs font-medium {{ $step['done'] ? 'border-brandColor text-gray-900 dark:text-white' : 'border-gray-200 text-gray-500 dark:border-gray-700' }}">{{ $step['label'] }}</li>@endforeach
        </ol>
    </div>

    <div class="portal-card mt-4 overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800"><p class="portal-kicker">Job Order Items</p><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Products and quantities</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-gray-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="w-20 px-4 py-3">Image</th><th class="px-4 py-3">Item Code</th><th class="px-4 py-3">Product Name</th><th class="px-4 py-3">Color</th><th class="px-4 py-3 text-right">Qty</th><th class="px-4 py-3">Unit</th><th class="px-4 py-3 text-right">Rate</th><th class="px-4 py-3 text-right">Amount</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse ($jobOrder->items as $item)
                        @php($imageUrl = $item->proformaInvoiceItem?->preview_image ?: \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::productImageUrl($item->product?->cover_image))
                        <tr class="portal-row"><td class="px-4 py-3">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $item->item_name }}" class="h-12 w-12 rounded-md border border-gray-200 object-cover dark:border-gray-700">@else<span class="flex h-12 w-12 items-center justify-center rounded-md border border-gray-200 bg-slate-50 dark:border-gray-700 dark:bg-gray-950"><i class="icon-image text-xl text-gray-300"></i></span>@endif</td><td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $item->display_code }}</td><td class="px-4 py-3"><p class="font-medium text-gray-900 dark:text-white">{{ $item->item_name ?: $item->product?->name ?: '-' }}</p><p class="mt-0.5 max-w-[260px] text-xs text-gray-500">{{ $item->description }}</p></td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->color_variant_name ?: '-' }}</td><td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::decimal($item->qty) }}</td><td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $item->unit ?: 'PCS' }}</td><td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($item->unit_price) }}</td><td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($item->line_total) }}</td></tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 8])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
