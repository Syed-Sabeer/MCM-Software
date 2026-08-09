<x-admin::layouts>
    <x-slot:title>Create Job Order</x-slot>

    @php
        $formatQty = fn ($value) => number_format((float) $value, 0, '.', '');
        $formatAmount = fn ($value) => number_format((float) $value, 3, '.', ',');
    @endphp

    <x-admin::form :action="route('admin.job_orders.store')" method="POST">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <div class="text-xl font-bold dark:text-white">Create Job Order</div>
                    <div class="text-sm text-gray-500">Source Proforma: {{ $proformaInvoice->proforma_number }}</div>
                </div>

                <button class="primary-button">Create Job Order</button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Job Order #</label>
                            <input type="text" name="job_order_number" value="{{ old('job_order_number', $nextJobOrderNumber) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            @include('admin::partials.document-status-picker', [
                                'type' => 'job_order',
                                'name' => 'status',
                                'selected' => 'open',
                                'selectClass' => 'w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white',
                            ])
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Issue Date</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">ETD</label>
                            <input type="date" name="required_delivery_date" value="{{ old('required_delivery_date', optional($proformaInvoice->due_date)->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium dark:text-white">Remarks</label>
                        <textarea name="remarks" rows="4" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('remarks', $proformaInvoice->notes) }}</textarea>
                    </div>

                    <input type="hidden" name="proforma_invoice_id" value="{{ $proformaInvoice->id }}">
                    <input type="hidden" name="organization_id" value="{{ $proformaInvoice->organization_id }}">
                    <input type="hidden" name="person_id" value="{{ $proformaInvoice->person_id }}">
                    <input type="hidden" name="customer_po_reference" value="{{ $proformaInvoice->customer_po_reference }}">
                    <input type="hidden" name="subject" value="{{ $proformaInvoice->subject }}">
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    <div class="space-y-2">
                        <div><strong>Customer:</strong> {{ optional($proformaInvoice->organization)->name }}</div>
                        <div><strong>Linked Quote:</strong> {{ optional($proformaInvoice->quote)->quote_number ? 'Q' . optional($proformaInvoice->quote)->quote_number : '-' }}</div>
                        <div><strong>Grand Total:</strong> {{ $formatAmount($proformaInvoice->grand_total ?: 0) }}</div>
                        <div><strong>Advance Received:</strong> {{ $formatAmount($proformaInvoice->received_amount ?: 0) }}</div>
                        <div><strong>Remaining Balance:</strong> {{ $formatAmount($proformaInvoice->remaining_amount ?: 0) }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Job Order Items</div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Image</th>
                            <th class="py-2 text-left">Item Code</th>
                            <th class="py-2 text-left">Description</th>
                            <th class="py-2 text-left">Color</th>
                            <th class="py-2 text-left">Qty </th>
                            <th class="py-2 text-right">Rate</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proformaInvoice->items as $index => $item)
                            @php $resolvedUnit = $item->unit ?: 'PCS'; @endphp
                            <tr class="border-b dark:border-gray-800 dark:text-white">
                                <td class="py-2">
                                    @if ($item->preview_image)
                                        <img src="{{ $item->preview_image }}" alt="preview" class="h-12 w-12 rounded border border-gray-200 object-cover dark:border-gray-700">
                                    @else
                                        <span class="text-xs text-gray-500">No image</span>
                                    @endif
                                </td>
                                <td class="py-2 font-medium">
                                    <input type="hidden" name="items[{{ $index }}][proforma_invoice_item_id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                    <input type="hidden" name="items[{{ $index }}][item_name]" value="{{ $item->item_name }}">
                                    <input type="hidden" name="items[{{ $index }}][item_code]" value="{{ $item->item_code }}">
                                    <input type="hidden" name="items[{{ $index }}][description]" value="{{ $item->description }}">
                                    <input type="hidden" name="items[{{ $index }}][preview_image]" value="{{ $item->preview_image }}">
                                    <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $formatQty($item->qty ?: 0) }}">
                                    <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $resolvedUnit }}">
                                    <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ number_format((float) ($item->unit_price ?: 0), 3, '.', '') }}">
                                    <input type="hidden" name="items[{{ $index }}][line_total]" value="{{ number_format((float) ($item->line_total ?: 0), 3, '.', '') }}">
                                    {{ $item->item_code ?: '-' }}
                                </td>
                                <td class="py-2">{{ $item->item_name ?: '-' }}</td>
                                <td class="py-2">{{ $item->color_variant_name ?: '-' }}</td>
                                 <td class="py-2">{{ $item->qty ? number_format($item->qty, 0) : '-' }}</td>

                                <td class="py-2 text-right">{{ $formatAmount($item->unit_price ?: 0) }}</td>
                                <td class="py-2 text-right font-medium">{{ $formatAmount($item->line_total ?: 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
