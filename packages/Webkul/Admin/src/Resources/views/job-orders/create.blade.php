<x-admin::layouts>
    <x-slot:title>Create Job Order</x-slot>

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
                            <label class="mb-1 block text-sm font-medium dark:text-white">Status</label>
                            <select name="status" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                @foreach (['draft', 'open', 'in_progress', 'ready_to_ship', 'completed', 'closed', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', 'open') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Issue Date</label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium dark:text-white">Required Delivery Date</label>
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
                        <div><strong>Grand Total:</strong> {{ core()->formatBasePrice($proformaInvoice->grand_total, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Job Order Items</div>

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Item Code</th>
                            <th class="py-2 text-left">Qty</th>
                            <th class="py-2 text-left">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proformaInvoice->items as $index => $item)
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2 font-medium">
                                    <input type="hidden" name="items[{{ $index }}][proforma_invoice_item_id]" value="{{ $item->id }}">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                    <input type="hidden" name="items[{{ $index }}][item_name]" value="{{ $item->item_name }}">
                                    <input type="hidden" name="items[{{ $index }}][item_code]" value="{{ $item->item_code }}">
                                    <input type="hidden" name="items[{{ $index }}][description]" value="{{ $item->description }}">
                                    <input type="hidden" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}">
                                    <input type="hidden" name="items[{{ $index }}][line_total]" value="{{ $item->line_total }}">
                                    {{ $item->item_code ?: '-' }}
                                </td>
                                <td class="py-2"><input type="number" step="0.0001" name="items[{{ $index }}][qty]" value="{{ $item->qty }}" class="w-28 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td>
                                <td class="py-2"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit }}" class="w-28 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
