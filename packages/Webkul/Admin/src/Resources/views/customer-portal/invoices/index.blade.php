@extends('admin::customer-portal.layouts.app', ['title' => 'Final Invoices', 'subtitle' => 'Review final bills, advances, payments, and outstanding balances.'])

@section('content')
    @include('admin::customer-portal.partials.list-toolbar', [
        'route' => 'customer_portal.invoices.index',
        'placeholder' => 'Search invoice number, subject, or PO reference',
        'statuses' => ['issued' => 'Issued', 'partially_paid' => 'Partially paid', 'paid' => 'Paid', 'cancelled' => 'Cancelled'],
    ])

    <section class="portal-card overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-800">
            <div><p class="portal-kicker">Final billing</p><p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $records->total() }} invoice{{ $records->total() === 1 ? '' : 's' }}</p></div>
            <span class="portal-icon-box"><i class="icon-note text-xl"></i></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-b border-gray-200 bg-slate-50 text-xs font-semibold uppercase text-gray-500 dark:border-gray-800 dark:bg-gray-950"><tr><th class="px-4 py-3">Invoice</th><th class="px-4 py-3">Issued</th><th class="px-4 py-3">Status</th><th class="px-4 py-3 text-right">Advance</th><th class="px-4 py-3 text-right">Paid after invoice</th><th class="px-4 py-3 text-right">Balance</th><th class="px-4 py-3 text-right">Total</th><th class="w-12 px-4 py-3"></th></tr></thead>
                <tbody class="divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    @forelse($records as $invoice)
                        <tr class="portal-row"><td class="px-4 py-3.5"><a class="font-semibold text-brandColor hover:underline" href="{{ route('customer_portal.invoices.view', $invoice->id) }}">{{ $invoice->invoice_number }}</a><p class="mt-0.5 text-xs text-gray-500">{{ $invoice->subject ?: 'Final invoice' }}</p></td><td class="px-4 py-3.5 dark:text-gray-300">{{ $invoice->issue_date?->format('M d, Y') ?: '-' }}</td><td class="px-4 py-3.5">@include('admin::customer-portal.partials.status-badge', ['status' => $invoice->status])</td><td class="px-4 py-3.5 text-right dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($invoice->advance_applied) }}</td><td class="px-4 py-3.5 text-right dark:text-gray-300">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($invoice->received_amount) }}</td><td class="px-4 py-3.5 text-right font-semibold text-amber-700">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($invoice->remaining_amount) }}</td><td class="px-4 py-3.5 text-right font-semibold dark:text-white">{{ \Webkul\Admin\Http\Controllers\CustomerPortal\PortalController::money($invoice->grand_total) }}</td><td class="px-4 py-3.5"><a href="{{ route('customer_portal.invoices.view', $invoice->id) }}" class="inline-flex rounded p-1.5 text-gray-400 hover:bg-gray-100 hover:text-brandColor dark:hover:bg-gray-800" aria-label="View {{ $invoice->invoice_number }}"><span class="icon-right-arrow text-xl"></span></a></td></tr>
                    @empty
                        @include('admin::customer-portal.partials.table-empty', ['colspan' => 8, 'message' => 'No published final invoices match your search.'])
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="mt-4">{{ $records->links() }}</div>
@endsection
