@php
    $organization = $purchaseOrder->organization;

    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Phone: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: ' . core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
    ]);

    $vendorLines = array_filter([
        $organization?->name,
        $organization?->billing_street ?: $organization?->shipping_street,
        trim(implode(' ', array_filter([
            $organization?->billing_city ?: $organization?->shipping_city,
            $organization?->billing_state ?: $organization?->shipping_state,
            $organization?->billing_postcode ?: $organization?->shipping_postcode,
        ]))),
        $organization?->billing_country ?: $organization?->shipping_country,
        $organization?->phone ? 'Phone: ' . $organization->phone : null,
        data_get($organization, 'email') ? 'Email: ' . data_get($organization, 'email') : null,
    ]);

    $shipToLines = array_filter([
        $companyName,
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Phone: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
    ]);

    $items = $purchaseOrder->items->map(function ($item) {
        return [
            'item' => $item->material_name ?: $item->item,
            'description' => $item->description,
            'ordered_quantity' => rtrim(rtrim(number_format((float) ($item->ordered_quantity ?: $item->quantity ?: 0), 4, '.', ''), '0'), '.'),
            'received_quantity' => rtrim(rtrim(number_format((float) ($item->received_quantity ?: 0), 4, '.', ''), '0'), '.'),
            'pending_quantity' => rtrim(rtrim(number_format((float) ($item->pending_quantity ?: 0), 4, '.', ''), '0'), '.'),
            'unit' => $item->unit,
            'price' => core()->formatBasePrice($item->price ?: 0),
            'total' => core()->formatBasePrice($item->total ?: 0),
        ];
    })->values()->all();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
</head>
<body>
    <x-admin::documents.standard
        mode="print"
        title="Purchase Order"
        :accent-color="core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9'"
        :company="[
            'logo' => $logoPath,
            'name' => $companyName,
            'lines' => $companyLines,
        ]"
        :meta="[
            ['label' => 'PO #', 'value' => $purchaseOrder->po_number],
            ['label' => 'Issue Date', 'value' => $purchaseOrder->created_at?->format('M d, Y')],
            ['label' => 'Job Order', 'value' => $purchaseOrder->job_number ?: optional($purchaseOrder->jobOrder)->job_order_number],
            ['label' => 'Status', 'value' => ucfirst(str_replace('_', ' ', $purchaseOrder->status ?: 'draft'))],
            ['label' => 'Vendor Quote', 'value' => optional($purchaseOrder->vendorQuote)->vendor_quote_number],
            ['label' => 'Expected Receive', 'value' => optional($purchaseOrder->expected_receive_date)?->format('M d, Y')],
        ]"
        :party-blocks="[
            ['title' => 'Vendor', 'lines' => $vendorLines],
            ['title' => 'Ship To / Factory', 'lines' => $shipToLines],
        ]"
        :summary-rows="[
            ['label' => 'Payment Term', 'value' => $purchaseOrder->payment_term],
            ['label' => 'Shipping Method', 'value' => $purchaseOrder->shipping_method],
            ['label' => 'First Delivery', 'value' => optional($purchaseOrder->completion_date)?->format('M d, Y')],
            ['label' => 'Last Delivery', 'value' => optional($purchaseOrder->last_delivery_date)?->format('M d, Y')],
        ]"
        :columns="[
            ['key' => 'item', 'label' => 'Material / Item', 'width' => '24%'],
            ['key' => 'description', 'label' => 'Description', 'width' => '20%'],
            ['key' => 'ordered_quantity', 'label' => 'Ordered Qty', 'align' => 'text-right', 'width' => '10%'],
            ['key' => 'received_quantity', 'label' => 'Received Qty', 'align' => 'text-right', 'width' => '10%'],
            ['key' => 'pending_quantity', 'label' => 'Pending Qty', 'align' => 'text-right', 'width' => '10%'],
            ['key' => 'unit', 'label' => 'Unit', 'align' => 'text-center', 'width' => '8%'],
            ['key' => 'price', 'label' => 'Rate', 'align' => 'text-right', 'width' => '9%'],
            ['key' => 'total', 'label' => 'Amount', 'align' => 'text-right', 'width' => '9%'],
        ]"
        :items="$items"
        :totals="[
            ['label' => 'Sub Total', 'value' => core()->formatBasePrice($purchaseOrder->sub_total ?: 0), 'always' => true],
            ['label' => 'Sales Tax', 'value' => core()->formatBasePrice($purchaseOrder->tax_amount ?: 0), 'always' => true],
            ['label' => 'Freight', 'value' => core()->formatBasePrice($purchaseOrder->freight ?: 0), 'always' => true],
            ['label' => 'Grand Total', 'value' => core()->formatBasePrice($purchaseOrder->grand_total ?: 0), 'highlight' => true, 'always' => true],
        ]"
        :notes="[
            ['label' => 'Procurement Notes', 'value' => $purchaseOrder->notes],
            ['label' => 'Delivery / Shipping Remarks', 'value' => collect([
                $purchaseOrder->payment_term ? 'Payment Term: ' . $purchaseOrder->payment_term : null,
                $purchaseOrder->shipping_method ? 'Shipping Method: ' . $purchaseOrder->shipping_method : null,
            ])->filter()->implode("\n")],
        ]"
        :signatures="['Prepared By', 'Approved By', 'Vendor Confirmation']"
    />
</body>
</html>
