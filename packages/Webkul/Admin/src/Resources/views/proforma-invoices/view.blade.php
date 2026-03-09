<x-admin::layouts>
    <x-slot:title>
        Proforma {{ $proformaInvoice->proforma_number }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-bold dark:text-white">Proforma {{ $proformaInvoice->proforma_number }}</h1>
                    <p class="text-sm text-gray-500">Status: {{ $proformaInvoice->status }}</p>
                </div>

                <a href="{{ route('admin.proforma_invoices.edit', $proformaInvoice->id) }}" class="primary-button">Edit</a>
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-3 text-sm dark:text-white">
                <div><strong>Customer:</strong> {{ optional($proformaInvoice->organization)->name }}</div>
                <div><strong>Contact:</strong> {{ optional($proformaInvoice->person)->name ?: '-' }}</div>
                <div><strong>Quote:</strong> {{ optional($proformaInvoice->quote)->quote_number ?: '-' }}</div>
                <div><strong>Issue Date:</strong> {{ optional($proformaInvoice->issue_date)->format('Y-m-d') }}</div>
                <div><strong>Due Date:</strong> {{ optional($proformaInvoice->due_date)->format('Y-m-d') ?: '-' }}</div>
                <div><strong>PO Ref:</strong> {{ $proformaInvoice->customer_po_reference ?: '-' }}</div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-lg font-semibold dark:text-white">Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 py-2">Name</th>
                            <th class="px-2 py-2">Code</th>
                            <th class="px-2 py-2">Qty</th>
                            <th class="px-2 py-2">Unit</th>
                            <th class="px-2 py-2">Unit Price</th>
                            <th class="px-2 py-2">Discount</th>
                            <th class="px-2 py-2">Tax</th>
                            <th class="px-2 py-2">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proformaInvoice->items as $item)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-2 py-2">{{ $item->item_name }}</td>
                                <td class="px-2 py-2">{{ $item->item_code ?: '-' }}</td>
                                <td class="px-2 py-2">{{ $item->qty }}</td>
                                <td class="px-2 py-2">{{ $item->unit ?: '-' }}</td>
                                <td class="px-2 py-2">{{ core()->formatBasePrice($item->unit_price, 2) }}</td>
                                <td class="px-2 py-2">{{ core()->formatBasePrice($item->discount_amount, 2) }}</td>
                                <td class="px-2 py-2">{{ core()->formatBasePrice($item->tax_amount, 2) }}</td>
                                <td class="px-2 py-2">{{ core()->formatBasePrice($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Totals</h3>
                <div class="space-y-2">
                    <div class="flex justify-between"><span>Subtotal</span><span>{{ core()->formatBasePrice($proformaInvoice->subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span>Discount</span><span>{{ core()->formatBasePrice($proformaInvoice->discount_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span>Tax</span><span>{{ core()->formatBasePrice($proformaInvoice->tax_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span>Adjustment</span><span>{{ core()->formatBasePrice($proformaInvoice->adjustment_amount, 2) }}</span></div>
                    <div class="flex justify-between font-bold border-t border-gray-200 pt-2 dark:border-gray-700"><span>Grand Total</span><span>{{ core()->formatBasePrice($proformaInvoice->grand_total, 2) }}</span></div>
                    <div class="flex justify-between"><span>Received</span><span>{{ core()->formatBasePrice($proformaInvoice->received_amount, 2) }}</span></div>
                    <div class="flex justify-between font-bold"><span>Remaining</span><span>{{ core()->formatBasePrice($proformaInvoice->remaining_amount, 2) }}</span></div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Receipts</h3>
                <div class="space-y-2">
                    @forelse ($proformaInvoice->receipts as $receipt)
                        <div class="rounded border border-gray-200 p-2 dark:border-gray-700">
                            <div>{{ $receipt->receipt_number ?: '-' }} | {{ optional($receipt->payment_date)->format('Y-m-d') }}</div>
                            <div>{{ core()->formatBasePrice($receipt->amount, 2) }} | {{ $receipt->payment_method ?: '-' }}</div>
                            <div>{{ $receipt->reference_no ?: '-' }}</div>
                        </div>
                    @empty
                        <div class="text-gray-500">No receipts recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts>
