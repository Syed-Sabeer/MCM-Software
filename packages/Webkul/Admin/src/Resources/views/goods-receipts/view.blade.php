<x-admin::layouts>
    <x-slot:title>{{ $goodsReceipt->goods_receipt_number }}</x-slot>
    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-bold dark:text-white">Goods Receipt {{ $goodsReceipt->goods_receipt_number }}</div>
                    <div class="text-sm text-gray-500">Posted against purchase order {{ optional($goodsReceipt->purchaseOrder)->po_number }}</div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.goods_receipts.edit', $goodsReceipt->id) }}" class="secondary-button">Edit</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 text-sm dark:text-white md:grid-cols-4">
                <div>
                    <strong>Vendor:</strong>
                    @if ($goodsReceipt->vendor_id && $goodsReceipt->vendor)
                        <a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $goodsReceipt->vendor_id) }}">{{ $goodsReceipt->vendor->name }}</a>
                    @else
                        -
                    @endif
                </div>
                <div><strong>PO:</strong> <a class="text-brandColor" href="{{ route('admin.purchase_orders.view', $goodsReceipt->purchase_order_id) }}">{{ optional($goodsReceipt->purchaseOrder)->po_number }}</a></div>
                <div><strong>Date:</strong> {{ optional($goodsReceipt->receipt_date)->format('Y-m-d') }}</div>
                <div><strong>Status:</strong> {{ ucfirst((string) $goodsReceipt->status) }}</div>
            </div>

            @if ($goodsReceipt->notes || $goodsReceipt->attachment_path)
                <div class="mt-4 grid gap-4 text-sm dark:text-white md:grid-cols-2">
                    <div>
                        <strong>Notes:</strong>
                        <div class="mt-1 whitespace-pre-line text-gray-600 dark:text-gray-300">{{ $goodsReceipt->notes ?: '-' }}</div>
                    </div>

                    <div>
                        <strong>Attachment:</strong>
                        <div class="mt-1">
                            @if ($goodsReceipt->attachment_path)
                                <a class="text-brandColor" href="{{ asset('storage/' . $goodsReceipt->attachment_path) }}" target="_blank">View Attachment</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <table class="w-full text-sm dark:text-white">
                <thead>
                    <tr class="border-b dark:border-gray-700">
                        <th class="py-2 text-left">Material</th>
                        <th class="py-2 text-left">Qty</th>
                        <th class="py-2 text-left">Unit Price</th>
                        <th class="py-2 text-left">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($goodsReceipt->items as $item)
                        <tr class="border-b dark:border-gray-800">
                            <td class="py-2">
                                @if ($item->product_id)
                                    <a class="text-brandColor" href="{{ route('admin.products.view', $item->product_id) }}">{{ $item->material_name }}</a>
                                @else
                                    {{ $item->material_name }}
                                @endif
                            </td>
                            <td class="py-2">{{ $item->received_qty }} {{ $item->unit }}</td>
                            <td class="py-2">{{ core()->formatBasePrice($item->unit_price, 2) }}</td>
                            <td class="py-2">{{ core()->formatBasePrice($item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-3 text-base font-semibold dark:text-white">Receipt History</div>

            @php
                $purchaseOrderItems = optional($goodsReceipt->purchaseOrder)->items ?? collect();
                $orderedQtyMap = $purchaseOrderItems->mapWithKeys(fn ($item) => [$item->id => (float) $item->ordered_quantity]);
                $materialNameMap = $purchaseOrderItems->mapWithKeys(fn ($item) => [$item->id => ($item->material_name ?: $item->item)]);
                $runningReceived = [];
                $receiptTimeline = [];

                foreach ((optional($goodsReceipt->purchaseOrder)->receipts ?? collect())->sortBy(fn ($receipt) => (optional($receipt->receipt_date)->format('Y-m-d') ?: '0000-00-00') . '-' . str_pad((string) $receipt->id, 10, '0', STR_PAD_LEFT)) as $receiptHistory) {
                    $lineSnapshots = [];

                    foreach ($receiptHistory->items as $historyItem) {
                        $poItemId = $historyItem->purchase_order_item_id;
                        $thisReceiptQty = (float) $historyItem->received_qty;
                        $runningReceived[$poItemId] = ($runningReceived[$poItemId] ?? 0) + $thisReceiptQty;
                        $orderedQty = (float) ($orderedQtyMap[$poItemId] ?? $thisReceiptQty);
                        $remainingQty = max($orderedQty - $runningReceived[$poItemId], 0);

                        $lineSnapshots[] = [
                            'material' => $materialNameMap[$poItemId] ?? $historyItem->material_name,
                            'this_receipt' => $thisReceiptQty,
                            'cumulative_received' => $runningReceived[$poItemId],
                            'remaining' => $remainingQty,
                            'unit' => $historyItem->unit,
                        ];
                    }

                    $receiptTimeline[] = [
                        'receipt' => $receiptHistory,
                        'total_qty' => (float) $receiptHistory->items->sum('received_qty'),
                        'total_value' => (float) $receiptHistory->items->sum('line_total'),
                        'lines' => $lineSnapshots,
                    ];
                }
            @endphp

            <div class="grid gap-3">
                @forelse (collect($receiptTimeline)->reverse() as $timelineEntry)
                    @php $receiptHistory = $timelineEntry['receipt']; @endphp

                    <div class="rounded border border-gray-200 px-4 py-3 text-sm dark:border-gray-700 dark:text-white">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <a class="font-semibold text-brandColor" href="{{ route('admin.goods_receipts.view', $receiptHistory->id) }}">{{ $receiptHistory->goods_receipt_number }}</a>
                                <div class="text-xs text-gray-500">{{ optional($receiptHistory->receipt_date)->format('Y-m-d') ?: '-' }}</div>
                            </div>

                            <div class="text-right">
                                <div>{{ number_format($timelineEntry['total_qty'], 2) }} qty received</div>
                                <div class="text-xs text-gray-500">{{ core()->formatBasePrice($timelineEntry['total_value'], 2) }}</div>
                            </div>
                        </div>

                        @if (! empty($timelineEntry['lines']))
                            <div class="mt-3 overflow-x-auto">
                                <table class="w-full text-xs md:text-sm">
                                    <thead>
                                        <tr class="border-b dark:border-gray-700">
                                            <th class="py-2 text-left">Material</th>
                                            <th class="py-2 text-right">This Update</th>
                                            <th class="py-2 text-right">Previously Received</th>
                                            <th class="py-2 text-right">Remaining Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($timelineEntry['lines'] as $line)
                                            <tr class="border-b dark:border-gray-800">
                                                <td class="py-2">{{ $line['material'] }}</td>
                                                <td class="py-2 text-right">{{ number_format($line['this_receipt'], 2) }} {{ $line['unit'] }}</td>
                                                <td class="py-2 text-right">{{ number_format(max($line['cumulative_received'] - $line['this_receipt'], 0), 2) }} {{ $line['unit'] }}</td>
                                                <td class="py-2 text-right">{{ number_format($line['remaining'], 2) }} {{ $line['unit'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-sm text-gray-500">No receipt history found for this purchase order.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-admin::layouts>


