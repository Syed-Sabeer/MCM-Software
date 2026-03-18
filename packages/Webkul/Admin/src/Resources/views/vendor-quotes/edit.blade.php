@php
    $prefillItems = old('items', $vendorQuote->items->map(fn ($item) => [
        'id'                    => $item->id,
        'requirement_id'        => $item->requirement_id,
        'material_name'         => $item->material_name,
        'quantity'              => $item->quantity,
        'unit'                  => $item->unit,
        'unit_price'            => $item->unit_price,
        'expected_receive_date' => optional($item->expected_receive_date)->toDateString(),
    ])->toArray());
@endphp
<x-admin::layouts>
    <x-slot:title>Edit Vendor Quote</x-slot>

    <x-admin::form :action="route('admin.vendor_quotes.update', $vendorQuote->id)" method="PUT" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900"><div><div class="text-xl font-bold dark:text-white">Edit Vendor Quote</div><div class="text-sm text-gray-500">{{ $vendorQuote->jobOrder?->job_order_number ? 'Job Order: ' . $vendorQuote->jobOrder->job_order_number : 'Procurement RFQ' }}</div></div><button class="primary-button">Save Vendor Quote</button></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"><div class="grid gap-4 md:grid-cols-3"><div><label class="mb-1 block text-sm font-medium dark:text-white">Vendor Quote #</label><input type="text" name="vendor_quote_number" value="{{ old('vendor_quote_number', $vendorQuote->vendor_quote_number) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></div><div><label class="mb-1 block text-sm font-medium dark:text-white">Vendor</label><select name="organization_id" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"><option value="">Select vendor</option>@foreach ($vendors as $vendor)<option value="{{ $vendor->id }}" @selected(old('organization_id', $vendorQuote->organization_id) == $vendor->id)>{{ $vendor->name }}</option>@endforeach</select></div><div><label class="mb-1 block text-sm font-medium dark:text-white">Status</label><select name="status" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">@foreach (['draft', 'requested', 'received', 'selected', 'rejected', 'cancelled'] as $status)<option value="{{ $status }}" @selected(old('status', $vendorQuote->status) === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div><div><label class="mb-1 block text-sm font-medium dark:text-white">Issue Date</label><input type="date" name="issue_date" value="{{ old('issue_date', optional($vendorQuote->issue_date)->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></div><div><label class="mb-1 block text-sm font-medium dark:text-white">Expected Response</label><input type="date" name="expected_response_date" value="{{ old('expected_response_date', optional($vendorQuote->expected_response_date)->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></div><div><label class="mb-1 block text-sm font-medium dark:text-white">Attachment</label><input type="file" name="attachment" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></div></div><div class="mt-4"><label class="mb-1 block text-sm font-medium dark:text-white">Notes</label><textarea name="notes" rows="3" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('notes', $vendorQuote->notes) }}</textarea></div><input type="hidden" name="job_order_id" value="{{ $vendorQuote->job_order_id }}"></div>
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"><div class="mb-3 flex items-center justify-between"><div class="text-base font-semibold dark:text-white">Vendor Quote Items</div><button type="button" class="secondary-button" onclick="window.addVendorQuoteRow()">Add Row</button></div><table class="w-full text-sm" id="vendor-quote-items-table"><thead><tr class="border-b dark:border-gray-700"><th class="py-2 text-left">Material</th><th class="py-2 text-left">Qty</th><th class="py-2 text-left">Unit</th><th class="py-2 text-left">Unit Price</th><th class="py-2 text-left">Expected Receive</th><th class="py-2 text-center">Action</th></tr></thead><tbody>@foreach ($prefillItems as $index => $item)<tr><td class="py-2"><input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}"><input type="hidden" name="items[{{ $index }}][requirement_id]" value="{{ $item['requirement_id'] ?? '' }}"><input type="text" name="items[{{ $index }}][material_name]" value="{{ $item['material_name'] ?? '' }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td><td class="py-2"><input type="number" step="0.0001" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td><td class="py-2"><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? '' }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td><td class="py-2"><input type="number" step="0.0001" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? 0 }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td><td class="py-2"><input type="date" name="items[{{ $index }}][expected_receive_date]" value="{{ $item['expected_receive_date'] ?? '' }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></td><td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800" onclick="window.removeVendorQuoteRow(this)"><i class="icon-delete"></i></button></td></tr>@endforeach</tbody></table></div>
        </div>
    </x-admin::form>
    @push('scripts')
        <script>
            window.removeVendorQuoteRow = function (button) {
                const tbody = document.querySelector('#vendor-quote-items-table tbody');
                const row = button.closest('tr');

                if (!tbody || !row) {
                    return;
                }

                if (tbody.children.length <= 1) {
                    row.querySelectorAll('input').forEach(function (input) {
                        if (input.type === 'hidden') {
                            input.value = '';
                        } else if (input.type === 'number') {
                            input.value = input.name.includes('[quantity]') ? '1' : '0';
                        } else {
                            input.value = '';
                        }
                    });

                    return;
                }

                row.remove();
            };

            window.addVendorQuoteRow = function () {
                const tbody = document.querySelector('#vendor-quote-items-table tbody');
                const index = tbody.children.length;

                tbody.insertAdjacentHTML('beforeend', `<tr><td class="py-2"><input type="hidden" name="items[${index}][requirement_id]" value=""><input type="text" name="items[${index}][material_name]" class="w-full rounded border border-gray-200 px-3 py-2 text-sm"></td><td class="py-2"><input type="number" step="0.0001" name="items[${index}][quantity]" value="1" class="w-full rounded border border-gray-200 px-3 py-2 text-sm"></td><td class="py-2"><input type="text" name="items[${index}][unit]" class="w-full rounded border border-gray-200 px-3 py-2 text-sm"></td><td class="py-2"><input type="number" step="0.0001" name="items[${index}][unit_price]" value="0" class="w-full rounded border border-gray-200 px-3 py-2 text-sm"></td><td class="py-2"><input type="date" name="items[${index}][expected_receive_date]" class="w-full rounded border border-gray-200 px-3 py-2 text-sm"></td><td class="py-2 text-center"><button type="button" class="inline-flex items-center justify-center rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100" onclick="window.removeVendorQuoteRow(this)"><i class="icon-delete"></i></button></td></tr>`);
            };
        </script>
    @endpush
</x-admin::layouts>
