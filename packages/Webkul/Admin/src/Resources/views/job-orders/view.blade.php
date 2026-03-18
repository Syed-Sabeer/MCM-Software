<x-admin::layouts>
    <x-slot:title>{{ $jobOrder->job_order_number }}</x-slot>

    @php
        $formatItemQty = fn ($value) => number_format((float) $value, 0, '.', '');
        $formatStageQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
        $formatAmount = fn ($value) => number_format((float) $value, 3, '.', ',');
    @endphp

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-bold dark:text-white">Job Order {{ $jobOrder->job_order_number }}</div>
                    <div class="text-sm text-gray-500">Status: {{ ucfirst(str_replace('_', ' ', $jobOrder->status)) }}</div>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.vendor_quotes.create', ['job_order_id' => $jobOrder->id]) }}" class="secondary-button">Create Vendor Quote</a>
                    <a href="{{ route('admin.purchase_orders.create', ['job_order_id' => $jobOrder->id]) }}" class="secondary-button">Create Vendor PO</a>
                    <a href="{{ route('admin.job_orders.edit', $jobOrder->id) }}" class="primary-button">Edit</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 text-sm dark:text-white md:grid-cols-4">
                <div><strong>Customer:</strong> {{ optional($jobOrder->organization)->name }}</div>
                <div><strong>Proforma:</strong> {{ optional($jobOrder->proformaInvoice)->proforma_number }}</div>
                <div><strong>Issue Date:</strong> {{ optional($jobOrder->issue_date)->format('Y-m-d') }}</div>
                <div><strong>Required Delivery:</strong> {{ optional($jobOrder->required_delivery_date)->format('Y-m-d') ?: '-' }}</div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Job Order Items</div>

                <table class="w-full text-sm dark:text-white">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Item Code</th>
                            <th class="py-2 text-left">Description</th>
                            <th class="py-2 text-right">Qty</th>
                            <th class="py-2 text-left">Unit</th>
                            <th class="py-2 text-right">Rate</th>
                            <th class="py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobOrder->items as $item)
                            @php
                                $fallbackProformaItem = $jobOrder->proformaInvoice?->items?->first(function ($proformaItem) use ($item) {
                                    if (! empty($item->proforma_invoice_item_id)) {
                                        return (int) $proformaItem->id === (int) $item->proforma_invoice_item_id;
                                    }

                                    if (! empty($item->product_id)) {
                                        return (int) $proformaItem->product_id === (int) $item->product_id;
                                    }

                                    return ! empty($item->item_code)
                                        && (string) $proformaItem->item_code === (string) $item->item_code;
                                });

                                $displayRate = $item->unit_price
                                    ?? $fallbackProformaItem?->unit_price
                                    ?? $item->product?->selling_price
                                    ?? 0;

                                $displayAmount = $item->line_total
                                    ?? $fallbackProformaItem?->line_total
                                    ?? ((float) $item->qty * (float) $displayRate);
                            @endphp
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2 font-medium">{{ $item->display_code }}</td>
                                <td class="py-2">{{ $item->item_name ?: '-' }}</td>
                                <td class="py-2 text-right">{{ $formatItemQty($item->qty) }}</td>
                                <td class="py-2">{{ $item->unit ?: 'PCS' }}</td>
                                <td class="py-2 text-right">{{ $formatAmount($displayRate) }}</td>
                                <td class="py-2 text-right font-medium">{{ $formatAmount($displayAmount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Requirement Sheet</div>

                <table class="w-full text-sm dark:text-white">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Item</th>
                            <th class="py-2 text-left">Material</th>
                            <th class="py-2 text-left">Per Item Required</th>
                            <th class="py-2 text-left">Required</th>
                            <th class="py-2 text-left">Received</th>
                            <th class="py-2 text-left">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobOrder->requirements as $requirement)
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2">
                                    {{ optional($jobOrder->items->firstWhere('id', $requirement->job_order_item_id))->display_code ?: '-' }}
                                </td>
                                <td class="py-2">{{ $requirement->material_name }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->qty_per_unit) }} {{ $requirement->unit }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->required_qty) }} {{ $requirement->unit }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->received_qty) }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->balance_qty) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Job Cards</div>

                <table class="w-full text-sm dark:text-white">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Item</th>
                            <th class="py-2 text-left">Section</th>
                            <th class="py-2 text-left">Process</th>
                            <th class="py-2 text-left">Requirement</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobOrder->jobCards as $jobCard)
                            @php
                                $itemCode = optional($jobCard->jobOrderItem)->display_code;
                                $itemLabel = $itemCode && $itemCode !== '-' ? $itemCode : ($jobCard->title ?: 'Job Card');
                            @endphp

                            @foreach ($jobCard->sections as $section)
                                @forelse ($section->items as $sectionItem)
                                    <tr class="border-b dark:border-gray-800">
                                        <td class="py-2 font-medium">{{ $itemLabel }}</td>
                                        <td class="py-2">{{ $section->section_name }}</td>
                                        <td class="py-2">{{ $sectionItem->name }}</td>
                                        <td class="py-2">
                                            @if ($sectionItem->qty)
                                                {{ $formatStageQty($sectionItem->qty) }} {{ $sectionItem->unit }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="border-b dark:border-gray-800">
                                        <td class="py-2 font-medium">{{ $itemLabel }}</td>
                                        <td class="py-2">{{ $section->section_name }}</td>
                                        <td class="py-2">-</td>
                                        <td class="py-2">-</td>
                                    </tr>
                                @endforelse
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">No job cards available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 text-base font-semibold dark:text-white">Procurement Summary</div>

                <div class="space-y-2 text-sm dark:text-white">
                    <div><strong>Vendor Quotes:</strong> {{ $jobOrder->vendorQuotes->count() }}</div>
                    <div><strong>Vendor POs:</strong> {{ $jobOrder->purchaseOrders->count() }}</div>

                    @foreach ($jobOrder->purchaseOrders as $purchaseOrder)
                        <div class="rounded border border-gray-200 p-2 dark:border-gray-700">
                            <a class="text-brandColor" href="{{ route('admin.purchase_orders.view', $purchaseOrder->id) }}">{{ $purchaseOrder->po_number }}</a>
                            <div>Status: {{ ucfirst(str_replace('_', ' ', $purchaseOrder->status)) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin::layouts>
