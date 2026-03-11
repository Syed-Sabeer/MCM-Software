<x-admin::layouts>
    <x-slot:title>Edit {{ $goodsReceipt->goods_receipt_number }}</x-slot>

    <x-admin::form :action="route('admin.goods_receipts.update', $goodsReceipt->id)" method="PUT" enctype="multipart/form-data">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <div>
                    <div class="text-xl font-bold dark:text-white">Edit Goods Receipt</div>
                    <div class="text-sm text-gray-500">PO: {{ $purchaseOrder->po_number }}</div>
                </div>

                <button class="primary-button">Update Receipt</button>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Receipt #</label>
                        <input type="text" value="{{ $goodsReceipt->goods_receipt_number }}" disabled class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Receipt Date</label>
                        <input type="date" name="receipt_date" value="{{ old('receipt_date', optional($goodsReceipt->receipt_date)->toDateString()) }}" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @error('receipt_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium dark:text-white">Attachment</label>
                        <input type="file" name="attachment" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @if ($goodsReceipt->attachment_path)
                            <a href="{{ asset('storage/' . $goodsReceipt->attachment_path) }}" target="_blank" class="mt-1 inline-block text-xs text-brandColor">View current attachment</a>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-1 block text-sm font-medium dark:text-white">Notes</label>
                    <textarea name="notes" rows="3" class="w-full rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('notes', $goodsReceipt->notes) }}</textarea>
                </div>

                <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
                <input type="hidden" name="vendor_id" value="{{ $purchaseOrder->organization_id }}">
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Receipt Items</div>

                @error('items')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror

                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Material</th>
                            <th class="py-2 text-left">Ordered</th>
                            <th class="py-2 text-left">Previously Received</th>
                            <th class="py-2 text-left">Current Receipt</th>
                            <th class="py-2 text-left">Available to Receive</th>
                            <th class="py-2 text-left">Receive Now</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($purchaseOrder->items as $index => $item)
                            @php
                                $currentReceiptItem = $goodsReceipt->items->firstWhere('purchase_order_item_id', $item->id);
                                $currentReceiptQty = (float) optional($currentReceiptItem)->received_qty;
                                $alreadyReceivedElsewhere = max((float) $item->received_quantity - $currentReceiptQty, 0);
                                $availableToReceive = max((float) $item->ordered_quantity - $alreadyReceivedElsewhere, 0);
                            @endphp

                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2">{{ $item->material_name ?: $item->item }}</td>
                                <td class="py-2">{{ $item->ordered_quantity }}</td>
                                <td class="py-2">{{ $alreadyReceivedElsewhere }}</td>
                                <td class="py-2">{{ $currentReceiptQty }}</td>
                                <td class="py-2">{{ $availableToReceive }}</td>
                                <td class="py-2">
                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                    <input type="number" step="0.0001" max="{{ $availableToReceive }}" min="0" name="items[{{ $index }}][received_qty]" value="{{ old('items.' . $index . '.received_qty', $currentReceiptQty) }}" class="w-28 rounded border border-gray-200 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                    @error('items.' . $index . '.received_qty')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin::form>
</x-admin::layouts>
