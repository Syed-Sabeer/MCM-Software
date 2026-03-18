<x-admin::layouts>
    <x-slot:title>Edit Job Order</x-slot>

    @php
        $formatQty = fn ($value) => number_format((float) $value, 0, '.', '');
        $formatAmount = fn ($value) => number_format((float) $value, 3, '.', ',');
    @endphp

    <x-admin::form :action="route('admin.job_orders.update', $jobOrder->id)" method="PUT">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <div class="text-xl font-bold dark:text-white">Edit Job Order</div>
                    <div class="text-sm text-gray-500">Source Proforma: {{ optional($jobOrder->proformaInvoice)->proforma_number ?: '-' }}</div>
                </div>

                <button class="primary-button">Update Job Order</button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Job Order #</label>
                            <input type="text" name="job_order_number" value="{{ old('job_order_number', $jobOrder->job_order_number) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Status</label>
                            <select name="status" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @foreach (['draft', 'open', 'in_progress', 'ready_to_ship', 'completed', 'closed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $jobOrder->status) === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Issue Date</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', optional($jobOrder->issue_date)->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Required Delivery Date</label>
                            <input type="date" name="required_delivery_date" value="{{ old('required_delivery_date', optional($jobOrder->required_delivery_date)->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="mb-1 block text-sm font-medium dark:text-white">Remarks</label>
                        <textarea name="remarks" rows="4" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('remarks', $jobOrder->remarks) }}</textarea>
                    </div>

                    <input type="hidden" name="proforma_invoice_id" value="{{ $jobOrder->proforma_invoice_id }}">
                    <input type="hidden" name="organization_id" value="{{ $jobOrder->organization_id }}">
                    <input type="hidden" name="person_id" value="{{ $jobOrder->person_id }}">
                    <input type="hidden" name="customer_po_reference" value="{{ $jobOrder->customer_po_reference }}">
                    <input type="hidden" name="subject" value="{{ $jobOrder->subject }}">
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                    <div class="space-y-2">
                        <div><strong>Customer:</strong> {{ optional($jobOrder->organization)->name }}</div>
                        <div><strong>Linked Quote:</strong> {{ optional(optional($jobOrder->proformaInvoice)->quote)->quote_number ? 'Q' . optional(optional($jobOrder->proformaInvoice)->quote)->quote_number : '-' }}</div>
                        <div><strong>Grand Total:</strong> {{ $formatAmount(optional($jobOrder->proformaInvoice)->grand_total ?: 0) }}</div>
                        <div><strong>Advance Received:</strong> {{ $formatAmount(optional($jobOrder->proformaInvoice)->received_amount ?: 0) }}</div>
                        <div><strong>Remaining Balance:</strong> {{ $formatAmount(optional($jobOrder->proformaInvoice)->remaining_amount ?: 0) }}</div>
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
                            <th class="py-2 text-left">Qty</th>
                            <th class="py-2 text-right">Rate</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobOrder->items as $index => $item)
                            @php
                                $resolvedUnit = $item->unit ?: 'PCS';
                                $proformaItems = optional($jobOrder->proformaInvoice)->items;
                                $proformaItem = $proformaItems?->firstWhere('id', $item->proforma_invoice_item_id);

                                if (! $proformaItem && $item->product_id) {
                                    $proformaItem = $proformaItems?->firstWhere('product_id', $item->product_id);
                                }

                                $resolvedPreviewImage = optional($proformaItem)->preview_image;

                                if (! $resolvedPreviewImage && optional($item->product)->cover_image) {
                                    $resolvedPreviewImage = secure_url('public/storage/'.optional($item->product)->cover_image);
                                }

                                $resolvedItemName = $item->item_name ?: optional($proformaItem)->item_name;

                                $resolvedColorName = optional($proformaItem)->color_variant_name ?: '-';

                                $resolvedQty = $item->qty;

                                if ($resolvedQty === null || $resolvedQty === '') {
                                    $resolvedQty = optional($proformaItem)->qty;
                                }

                                $resolvedRate = $item->unit_price;

                                if ($resolvedRate === null || $resolvedRate === '') {
                                    $resolvedRate = optional($proformaItem)->unit_price;
                                }

                                $resolvedAmount = $item->line_total;

                                if ($resolvedAmount === null || $resolvedAmount === '') {
                                    $resolvedAmount = optional($proformaItem)->line_total;
                                }

                                if (($resolvedAmount === null || $resolvedAmount === '') && $resolvedQty !== null && $resolvedRate !== null) {
                                    $resolvedAmount = (float) $resolvedQty * (float) $resolvedRate;
                                }

                                $resolvedQty = (float) ($resolvedQty ?? 0);
                                $resolvedRate = (float) ($resolvedRate ?? 0);
                                $resolvedAmount = (float) ($resolvedAmount ?? 0);
                            @endphp
                            <tr class="border-b dark:border-gray-800 dark:text-white">
                                <td class="py-2 font-medium">
                                    @if ($resolvedPreviewImage)
                                        <img src="{{ $resolvedPreviewImage }}" alt="preview" class="h-12 w-12 rounded border border-gray-200 object-cover dark:border-gray-700">
                                    @else
                                        <span class="text-xs text-gray-500">No image</span>
                                    @endif
                                </td>
                                <td class="py-2 font-medium">
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $index }}][proforma_invoice_item_id]" value="{{ $item->proforma_invoice_item_id }}">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                    <input type="hidden" name="items[{{ $index }}][item_name]" value="{{ $resolvedItemName }}">
                                    <input type="hidden" name="items[{{ $index }}][description]" value="{{ $item->description }}">
                                    <input type="hidden" name="items[{{ $index }}][preview_image]" value="{{ $resolvedPreviewImage }}">
                                    <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ number_format($resolvedRate, 3, '.', '') }}">
                                    <input type="hidden" name="items[{{ $index }}][line_total]" value="{{ number_format($resolvedAmount, 3, '.', '') }}">
                                    <input type="hidden" name="items[{{ $index }}][item_code]" value="{{ $item->display_code !== '-' ? $item->display_code : $item->item_code }}">
                                    <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $formatQty($resolvedQty) }}">
                                    <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $resolvedUnit }}">
                                    {{ $item->display_code !== '-' ? $item->display_code : ($item->item_code ?: '-') }}
                                </td>
                                <td class="py-2 font-medium">{{ $resolvedItemName ?: '-' }}</td>
                                <td class="py-2 font-medium">{{ $resolvedColorName }}</td>
                                <td class="py-2 font-medium">{{ $resolvedQty ? number_format($resolvedQty, 0) : '-' }}</td>
                                <input type="hidden" name="items[{{ $index }}][unit]" value="{{ $resolvedUnit }}">
                                <td class="py-2 text-right">{{ $formatAmount($resolvedRate) }}</td>
                                <td class="py-2 text-right font-medium">{{ $formatAmount($resolvedAmount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>




