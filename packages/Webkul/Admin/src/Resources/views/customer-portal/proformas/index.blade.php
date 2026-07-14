@extends('admin::customer-portal.layouts.app', ['title' => 'Proforma Invoices', 'subtitle' => 'Review issued invoices, payment receipts, and outstanding balances.'])

@section('content')
    @include('admin::customer-portal.partials.list-toolbar', [
        'route' => 'customer_portal.proformas.index',
        'placeholder' => 'Search invoice number, subject, or PO reference',
        'statuses' => ['draft' => 'Draft', 'issued' => 'Issued', 'partially_paid' => 'Partially paid', 'paid' => 'Paid', 'cancelled' => 'Cancelled'],
    ])

    <section class="portal-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
            <div><p class="portal-kicker">Billing documents</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $records->total() }} proforma invoice{{ $records->total() === 1 ? '' : 's' }}</p></div>
            <span class="portal-icon-box"><i class="icon-dollar text-xl"></i></span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-300"><tr><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Issued</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Received</th><th class="px-4 py-3 text-right">Balance</th><th class="px-4 py-3 text-right">Total</th><th class="w-12 px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse($records as $proforma)
                        <tr class="portal-row">
                            <td class="px-4 py-3.5"><a class="font-semibold text-brandColor hover:underline" href="{{ route('customer_portal.proformas.view', $proforma->id) }}">{{ $proforma->proforma_number }}</a><p class="mt-0.5 max-w-[300px] truncate text-xs text-gray-500">{{ $proforma->subject ?: ($proforma->customer_po_reference ? 'PO '.$proforma->customer_po_reference : 'Proforma invoice') }}</p></td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $proforma->issue_date?->format('M d, Y') ?: '-' }}</td>
                            <td class="px-4 py-3.5">@include('admin::customer-portal.partials.status-badge', ['status' => $proforma->status])</td>
                            <td class="px-4 py-3.5 text-right text-gray-700 dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->received_amount) }}</td>
                            <td class="px-4 py-3.5 text-right font-semibold text-amber-700 dark:text-amber-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->remaining_amount) }}</td>
                            <td class="px-4 py-3.5 text-right font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->grand_total) }}</td>
                            <td class="px-4 py-3.5"><a href="{{ route('customer_portal.proformas.view', $proforma->id) }}" class="inline-flex rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-brandColor dark:hover:bg-gray-800" aria-label="View {{ $proforma->proforma_number }}"><span class="icon-right-arrow text-xl"></span></a></td>
                        </tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 7, 'message' => 'No published proforma invoices match your search.'])
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-gray-200 md:hidden dark:divide-gray-800">
            @forelse($records as $proforma)
                <a href="{{ route('customer_portal.proformas.view', $proforma->id) }}" class="portal-row block p-4">
                    <div class="flex items-start justify-between gap-3"><div><p class="font-semibold text-brandColor">{{ $proforma->proforma_number }}</p><p class="mt-1 text-xs text-gray-500">{{ $proforma->issue_date?->format('M d, Y') ?: 'Date unavailable' }}</p></div>@include('admin::customer-portal.partials.status-badge', ['status' => $proforma->status])</div>
                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm"><div><p class="text-xs text-gray-500">Balance</p><p class="mt-1 font-semibold text-amber-700 dark:text-amber-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->remaining_amount) }}</p></div><div class="text-right"><p class="text-xs text-gray-500">Total</p><p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($proforma->grand_total) }}</p></div></div>
                </a>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">No published proforma invoices match your search.</div>
            @endforelse
        </div>
    </section>

    <div class="mt-4">{{ $records->links() }}</div>
@endsection
