<x-admin::layouts>
    <x-slot:title>{{ $purchaseOrder->po_number }}</x-slot>

    @php
        $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
        $charges = $chargeManager->extract($purchaseOrder->loadMissing('additionalCharges'), 'purchase_order');
        $selectedBillingAddress = trim((string) data_get($purchaseOrder->billing_address, 'address'));
        $selectedShippingAddress = trim((string) data_get($purchaseOrder->shipping_address, 'address'));
        $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))->map(fn ($line) => trim((string) $line))->filter()->values()->all();
        $formatPkr = fn ($value) => 'PKR ' . number_format((float) $value, 2);
        $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge')
            . (($charge['type'] ?? 'value') === 'percentage' ? ' (' . rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ''), '0'), '.') . '%)' : '');
    @endphp

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-bold dark:text-white">Purchase Order {{ $purchaseOrder->po_number }}</div>
                    <div class="text-sm text-gray-500">
                        Vendor-facing procurement document linked to
                        @if ($purchaseOrder->job_order_id && $purchaseOrder->jobOrder)
                            <a class="text-brandColor" href="{{ route('admin.job_orders.view', $purchaseOrder->job_order_id) }}">{{ $purchaseOrder->jobOrder->job_order_number }}</a>
                        @else
                            manual purchasing
                        @endif
                    </div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.goods_receipts.create', ['purchase_order_id' => $purchaseOrder->id]) }}" class="secondary-button">Receive Goods</a>
                    <a href="{{ route('admin.purchase_orders.print', $purchaseOrder->id) }}" class="secondary-button">Print</a>
                    <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder->id) }}" class="primary-button">Edit</a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Vendor</div>
                <div class="font-semibold">
                    @if ($purchaseOrder->organization_id && $purchaseOrder->organization)
                        <a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $purchaseOrder->organization_id) }}">{{ $purchaseOrder->organization->name }}</a>
                    @else
                        -
                    @endif
                </div>
                <div class="mt-1 text-gray-500">{{ optional($purchaseOrder->organization)->phone ?: '' }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Job Order</div>
                <div class="font-semibold">
                    @if ($purchaseOrder->job_order_id && $purchaseOrder->jobOrder)
                        <a class="text-brandColor" href="{{ route('admin.job_orders.view', $purchaseOrder->job_order_id) }}">{{ $purchaseOrder->job_number ?: $purchaseOrder->jobOrder->job_order_number }}</a>
                    @else
                        {{ $purchaseOrder->job_number ?: '-' }}
                    @endif
                </div>
                <div class="mt-1 text-gray-500">
                    Vendor Quote:
                    @if ($purchaseOrder->vendor_quote_id && $purchaseOrder->vendorQuote)
                        <a class="text-brandColor" href="{{ route('admin.vendor_quotes.view', $purchaseOrder->vendor_quote_id) }}">{{ $purchaseOrder->vendorQuote->vendor_quote_number }}</a>
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Commercial</div>
                <div><strong>Status:</strong> {{ \Webkul\Admin\Support\DocumentStatusOptions::label('purchase_order', $purchaseOrder->status) }}</div>
                <div><strong>Payment Term:</strong> {{ $purchaseOrder->payment_term ?: '-' }}</div>
                <div><strong>Shipping:</strong> {{ $purchaseOrder->shipping_method ?: '-' }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Dates</div>
                <div><strong>Created:</strong> {{ optional($purchaseOrder->created_at)->format('Y-m-d') }}</div>
                <div><strong>Expected Receive:</strong> {{ optional($purchaseOrder->expected_receive_date)->format('Y-m-d') ?: '-' }}</div>
                <div><strong>Last Delivery:</strong> {{ optional($purchaseOrder->last_delivery_date)->format('Y-m-d') ?: '-' }}</div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Vendor Address</div>
                <div class="space-y-1 text-sm">
                    <div>{{ optional($purchaseOrder->organization)->name ?: '-' }}</div>
                    @foreach ($addressLines($selectedBillingAddress) as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Ship To Address</div>
                <div class="space-y-1 text-sm">
                    <div>{{ optional($purchaseOrder->organization)->name ?: '-' }}</div>
                    @foreach ($addressLines($selectedShippingAddress) as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-3 text-base font-semibold dark:text-white">Purchase Order Items</div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm dark:text-white">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Material / Item</th>
                            <th class="py-2 text-left">Description</th>
                            <th class="py-2 text-right">Ordered</th>
                            <th class="py-2 text-right">Received</th>
                            <th class="py-2 text-right">Pending</th>
                            <th class="py-2 text-center">Unit</th>
                            <th class="py-2 text-right">Rate</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchaseOrder->items as $item)
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2">
                                    @if ($item->product_id)
                                        <a class="text-brandColor" href="{{ route('admin.products.view', $item->product_id) }}">{{ $item->material_name ?: $item->item ?: '-' }}</a>
                                    @else
                                        {{ $item->material_name ?: $item->item ?: '-' }}
                                    @endif
                                </td>
                                <td class="py-2">{{ $item->description ?: '-' }}</td>
                                <td class="py-2 text-right">{{ rtrim(rtrim(number_format((float) ($item->ordered_quantity ?: $item->quantity ?: 0), 4, '.', ''), '0'), '.') }}</td>
                                <td class="py-2 text-right">{{ rtrim(rtrim(number_format((float) ($item->received_quantity ?: 0), 4, '.', ''), '0'), '.') }}</td>
                                <td class="py-2 text-right">{{ rtrim(rtrim(number_format((float) ($item->pending_quantity ?: 0), 4, '.', ''), '0'), '.') }}</td>
                                <td class="py-2 text-center">{{ $item->unit ?: '-' }}</td>
                                <td class="py-2 text-right">{{ $formatPkr($item->price ?: 0) }}</td>
                                <td class="py-2 text-right">{{ $formatPkr($item->total ?: 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-center text-gray-500">No line items available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Notes</div>
                <div class="text-sm whitespace-pre-line text-gray-600 dark:text-gray-300">{{ $purchaseOrder->notes ?: '-' }}</div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Totals</div>
                <div class="space-y-2 text-sm dark:text-white">
                    <div class="flex items-center justify-between"><span>Sub Total</span><strong>{{ $formatPkr($purchaseOrder->sub_total ?: 0) }}</strong></div>
                    @foreach ($charges as $charge)
                        <div class="flex items-center justify-between"><span>{{ $formatChargeLabel($charge) }}</span><strong>{{ $formatPkr($charge['amount'] ?: 0) }}</strong></div>
                    @endforeach
                    <div class="flex items-center justify-between border-t pt-2 text-base"><span>Grand Total</span><strong>{{ $formatPkr($purchaseOrder->grand_total ?: 0) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Receipts</div>
                <div class="space-y-2 text-sm dark:text-white">
                    @forelse ($purchaseOrder->receipts as $receipt)
                        <div class="rounded border border-gray-200 p-3 dark:border-gray-700">
                            <a class="font-semibold text-brandColor" href="{{ route('admin.goods_receipts.view', $receipt->id) }}">{{ $receipt->goods_receipt_number }}</a>
                            <div class="text-gray-500">{{ optional($receipt->receipt_date)->format('M d, Y') }}</div>
                        </div>
                    @empty
                        <div class="text-gray-500">No receipts posted yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Vendor Payables</div>
                <div class="space-y-2 text-sm dark:text-white">
                    @forelse ($purchaseOrder->payables as $payable)
                        <div class="rounded border border-gray-200 p-3 dark:border-gray-700">
                            <div class="font-semibold">{{ $payable->payable_number }}</div>
                            <div class="text-gray-500">{{ $formatPkr($payable->total_amount) }} | Remaining {{ $formatPkr($payable->remaining_amount) }}</div>
                        </div>
                    @empty
                        <div class="text-gray-500">No vendor payables created yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts>
