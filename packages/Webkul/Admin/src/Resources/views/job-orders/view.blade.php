<x-admin::layouts>
    <x-slot:title>{{ $jobOrder->job_order_number }}</x-slot>

    @php
        $formatItemQty = fn ($value) => number_format((float) $value, 0, '.', ',');
        $formatStageQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ','), '0'), '.');
        $formatAmount = fn ($value) => number_format((float) $value, 3, '.', ',');
        $formatDate = function ($value) {
            if (empty($value) || (is_string($value) && str_starts_with($value, '0000-00-00'))) {
                return '-';
            }

            try {
                $date = \Illuminate\Support\Carbon::parse($value);

                return (int) $date->format('Y') <= 1 ? '-' : $date->format('Y-m-d');
            } catch (\Throwable) {
                return '-';
            }
        };
        $createdPurchaseOrder = $jobOrder->purchaseOrders->first();
    @endphp

    <div class="flex flex-col gap-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-xl font-bold dark:text-white">Job Order {{ $jobOrder->job_order_number }}</div>
                    <div class="text-sm text-gray-500">Status: {{ \Webkul\Admin\Support\DocumentStatusOptions::label('job_order', $jobOrder->status) }}</div>
                </div>

                <div class="flex gap-2">
                    <form method="POST" action="{{ route('admin.job_orders.customer_visibility', $jobOrder->id) }}">@csrf<button class="secondary-button" @disabled($jobOrder->status === 'draft' && ! $jobOrder->customer_visible_at)>{{ $jobOrder->customer_visible_at ? 'Unpublish' : 'Publish to customer' }}</button></form>
                    <a href="{{ route('admin.vendor_quotes.create', ['job_order_id' => $jobOrder->id]) }}" class="secondary-button" data-loading-link data-loading-text="Opening...">Create Vendor Quote</a>
                    @if ($createdPurchaseOrder)
                        <a href="{{ route('admin.purchase_orders.view', $createdPurchaseOrder->id) }}" class="secondary-button" data-loading-link data-loading-text="Opening...">PO Created</a>
                    @else
                        <a href="{{ route('admin.purchase_orders.create', ['job_order_id' => $jobOrder->id]) }}" class="secondary-button" data-loading-link data-loading-text="Opening...">Create Vendor PO</a>
                    @endif
                    <a href="{{ route('admin.job_orders.edit', $jobOrder->id) }}" class="primary-button" data-loading-link data-loading-text="Opening...">Edit</a>
                </div>
            </div>

            <div class="mt-4 grid gap-4 text-sm dark:text-white md:grid-cols-4">
                <div>
                    <strong>Customer:</strong>
                    @if ($jobOrder->organization_id && $jobOrder->organization)
                        <a class="text-brandColor" href="{{ route('admin.contacts.organizations.view', $jobOrder->organization_id) }}">{{ $jobOrder->organization->name }}</a>
                    @else
                        -
                    @endif
                </div>
                <div>
                    <strong>Proforma:</strong>
                    @if ($jobOrder->proforma_invoice_id && $jobOrder->proformaInvoice)
                        <a class="text-brandColor" href="{{ route('admin.proforma_invoices.view', $jobOrder->proforma_invoice_id) }}">{{ $jobOrder->proformaInvoice->proforma_number }}</a>
                    @else
                        -
                    @endif
                </div>
                <div><strong>Issue Date:</strong> {{ $formatDate($jobOrder->issue_date) }}</div>
                <div><strong>ETD:</strong> {{ $formatDate($jobOrder->required_delivery_date) }}</div>
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
                                <td class="py-2 font-medium">
                                    @if ($item->product_id)
                                        <a class="text-brandColor" href="{{ route('admin.products.view', $item->product_id) }}">{{ $item->display_code }}</a>
                                    @else
                                        {{ $item->display_code }}
                                    @endif
                                </td>
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
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div class="text-base font-semibold dark:text-white">Requirement Sheet</div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.job_orders.requirement_sheet.pdf', $jobOrder->id) }}" class="secondary-button">Export to PDF</a>
                        <a href="{{ route('admin.job_orders.requirement_sheet.csv', $jobOrder->id) }}" class="secondary-button">Export to CSV</a>
                    </div>
                </div>

                <table class="w-full text-sm dark:text-white" style="margin-top: 2%;">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Item</th>
                            <th class="py-2 text-left">Material</th>
                            <th class="py-2 text-left">Color</th>
                            <th class="py-2 text-left">Per Item Required</th>
                            <th class="py-2 text-left">Required</th>
                            <th class="py-2 text-left">From Stock</th>
                            <th class="py-2 text-left">Received</th>
                            <th class="py-2 text-left">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobOrder->requirements as $requirement)
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2">
                                    {{ $requirement->item_codes ?: '-' }}
                                </td>
                                <td class="py-2">{{ $requirement->material_name }}</td>
                                <td class="py-2">{{ $requirement->color_name ?: $requirement->color_code ?: '-' }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->qty_per_unit) }} {{ $requirement->unit }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->required_qty) }} {{ $requirement->unit }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->inventory_allocated_qty) }} {{ $requirement->unit }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->received_qty) }} {{ $requirement->unit }}</td>
                                <td class="py-2">{{ $formatStageQty($requirement->balance_qty) }} {{ $requirement->unit }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-6 text-base font-semibold dark:text-white" style="margin-top:4%;">Total Requirement from Vendor</div>

                <table class="mt-3 w-full text-sm dark:text-white">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="py-2 text-left">Material</th>
                            <th class="py-2 text-left">Color</th>
                            <th class="py-2 text-left">Total Required</th>
                            <th class="py-2 text-left">Received</th>
                            <th class="py-2 text-left">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendorRequirementTotals as $total)
                            <tr class="border-b dark:border-gray-800">
                                <td class="py-2">{{ $total['material_name'] }}</td>
                                <td class="py-2">{{ $total['color_label'] }}</td>
                                <td class="py-2">{{ $formatStageQty($total['required_qty']) }} {{ $total['unit'] }}</td>
                                <td class="py-2">{{ $formatStageQty($total['received_qty']) }} {{ $total['unit'] }}</td>
                                <td class="py-2">{{ $formatStageQty($total['balance_qty']) }} {{ $total['unit'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">No vendor requirements available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div class="text-base font-semibold dark:text-white">Job Cards</div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.job_orders.job_card.pdf', $jobOrder->id) }}" class="secondary-button">Export to PDF</a>
                        <a href="{{ route('admin.job_orders.job_card.csv', $jobOrder->id) }}" class="secondary-button">Export to CSV</a>
                    </div>
                </div>

                <table class="w-full text-sm dark:text-white" style="margin-top: 2%;">
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
                            @php($itemLabel = $jobCard->display_item_label)

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
                    <div><strong>Vendor Quotes:</strong> {{ number_format($jobOrder->vendorQuotes->count()) }}</div>
                    <div><strong>Vendor POs:</strong> {{ number_format($jobOrder->purchaseOrders->count()) }}</div>

                    @foreach ($jobOrder->purchaseOrders as $purchaseOrder)
                        <div class="rounded border border-gray-200 p-2 dark:border-gray-700">
                            <a class="text-brandColor" href="{{ route('admin.purchase_orders.view', $purchaseOrder->id) }}">{{ $purchaseOrder->po_number }}</a>
                            <div>Status: {{ \Webkul\Admin\Support\DocumentStatusOptions::label('purchase_order', $purchaseOrder->status) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-loading-link]').forEach((link) => {
                    link.addEventListener('click', function () {
                        if (this.dataset.loading === '1') {
                            return;
                        }

                        this.dataset.loading = '1';
                        this.classList.add('opacity-70', 'pointer-events-none');
                        this.innerHTML = `<span class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent align-[-2px]"></span><span class="ml-2">${this.dataset.loadingText || 'Opening...'}</span>`;
                    });
                });
            });
        </script>
    @endPushOnce
</x-admin::layouts>
