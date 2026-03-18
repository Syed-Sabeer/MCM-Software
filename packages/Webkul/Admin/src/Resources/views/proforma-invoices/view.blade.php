@php
    $organization = $proformaInvoice->organization;
    $salesPerson = $proformaInvoice->salesOwner;
    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoUrl = $logo ? asset('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Phone: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: ' . core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
    ]);

    $billToLines = array_filter([
        $organization?->name,
        data_get($proformaInvoice->billing_address, 'address') ?: $organization?->billing_street ?: $organization?->shipping_street,
        trim(implode(' ', array_filter([$organization?->billing_city ?: $organization?->shipping_city, $organization?->billing_state ?: $organization?->shipping_state, $organization?->billing_postcode ?: $organization?->shipping_postcode]))),
        $organization?->billing_country ?: $organization?->shipping_country,
        $organization?->phone ? 'Phone: ' . $organization->phone : null,
        data_get($organization, 'email') ? 'Email: ' . data_get($organization, 'email') : null,
    ]);

    $shipToLines = array_filter([
        $organization?->name,
        data_get($proformaInvoice->shipping_address, 'address') ?: $organization?->shipping_street ?: $organization?->billing_street,
        trim(implode(' ', array_filter([$organization?->shipping_city ?: $organization?->billing_city, $organization?->shipping_state ?: $organization?->billing_state, $organization?->shipping_postcode ?: $organization?->billing_postcode]))),
        $organization?->shipping_country ?: $organization?->billing_country,
        $organization?->phone ? 'Phone: ' . $organization->phone : null,
    ]);

    $items = $proformaInvoice->items->map(function ($item) {
        return [
            'preview_image' => $item->preview_image,
            'item_code' => $item->item_code,
            'item_name' => $item->item_name,
            'color' => $item->color_variant_name,
            'quantity' => rtrim(rtrim(number_format((float) ($item->qty ?: 0), 4, '.', ''), '0'), '.'),
            'price' => core()->formatBasePrice($item->unit_price ?: 0),
            'discount' => core()->formatBasePrice($item->discount_amount ?: 0),
            'tax' => core()->formatBasePrice($item->tax_amount ?: 0),
            'total' => core()->formatBasePrice($item->line_total ?: 0),
        ];
    })->values()->all();
@endphp

<x-admin::layouts>
    <x-slot:title>Proforma {{ $proformaInvoice->proforma_number }}</x-slot>

    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
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

        <x-admin::documents.standard
            mode="screen"
            :accent-color="core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9'"
            title="Proforma Invoice"
            :company="['logo' => $logoUrl, 'name' => $companyName, 'lines' => $companyLines]"
            :meta="[
                ['label' => 'Proforma #', 'value' => $proformaInvoice->proforma_number],
                ['label' => 'Quote #', 'value' => optional($proformaInvoice->quote)->quote_number ? 'Q' . ltrim((string) optional($proformaInvoice->quote)->quote_number, 'Q') : null],
                ['label' => 'Issue Date', 'value' => optional($proformaInvoice->issue_date)?->format('M d, Y')],
                ['label' => 'Sales Owner', 'value' => $salesPerson?->name ?: '-'],
                ['label' => 'Status', 'value' => ucfirst((string) $proformaInvoice->status)],
                ['label' => 'Payment Term', 'value' => $proformaInvoice->payment_term],
            ]"
            :party-blocks="[
                ['title' => 'Bill To', 'lines' => $billToLines],
                ['title' => 'Ship To', 'lines' => $shipToLines],
            ]"
            :summary-rows="[
                ['label' => 'Customer PO Ref', 'value' => $proformaInvoice->customer_po_reference],
                ['label' => 'Source Type', 'value' => $proformaInvoice->source_type],
                ['label' => 'Advance Received', 'value' => core()->formatBasePrice($proformaInvoice->received_amount ?: 0)],
                ['label' => 'Remaining Balance', 'value' => core()->formatBasePrice($proformaInvoice->remaining_amount ?: 0)],
            ]"
            :columns="[
                ['key' => 'preview_image', 'label' => 'Image', 'type' => 'image', 'align' => 'text-center', 'width' => '10%'],
                ['key' => 'item_code', 'label' => 'Item Code', 'width' => '16%'],
                ['key' => 'item_name', 'label' => 'Description', 'width' => '22%'],
                ['key' => 'color', 'label' => 'Color', 'width' => '12%'],
                ['key' => 'quantity', 'label' => 'Qty', 'align' => 'text-right', 'width' => '8%'],
                ['key' => 'price', 'label' => 'Rate', 'align' => 'text-right', 'width' => '10%'],
                ['key' => 'discount', 'label' => 'Discount', 'align' => 'text-right', 'width' => '10%'],
                ['key' => 'tax', 'label' => 'Tax', 'align' => 'text-right', 'width' => '10%'],
                ['key' => 'total', 'label' => 'Amount', 'align' => 'text-right', 'width' => '12%'],
            ]"
            :items="$items"
            :totals="[
                ['label' => 'Sub Total', 'value' => core()->formatBasePrice($proformaInvoice->subtotal ?: 0), 'always' => true],
                ['label' => 'Discount', 'value' => core()->formatBasePrice($proformaInvoice->discount_amount ?: 0), 'always' => true],
                ['label' => 'Tax', 'value' => core()->formatBasePrice($proformaInvoice->tax_amount ?: 0), 'always' => true],
                ['label' => 'Advance Received', 'value' => core()->formatBasePrice($proformaInvoice->received_amount ?: 0), 'always' => true],
                ['label' => 'Expected Balance', 'value' => core()->formatBasePrice($proformaInvoice->remaining_amount ?: 0), 'always' => true],
                ['label' => 'Grand Total', 'value' => core()->formatBasePrice($proformaInvoice->grand_total ?: 0), 'highlight' => true, 'always' => true],
            ]"
            :notes="[
                ['label' => 'Remarks', 'value' => $proformaInvoice->notes],
                ['label' => 'Terms & Conditions', 'value' => $proformaInvoice->terms],
            ]"
            :signatures="['Prepared By', 'Approved By', 'Customer Confirmation']"
        />

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold dark:text-white">Receipt History</h3>
                <div class="space-y-3 text-sm dark:text-white">
                    @forelse ($proformaInvoice->receipts as $receipt)
                        <div class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-semibold">{{ $receipt->receipt_number ?: 'Receipt' }}</div>
                                    <div class="text-gray-500">{{ optional($receipt->payment_date)->format('M d, Y') }} | {{ $receipt->payment_method ?: 'Method not set' }}</div>
                                </div>
                                <div class="text-right font-semibold">{{ core()->formatBasePrice($receipt->amount) }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500">No advance receipts recorded yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-3 text-lg font-semibold dark:text-white">Add Advance Receipt</h3>
                <x-admin::form :action="route('admin.proforma_invoices.receipts.store', $proformaInvoice->id)" method="POST">
                    <div class="grid gap-3">
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Payment Date</x-admin::form.control-group.label><x-admin::form.control-group.control type="date" name="payment_date" rules="required" value="{{ now()->toDateString() }}" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label class="required">Amount</x-admin::form.control-group.label><x-admin::form.control-group.control type="number" name="amount" step="0.0001" min="0.0001" rules="required" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Payment Method</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="payment_method" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Reference No</x-admin::form.control-group.label><x-admin::form.control-group.control type="text" name="reference_no" /></x-admin::form.control-group>
                        <x-admin::form.control-group class="!mb-0"><x-admin::form.control-group.label>Notes</x-admin::form.control-group.label><x-admin::form.control-group.control type="textarea" name="notes" /></x-admin::form.control-group>
                        <button type="submit" class="primary-button w-max">Record Receipt</button>
                    </div>
                </x-admin::form>
            </div>
        </div>
    </div>
</x-admin::layouts>
