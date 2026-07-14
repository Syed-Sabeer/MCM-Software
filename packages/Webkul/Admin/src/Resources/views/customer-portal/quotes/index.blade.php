@extends('admin::customer-portal.layouts.app', ['title' => 'Quotations', 'subtitle' => 'Review published quotations, commercial values, and delivery estimates.'])

@section('content')
    @include('admin::customer-portal.partials.list-toolbar', [
        'route' => 'customer_portal.quotes.index',
        'placeholder' => 'Search quote number or subject',
        'statuses' => ['open' => 'Open', 'approved' => 'Approved', 'closed' => 'Closed', 'rejected' => 'Rejected', 'expired' => 'Expired'],
    ])

    <section class="portal-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
            <div><p class="portal-kicker">Commercial documents</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $records->total() }} quotation{{ $records->total() === 1 ? '' : 's' }}</p></div>
            <span class="portal-icon-box"><i class="icon-quote text-xl"></i></span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300">
                    <tr><th class="px-4 py-3">Quotation</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Est. delivery</th><th class="px-4 py-3 text-right">Total</th><th class="w-12 px-4 py-3"></th></tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse($records as $quote)
                        <tr class="portal-row">
                            <td class="px-4 py-3.5"><a class="font-semibold text-brandColor hover:underline" href="{{ route('customer_portal.quotes.view', $quote->id) }}">{{ $quote->quote_number }}</a><p class="mt-0.5 max-w-[340px] truncate text-xs text-gray-500">{{ $quote->subject ?: 'Quotation' }}</p></td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $quote->quote_date?->format('M d, Y') ?: '-' }}</td>
                            <td class="px-4 py-3.5">@include('admin::customer-portal.partials.status-badge', ['status' => $quote->status])</td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $quote->eta?->format('M d, Y') ?: ($quote->etd?->format('M d, Y') ?: '-') }}</td>
                            <td class="px-4 py-3.5 text-right font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($quote->grand_total) }}</td>
                            <td class="px-4 py-3.5"><a href="{{ route('customer_portal.quotes.view', $quote->id) }}" class="inline-flex rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-brandColor dark:hover:bg-gray-800" aria-label="View {{ $quote->quote_number }}"><span class="icon-right-arrow text-xl"></span></a></td>
                        </tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 6, 'message' => 'No published quotations match your search.'])
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-800 md:hidden">
            @forelse($records as $quote)
                <a href="{{ route('customer_portal.quotes.view', $quote->id) }}" class="portal-row block p-4">
                    <div class="flex items-start justify-between gap-3"><div><p class="font-semibold text-brandColor">{{ $quote->quote_number }}</p><p class="mt-1 line-clamp-1 text-xs text-gray-500">{{ $quote->subject ?: 'Quotation' }}</p></div>@include('admin::customer-portal.partials.status-badge', ['status' => $quote->status])</div>
                    <div class="mt-4 grid grid-cols-2 gap-3 text-xs"><div><p class="text-gray-500">Quote date</p><p class="mt-1 font-medium text-gray-800 dark:text-gray-200">{{ $quote->quote_date?->format('M d, Y') ?: '-' }}</p></div><div class="text-right"><p class="text-gray-500">Total</p><p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($quote->grand_total) }}</p></div></div>
                </a>
            @empty
                <p class="p-8 text-center text-sm text-gray-500">No published quotations match your search.</p>
            @endforelse
        </div>
    </section>

    <div class="mt-4">{{ $records->links() }}</div>
@endsection
