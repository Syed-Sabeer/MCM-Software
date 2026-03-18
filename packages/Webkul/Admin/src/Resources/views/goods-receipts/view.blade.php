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
    </div>
</x-admin::layouts>
