<x-admin::layouts>
    <x-slot:title>Proforma {{ $proformaInvoice->proforma_number }}</x-slot>

    @php
        $organization = $proformaInvoice->organization;
        $salesPerson = $proformaInvoice->salesOwner;
        $sourceQuote = $proformaInvoice->quote;

        $billTo = array_filter([
            $organization?->name,
            data_get($proformaInvoice->billing_address, 'address') ?: $organization?->billing_street ?: $organization?->shipping_street,
            trim(implode(', ', array_filter([
                $organization?->billing_city ?: $organization?->shipping_city,
                $organization?->billing_state ?: $organization?->shipping_state,
                $organization?->billing_postcode ?: $organization?->shipping_postcode,
                $organization?->billing_country ?: $organization?->shipping_country,
            ]))),
            $organization?->phone ? 'Phone: ' . $organization->phone : null,
            data_get($organization, 'email') ? 'Email: ' . data_get($organization, 'email') : null,
        ]);

        $shipTo = array_filter([
            $organization?->name,
            data_get($proformaInvoice->shipping_address, 'address') ?: $organization?->shipping_street ?: $organization?->billing_street,
            trim(implode(', ', array_filter([
                $organization?->shipping_city ?: $organization?->billing_city,
                $organization?->shipping_state ?: $organization?->billing_state,
                $organization?->shipping_postcode ?: $organization?->billing_postcode,
                $organization?->shipping_country ?: $organization?->billing_country,
            ]))),
            $organization?->phone ? 'Phone: ' . $organization->phone : null,
        ]);

        $formatQty = fn ($value) => number_format((float) $value, 0, '.', '');
        $formatAmount = fn ($value) => number_format((float) $value, 3, '.', ',');
        $itemsTotal = (float) ($proformaInvoice->subtotal ?: 0);
        $tariffAmount = (float) ($proformaInvoice->tax_amount ?: 0);
        $freightAmount = (float) ($proformaInvoice->adjustment_amount ?: 0);
        $subTotalWithCharges = $itemsTotal + $tariffAmount + $freightAmount;
        $advanceReceived = (float) ($proformaInvoice->received_amount ?: 0);
        $remainingBalance = (float) ($proformaInvoice->remaining_amount ?: 0);
    @endphp

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold dark:text-white">Proforma {{ $proformaInvoice->proforma_number }}</h1>
                    <p class="text-sm text-gray-500">Commercial confirmation linked to quote {{ optional($proformaInvoice->quote)->quote_number ? 'Q' . ltrim((string) optional($proformaInvoice->quote)->quote_number, 'Q') : '-' }}</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.proforma_invoices.print', $proformaInvoice->id) }}" class="secondary-button">Print</a>
                    <a href="{{ route('admin.job_orders.create', ['proforma_invoice_id' => $proformaInvoice->id]) }}" class="secondary-button">Create Job Order</a>
                    <a href="{{ route('admin.proforma_invoices.edit', $proformaInvoice->id) }}" class="primary-button">Edit</a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Document Summary</div>
                <div class="grid gap-3 text-sm md:grid-cols-2">
                    <div><strong>Proforma #:</strong> {{ $proformaInvoice->proforma_number }}</div>
                    <div><strong>Quote #:</strong> {{ optional($proformaInvoice->quote)->quote_number ? 'Q' . ltrim((string) optional($proformaInvoice->quote)->quote_number, 'Q') : '-' }}</div>
                    <div><strong>Issue Date:</strong> {{ optional($proformaInvoice->issue_date)->format('Y-m-d') ?: '-' }}</div>
                    <div><strong>Status:</strong> {{ ucfirst((string) ($proformaInvoice->status ?: 'draft')) }}</div>
                    <div><strong>Sales Owner:</strong> {{ $salesPerson?->name ?: '-' }}</div>
                    <div><strong>Payment Term:</strong> {{ $proformaInvoice->payment_term ?: '-' }}</div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Commercial Details</div>
                <div class="grid gap-3 text-sm md:grid-cols-2">
                    <div><strong>Shipping Method:</strong> {{ $proformaInvoice->shipping_method ?: $sourceQuote?->shipping_method ?: '-' }}</div>
                    <div><strong>Production Time:</strong> {{ $proformaInvoice->production_time ?: $sourceQuote?->production_time ?: '-' }}</div>
                    <div><strong>Transit Time:</strong> {{ $proformaInvoice->transit_time ?: $sourceQuote?->transit_time ?: '-' }}</div>
                    <div><strong>ETD:</strong> {{ $proformaInvoice->etd ? $proformaInvoice->etd->format('Y-m-d') : (optional($sourceQuote?->etd)->format('Y-m-d') ?: '-') }}</div>
                    <div><strong>ETA:</strong> {{ $proformaInvoice->eta ? $proformaInvoice->eta->format('Y-m-d') : (optional($sourceQuote?->eta)->format('Y-m-d') ?: '-') }}</div>
                    <div><strong>Advance Received:</strong> {{ $formatAmount($advanceReceived) }}</div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Bill To</div>
                <div class="space-y-1 text-sm">
                    @foreach ($billTo as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <div class="mb-3 text-base font-semibold">Ship To</div>
                <div class="space-y-1 text-sm">
                    @foreach ($shipTo as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-4">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Proforma Invoice Items</div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm dark:text-white">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="py-2 text-left">Image</th>
                                <th class="py-2 text-left">Item Code</th>
                                <th class="py-2 text-left">Description</th>
                                <th class="py-2 text-left">Color</th>
                                <th class="py-2 text-right">Qty</th>
                                <th class="py-2 text-left">Unit</th>
                                <th class="py-2 text-right">Rate</th>
                                <th class="py-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proformaInvoice->items as $item)
                                <tr class="border-b dark:border-gray-800">
                                    <td class="py-2">
                                        @if ($item->preview_image)
                                            <img src="{{ $item->preview_image }}" alt="preview" class="h-12 w-12 rounded border border-gray-200 object-cover dark:border-gray-700">
                                        @else
                                            <span class="text-xs text-gray-500">No image</span>
                                        @endif
                                    </td>
                                    <td class="py-2 font-medium">{{ $item->item_code ?: '-' }}</td>
                                    <td class="py-2">{{ $item->item_name ?: '-' }}</td>
                                    <td class="py-2">{{ $item->color_variant_name ?: '-' }}</td>
                                    <td class="py-2 text-right">{{ $formatQty($item->qty ?: 0) }}</td>
                                    <td class="py-2">{{ $item->unit ?: 'PCS' }}</td>
                                    <td class="py-2 text-right">{{ $formatAmount($item->unit_price ?: 0) }}</td>
                                    <td class="py-2 text-right font-medium">{{ $formatAmount($item->line_total ?: 0) }}</td>
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
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    <div class="mb-3 text-base font-semibold">Totals</div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>Items Total</span><span>{{ $formatAmount($itemsTotal) }}</span></div>
                        <div class="flex justify-between"><span>Tarrifs</span><span>{{ $formatAmount($tariffAmount) }}</span></div>
                        <div class="flex justify-between"><span>Freight</span><span>{{ $formatAmount($freightAmount) }}</span></div>
                        <div class="flex justify-between"><span>Subtotal</span><span>{{ $formatAmount($subTotalWithCharges) }}</span></div>
                        <div class="flex justify-between"><span>Advance Received</span><span>{{ $formatAmount($advanceReceived) }}</span></div>
                        <div class="flex justify-between border-t pt-2 text-base font-bold"><span>Grand Total</span><span>{{ $formatAmount($remainingBalance) }}</span></div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    <div class="mb-3 text-base font-semibold">Receipt History</div>
                    <div class="space-y-3 text-sm">
                        @forelse ($proformaInvoice->receipts as $receipt)
                            <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <div class="font-semibold">{{ $receipt->receipt_number ?: 'Receipt' }}</div>
                                        <div class="text-gray-500">{{ optional($receipt->payment_date)->format('Y-m-d') }} | {{ $receipt->payment_method ?: 'Method not set' }}</div>
                                    </div>
                                    <div class="text-right font-semibold">{{ $formatAmount($receipt->amount ?: 0) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-gray-500">No advance receipts recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    <div class="mb-3 text-base font-semibold">Remarks</div>
                    <div class="text-sm whitespace-pre-line">{{ $proformaInvoice->notes ?: '-' }}</div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    <div class="mb-3 text-base font-semibold">Terms & Conditions</div>
                    <div class="text-sm whitespace-pre-line">{{ $proformaInvoice->terms ?: '-' }}</div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold dark:text-white">Add Advance Receipt</h3>
                <x-admin::form :action="route('admin.proforma_invoices.receipts.store', $proformaInvoice->id)" method="POST">
                    <div class="grid gap-3 md:grid-cols-2">
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Payment Date</x-admin::form.control-group.label><x-admin::form.control-group.control type="date" name="payment_date" rules="required" value="{{ now()->toDateString() }}" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Amount</x-admin::form.control-group.label><x-admin::form.control-group.control type="number" name="amount" step="0.001" min="0.001" rules="required" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Payment Method</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="payment_method" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Reference No</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="reference_no" /></x-admin::form.control-group>
                        <div class="md:col-span-2"><x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Notes</x-admin::form.control-group.label><x-admin::form.control-group.control type="textarea" name="notes" /></x-admin::form.control-group></div>
                        <button type="submit" class="primary-button w-max">Record Receipt</button>
                    </div>
                </x-admin::form>
            </div>
        </div>
    </div>
</x-admin::layouts>

