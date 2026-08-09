<x-admin::layouts>
    <x-slot:title>Invoice {{ $invoice->invoice_number }}</x-slot>

    @php
        $formatAmount = fn ($value) => core()->formatBasePrice((float) $value, 2);
        $formatQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
        $charges = app(\Webkul\Core\Support\DocumentChargeManager::class)->extract($invoice, 'proforma');
        $totalPaid = (float) $invoice->advance_applied + (float) $invoice->received_amount;
    @endphp

    <div class="flex flex-col gap-4">
        <header class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Invoice {{ $invoice->invoice_number }}</h1>
                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ \Illuminate\Support\Str::headline($invoice->status) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Final billing from proforma {{ $invoice->proformaInvoice?->proforma_number }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if (bouncer()->hasPermission('invoices.edit'))
                        <form method="POST" action="{{ route('admin.invoices.status', $invoice->id) }}" id="invoice-status-form-{{ $invoice->id }}">
                            @csrf
                            <input type="hidden" name="status" value="{{ $invoice->status === 'cancelled' ? 'issued' : 'cancelled' }}">
                            <button
                                type="button"
                                class="secondary-button"
                                onclick="window.confirmInvoiceStatusChange && window.confirmInvoiceStatusChange(event, 'invoice-status-form-{{ $invoice->id }}', '{{ $invoice->status === 'cancelled' ? 'Reopen invoice' : 'Cancel invoice' }}', '{{ $invoice->status === 'cancelled' ? 'Reopen this invoice?' : 'Cancel this invoice? Existing payment records will be retained.' }}', '{{ $invoice->status === 'cancelled' ? 'Reopen' : 'Cancel Invoice' }}')"
                            >
                                {{ $invoice->status === 'cancelled' ? 'Reopen Invoice' : 'Cancel Invoice' }}
                            </button>
                        </form>
                    @endif
                    @if (bouncer()->hasPermission('invoices.edit'))
                        <form method="POST" action="{{ route('admin.invoices.customer_visibility', $invoice->id) }}">@csrf<button type="submit" class="secondary-button">{{ $invoice->customer_visible_at ? 'Unpublish' : 'Publish to customer' }}</button></form>
                    @endif
                    <a class="secondary-button inline-flex items-center gap-2" href="{{ route('admin.invoices.print', $invoice->id) }}"><span class="icon-download"></span> PDF</a>
                    <a class="secondary-button" href="{{ route('admin.proforma_invoices.view', $invoice->proforma_invoice_id) }}">View Proforma</a>
                </div>
            </div>
        </header>

        <div class="grid gap-4 lg:grid-cols-3">
            <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 lg:col-span-2">
                <h2 class="font-semibold text-gray-900 dark:text-white">Invoice details</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div><dt class="text-xs text-gray-500">Customer</dt><dd class="mt-1 font-medium dark:text-white">{{ $invoice->organization?->name ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Issue date</dt><dd class="mt-1 dark:text-white">{{ $invoice->issue_date?->format('Y-m-d') ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Payment terms</dt><dd class="mt-1 dark:text-white">{{ $invoice->payment_term ?: '-' }}</dd></div>
                    <div><dt class="text-xs text-gray-500">Sales owner</dt><dd class="mt-1 dark:text-white">{{ $invoice->salesOwner?->name ?: '-' }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white">Payment summary</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Invoice total</dt><dd class="font-semibold dark:text-white">{{ $formatAmount($invoice->grand_total) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Advance applied</dt><dd class="font-semibold text-green-700">{{ $formatAmount($invoice->advance_applied) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Invoice payments</dt><dd class="font-semibold text-green-700">{{ $formatAmount($invoice->received_amount) }}</dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base dark:border-gray-700"><dt class="font-semibold dark:text-white">Balance due</dt><dd class="font-bold text-brandColor">{{ $formatAmount($invoice->remaining_amount) }}</dd></div>
                </dl>
            </section>
        </div>

        <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="font-semibold text-gray-900 dark:text-white">Invoice items</h2>
            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-200 text-left text-xs text-gray-500 dark:border-gray-700"><th class="px-3 py-2">Item code</th><th class="px-3 py-2">Description</th><th class="px-3 py-2">Color</th><th class="px-3 py-2 text-right">Qty</th><th class="px-3 py-2 text-right">Rate</th><th class="px-3 py-2 text-right">Amount</th></tr></thead>
                    <tbody class="dark:text-gray-200">@foreach($invoice->items as $item)<tr class="border-b border-gray-100 dark:border-gray-800"><td class="px-3 py-3 font-medium">{{ $item->item_code ?: '-' }}</td><td class="px-3 py-3">{{ $item->item_name }}</td><td class="px-3 py-3">{{ $item->color_variant_name ?: '-' }}</td><td class="px-3 py-3 text-right">{{ $formatQty($item->qty) }}</td><td class="px-3 py-3 text-right">{{ $formatAmount($item->unit_price) }}</td><td class="px-3 py-3 text-right font-semibold">{{ $formatAmount($item->line_total) }}</td></tr>@endforeach</tbody>
                </table>
            </div>
            <div class="ml-auto mt-4 w-full max-w-sm space-y-2 text-sm dark:text-gray-200">
                <div class="flex justify-between"><span>Items total</span><span>{{ $formatAmount($invoice->subtotal) }}</span></div>
                @foreach($charges as $charge)<div class="flex justify-between"><span>{{ $charge['name'] }}</span><span>{{ $formatAmount($charge['amount']) }}</span></div>@endforeach
                <div class="flex justify-between border-t border-gray-200 pt-2 font-semibold dark:border-gray-700"><span>Grand total</span><span>{{ $formatAmount($invoice->grand_total) }}</span></div>
                <div class="flex justify-between text-green-700"><span>Total paid</span><span>- {{ $formatAmount($totalPaid) }}</span></div>
                <div class="flex justify-between text-base font-bold"><span>Balance due</span><span>{{ $formatAmount($invoice->remaining_amount) }}</span></div>
            </div>
        </section>

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white">Advance receipts from proforma</h2>
                <div class="mt-3 space-y-2">@forelse($invoice->proformaInvoice?->receipts ?? [] as $receipt)<div class="flex items-center justify-between rounded-md border border-gray-200 p-3 text-sm dark:border-gray-700"><div><p class="font-medium dark:text-white">{{ $receipt->receipt_number }}</p><p class="text-xs text-gray-500">{{ $receipt->payment_date?->format('Y-m-d') }} | {{ $receipt->payment_method ?: 'Method not set' }}</p></div><span class="font-semibold text-green-700">{{ $formatAmount($receipt->amount) }}</span></div>@empty<p class="text-sm text-gray-500">No advance was recorded.</p>@endforelse</div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white">Final invoice payments</h2>
                <div class="mt-3 space-y-2">@forelse($invoice->receipts as $receipt)<div class="flex items-center justify-between rounded-md border border-gray-200 p-3 text-sm dark:border-gray-700"><div><p class="font-medium dark:text-white">{{ $receipt->receipt_number }}</p><p class="text-xs text-gray-500">{{ $receipt->payment_date?->format('Y-m-d') }} | {{ $receipt->payment_method ?: 'Method not set' }}</p></div><div class="flex items-center gap-3"><span class="font-semibold text-green-700">{{ $formatAmount($receipt->amount) }}</span>@if(bouncer()->hasPermission('invoices.edit'))<form method="POST" action="{{ route('admin.invoices.receipts.delete', [$invoice->id, $receipt->id]) }}" id="invoice-receipt-delete-{{ $receipt->id }}">@csrf @method('DELETE')<button type="button" class="icon-delete text-lg text-red-600" title="Delete payment" onclick="window.confirmInvoiceReceiptDelete && window.confirmInvoiceReceiptDelete(event, 'invoice-receipt-delete-{{ $receipt->id }}')"></button></form>@endif</div></div>@empty<p class="text-sm text-gray-500">No final-invoice payments recorded yet.</p>@endforelse</div>
            </section>
        </div>

        @if ($invoice->remaining_amount > 0 && $invoice->status !== 'cancelled' && bouncer()->hasPermission('invoices.create'))
            <section class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="font-semibold text-gray-900 dark:text-white">Record invoice payment</h2>
                <x-admin::form :action="route('admin.invoices.receipts.store', $invoice->id)" method="POST">
                    <div class="mt-4 grid gap-3 md:grid-cols-4">
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Payment Date</x-admin::form.control-group.label><x-admin::form.control-group.control type="date" name="payment_date" rules="required" value="{{ now()->toDateString() }}" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Amount</x-admin::form.control-group.label><x-admin::form.control-group.control type="number" name="amount" step="0.001" min="0.001" max="{{ $invoice->remaining_amount }}" rules="required" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Payment Method</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="payment_method" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Reference No</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="reference_no" /></x-admin::form.control-group>
                        <button type="submit" class="primary-button w-max">Record Payment</button>
                    </div>
                </x-admin::form>
            </section>
        @endif
    </div>

    @pushOnce('scripts')
        <script>
            window.confirmInvoiceStatusChange = function (event, formId, title, message, agreeLabel) {
                event.preventDefault();

                var form = document.getElementById(formId);

                if (! form) {
                    return;
                }

                window.emitter.emit('open-confirm-modal', {
                    title: title,
                    message: message,
                    options: {
                        btnDisagree: 'Cancel',
                        btnAgree: agreeLabel,
                    },
                    agree: function () {
                        form.submit();
                    },
                });
            };

            window.confirmInvoiceReceiptDelete = function (event, formId) {
                event.preventDefault();

                var form = document.getElementById(formId);

                if (! form) {
                    return;
                }

                window.emitter.emit('open-confirm-modal', {
                    title: 'Delete payment',
                    message: 'Are you sure you want to delete this invoice payment?',
                    options: {
                        btnDisagree: 'Cancel',
                        btnAgree: 'Delete',
                    },
                    agree: function () {
                        form.submit();
                    },
                });
            };
        </script>
    @endPushOnce
</x-admin::layouts>
