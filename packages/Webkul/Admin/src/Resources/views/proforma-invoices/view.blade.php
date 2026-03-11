<x-admin::layouts>
    <x-slot:title>
        Proforma {{ $proformaInvoice->proforma_number }}
    </x-slot>

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold dark:text-white">Proforma {{ $proformaInvoice->proforma_number }}</h1>
                    <p class="text-sm text-gray-500">Status: {{ $proformaInvoice->status }}</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.proforma_invoices.print', $proformaInvoice->id) }}" class="secondary-button">Print</a>
                    <a href="{{ route('admin.proforma_invoices.edit', $proformaInvoice->id) }}" class="primary-button">Edit</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 text-sm dark:text-white md:grid-cols-3">
                <div><strong>Linked Quote:</strong> {{ optional($proformaInvoice->quote)->quote_number ? 'Q' . ltrim((string) optional($proformaInvoice->quote)->quote_number, 'Q') : '-' }}</div>
                <div><strong>Customer:</strong> {{ optional($proformaInvoice->organization)->name }}</div>
                <div><strong>Sales Owner:</strong> {{ optional($proformaInvoice->salesOwner)->name ?: '-' }}</div>
                <div><strong>Issue Date:</strong> {{ optional($proformaInvoice->issue_date)->format('Y-m-d') }}</div>
                <div><strong>Payment Term:</strong> {{ $proformaInvoice->payment_term ?: '-' }}</div>
                <div><strong>Source Type:</strong> {{ $proformaInvoice->source_type ?: '-' }}</div>
                <div><strong>Created By:</strong> {{ optional($proformaInvoice->salesOwner)->name ?: '-' }}</div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Billing Address</h3>
                <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ data_get($proformaInvoice->billing_address, 'address', '-') }}</p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Shipping Address</h3>
                <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ data_get($proformaInvoice->shipping_address, 'address', '-') }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-3 flex flex-col gap-1">
                <p class="text-base font-semibold text-gray-800 dark:text-white">Quote Items</p>
                <p class="text-sm text-gray-600 dark:text-white">Add Product Request for this quote.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-2 py-2">Product Name</th>
                            <th class="px-2 py-2 text-center">Image</th>
                            <th class="px-2 py-2 text-center">Color Variant</th>
                            <th class="px-2 py-2 text-center">Quantity</th>
                            <th class="px-2 py-2 text-center">Price</th>
                            <th class="px-2 py-2 text-center">Amount</th>
                            <th class="px-2 py-2 text-center">Discount</th>
                            <th class="px-2 py-2 text-center">Tax</th>
                            <th class="px-2 py-2 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proformaInvoice->items as $item)
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <td class="px-2 py-2">{{ ($item->item_code ? $item->item_code . ' - ' : '') . $item->item_name }}</td>
                                <td class="px-2 py-2 text-center">
                                    @if ($item->preview_image)
                                        <img src="{{ $item->preview_image }}" class="mx-auto h-12 w-12 rounded border border-gray-200 object-cover" alt="preview">
                                    @else
                                        <span class="text-xs text-gray-500">No image</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-center">{{ $item->color_variant_name ?: 'No Color' }}</td>
                                <td class="px-2 py-2 text-center">{{ $item->qty }}</td>
                                <td class="px-2 py-2 text-center">{{ core()->formatBasePrice($item->unit_price) }}</td>
                                <td class="px-2 py-2 text-center">{{ core()->formatBasePrice($item->qty * $item->unit_price) }}</td>
                                <td class="px-2 py-2 text-center">{{ core()->formatBasePrice($item->discount_amount) }}</td>
                                <td class="px-2 py-2 text-center">{{ core()->formatBasePrice($item->tax_amount) }}</td>
                                <td class="px-2 py-2 text-center">{{ core()->formatBasePrice($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Totals</h3>
                <div class="space-y-2">
                    <div class="flex justify-between"><span>Subtotal</span><span>{{ core()->formatBasePrice($proformaInvoice->subtotal) }}</span></div>
                    <div class="flex justify-between"><span>Discount</span><span>{{ core()->formatBasePrice($proformaInvoice->discount_amount) }}</span></div>
                    <div class="flex justify-between"><span>Tax</span><span>{{ core()->formatBasePrice($proformaInvoice->tax_amount) }}</span></div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 font-bold dark:border-gray-700"><span>Grand Total</span><span>{{ core()->formatBasePrice($proformaInvoice->grand_total) }}</span></div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Payment Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between"><span>Advance Received</span><span>{{ core()->formatBasePrice($proformaInvoice->received_amount) }}</span></div>
                    <div class="flex justify-between font-bold"><span>Remaining Balance</span><span>{{ core()->formatBasePrice($proformaInvoice->remaining_amount) }}</span></div>
                </div>

                <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                    <x-admin::form :action="route('admin.proforma_invoices.receipts.store', $proformaInvoice->id)" method="POST">
                        <div class="grid gap-3">
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">Receive Amount</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="number" name="amount" step="0.0001" min="0.0001" rules="required" />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label class="required">Payment Date</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="date" name="payment_date" value="{{ now()->toDateString() }}" rules="required" />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>Payment Method</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="payment_method" />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>Reference No</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="text" name="reference_no" />
                            </x-admin::form.control-group>
                            <x-admin::form.control-group class="!mb-0">
                                <x-admin::form.control-group.label>Notes</x-admin::form.control-group.label>
                                <x-admin::form.control-group.control type="textarea" name="notes" />
                            </x-admin::form.control-group>
                            <button type="submit" class="primary-button w-max">Save Receipt</button>
                        </div>
                    </x-admin::form>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
                <h3 class="mb-2 text-lg font-semibold">Notes / Terms</h3>
                <div class="space-y-3">
                    <div>
                        <div class="font-semibold">Notes</div>
                        <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $proformaInvoice->notes ?: '-' }}</p>
                    </div>
                    <div>
                        <div class="font-semibold">Terms</div>
                        <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $proformaInvoice->terms ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 text-sm dark:text-white">
            <h3 class="mb-3 text-lg font-semibold">Receipt History</h3>
            <div class="space-y-3">
                @forelse ($proformaInvoice->receipts as $receipt)
                    <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                        <div class="grid gap-2 md:grid-cols-4">
                            <div><strong>Receipt #:</strong> {{ $receipt->receipt_number ?: '-' }}</div>
                            <div><strong>Date:</strong> {{ optional($receipt->payment_date)->format('Y-m-d') }}</div>
                            <div><strong>Amount:</strong> {{ core()->formatBasePrice($receipt->amount) }}</div>
                            <div><strong>Method:</strong> {{ $receipt->payment_method ?: '-' }}</div>
                            <div><strong>Reference:</strong> {{ $receipt->reference_no ?: '-' }}</div>
                            <div><strong>Received By:</strong> {{ optional($receipt->receivedBy)->name ?: '-' }}</div>
                            <div class="md:col-span-2"><strong>Notes:</strong> {{ $receipt->notes ?: '-' }}</div>
                        </div>

                    </div>
                @empty
                    <div class="text-gray-500">No receipts recorded yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin::layouts>
