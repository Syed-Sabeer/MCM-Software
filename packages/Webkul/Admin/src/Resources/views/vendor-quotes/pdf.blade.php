@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $organization = $vendorQuote->organization;
    $charges = $chargeManager->extract($vendorQuote->loadMissing('additionalCharges'), 'vendor_quote');
    $remarks = trim((string) ($vendorQuote->notes ?: ''));
    $terms = trim((string) ($vendorQuote->terms ?: ''));
    $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge')
        . (($charge['type'] ?? 'value') === 'percentage' ? ' (' . rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ''), '0'), '.') . '%)' : '');

    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;

    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Phone: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: ' . core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
    ]);
    $selectedBillingAddress = trim((string) data_get($vendorQuote->billing_address, 'address'));
    $selectedShippingAddress = trim((string) data_get($vendorQuote->shipping_address, 'address'));
    $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))->map(fn ($line) => trim((string) $line))->filter()->values()->all();

    $vendorLines = array_filter([
        $organization?->name,
        ...($selectedBillingAddress !== '' ? $addressLines($selectedBillingAddress) : [
            $organization?->billing_street ?: $organization?->shipping_street,
            trim(implode(', ', array_filter([
                $organization?->billing_city ?: $organization?->shipping_city,
                $organization?->billing_state ?: $organization?->shipping_state,
                $organization?->billing_postcode ?: $organization?->shipping_postcode,
                $organization?->billing_country ?: $organization?->shipping_country,
            ]))),
        ]),
        $organization?->phone ? 'Phone: ' . $organization->phone : null,
        $organization?->phone ? 'Cell: ' . $organization->phone : null,
        data_get($organization, 'email') ? 'Email: ' . data_get($organization, 'email') : null,
    ]);

    $shipToLines = array_filter([
        $organization?->name,
        ...($selectedShippingAddress !== '' ? $addressLines($selectedShippingAddress) : [
            $companyName,
            core()->getConfigData('general.general.company_info.address'),
            core()->getConfigData('general.general.company_info.telephone') ? 'Phone: ' . core()->getConfigData('general.general.company_info.telephone') : null,
            core()->getConfigData('general.general.company_info.cell') ? 'Cell: ' . core()->getConfigData('general.general.company_info.cell') : null,
            core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
        ]),
    ]);

    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vendor Quote {{ $vendorQuote->vendor_quote_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; margin: 0; }
        .page { padding: 24px; }
        .header-table, .party-table, .items-table, .totals-table, .summary-table, .notes-table, .sign-table, .meta-table { width: 100%; border-collapse: collapse; }
        .brand { color: {{ $brandColor }}; }
        .title { font-size: 24px; font-weight: 700; text-align: right; }
        .logo { max-width: 120px; max-height: 70px; margin-bottom: 8px; }
        .meta-label { font-weight: 700; width: 35%; background: #f5f7fb; }
        .meta-table td { border: 1px solid #d8e0ea; padding: 7px 10px; }
        .section-box { border: 1px solid #d8e0ea; padding: 12px; min-height: 90px; }
        .section-title { color: {{ $brandColor }}; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; }
        .summary-table { margin: 16px 0; }
        .summary-table td { border: 1px solid #d8e0ea; padding: 8px; width: 25%; }
        .summary-label { display: block; color: #6b7280; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
        .summary-value { font-weight: 700; }
        .items-table th { background: {{ $brandColor }}; color: #fff; border: 1px solid {{ $brandColor }}; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { border: 1px solid #d8e0ea; padding: 8px; vertical-align: top; }
        .text-right { text-align: right; }
        .totals-table { width: 300px; margin-left: auto; margin-top: 16px; }
        .totals-table td { border: 1px solid #d8e0ea; padding: 8px 10px; }
        .totals-label { background: #f5f7fb; font-weight: 700; }
        .totals-value { text-align: right; font-weight: 700; }
        .grand-row td { background: #eef6ff; color: {{ $brandColor }}; font-weight: 700; }
        .notes-table { margin-top: 18px; }
        .notes-table td { width: 50%; vertical-align: top; padding-right: 12px; }
        .note-card { border: 1px solid #d8e0ea; padding: 12px; min-height: 90px; }
        .preline { white-space: pre-line; }
        .sign-table { margin-top: 26px; }
        .sign-cell { width: 33.33%; padding-right: 18px; }
        .sign-line { border-top: 1px solid #111827; padding-top: 8px; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
<div class="page">
    <table class="header-table">
        <tr>
            <td style="width: 52%; vertical-align: top;">
                @if ($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo" class="logo">
                @endif
                <div class="brand" style="font-size: 16px; font-weight: 700; margin-bottom: 6px;">{{ $companyName }}</div>
                @foreach ($companyLines as $line)
                    <div>{{ $line }}</div>
                @endforeach
            </td>
            <td style="width: 48%; vertical-align: top;">
                <div class="title brand">Vendor Quote</div>
                <table class="meta-table">
                    <tr><td class="meta-label">Vendor Quote #</td><td>{{ $vendorQuote->vendor_quote_number }}</td></tr>
                    <tr><td class="meta-label">Issue Date</td><td>{{ optional($vendorQuote->issue_date)->format('Y-m-d') ?: '-' }}</td></tr>
                    {{-- <tr><td class="meta-label">Job Order #</td><td>{{ optional($vendorQuote->jobOrder)->job_order_number ?: '-' }}</td></tr> --}}
                    <tr><td class="meta-label">Status</td><td>{{ ucfirst($vendorQuote->status ?: 'draft') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="party-table" style="margin-top: 16px;">
        <tr>
            <td style="width: 50%; padding-right: 8px; vertical-align: top;"><div class="section-box"><div class="section-title">Vendor</div>@foreach ($vendorLines as $line)<div>{{ $line }}</div>@endforeach</div></td>
            <td style="width: 50%; padding-left: 8px; vertical-align: top;"><div class="section-box"><div class="section-title">Ship To</div>@foreach ($shipToLines as $line)<div>{{ $line }}</div>@endforeach</div></td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td><span class="summary-label">Payment Terms</span><span class="summary-value">{{ $vendorQuote->payment_term ?: '-' }}</span></td>
            <td><span class="summary-label">Shipping Method</span><span class="summary-value">{{ $vendorQuote->shipping_method ?: '-' }}</span></td>
            <td><span class="summary-label">First Delivery</span><span class="summary-value">{{ optional($vendorQuote->first_delivery_date)->format('Y-m-d') ?: '-' }}</span></td>
            <td><span class="summary-label">Last Delivery</span><span class="summary-value">{{ optional($vendorQuote->last_delivery_date)->format('Y-m-d') ?: '-' }}</span></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 10%;" class="text-right">Qty</th>
                <th style="width: 20%;">Item #</th>
                <th style="width: 42%;">Description</th>
                <th style="width: 14%;" class="text-right">Rate</th>
                <th style="width: 14%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($vendorQuote->items as $item)
                <tr>
                    <td class="text-right">{{ rtrim(rtrim(number_format((float) ($item->quantity ?: 0), 4, '.', ''), '0'), '.') }}</td>
                    <td>{{ $item->item_code ?: $item->sku ?: $item->material_name ?: '-' }}</td>
                    <td>
                        {{ $item->material_name ?: '-' }}
                        @if ($item->unit)
                            <div style="margin-top: 4px; color: #6b7280; font-size: 10px;">UOM: {{ $item->unit }}</div>
                        @endif
                    </td>
                    <td class="text-right">{{ core()->formatBasePrice($item->unit_price ?: 0, 2) }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($item->total ?: 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;">No line items available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr><td class="totals-label">Sub Total</td><td class="totals-value">{{ core()->formatBasePrice($vendorQuote->subtotal ?: 0, 2) }}</td></tr>
        @foreach ($charges as $charge)
            <tr><td class="totals-label">{{ $formatChargeLabel($charge) }}</td><td class="totals-value">{{ core()->formatBasePrice($charge['amount'] ?: 0, 2) }}</td></tr>
        @endforeach
        <tr class="grand-row"><td class="totals-label">Grand Total</td><td class="totals-value">{{ core()->formatBasePrice($vendorQuote->grand_total ?: 0, 2) }}</td></tr>
    </table>

    @if ($remarks)
        <table class="notes-table">
            <tr>
                <td><div class="note-card"><div class="section-title">Remarks</div><div class="preline">{{ $remarks }}</div></div></td>
            </tr>
        </table>
    @endif

    @if ($terms)
        <table class="notes-table">
            <tr>
                <td><div class="note-card"><div class="section-title">Terms & Conditions</div><div class="preline">{{ $terms }}</div></div></td>
            </tr>
        </table>
    @endif

    <table class="sign-table">
        <tr>
            <td class="sign-cell"><div class="sign-line">Prepared By</div></td>
            <td class="sign-cell"><div class="sign-line">Approved By</div></td>
            <td class="sign-cell" style="padding-right:0;"><div class="sign-line">Vendor Confirmation</div></td>
        </tr>
    </table>
</div>
</body>
</html>
