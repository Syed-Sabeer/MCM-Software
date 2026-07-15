@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $organization = $quote->organization;
    $salesPerson = $quote->user;
    $charges = $chargeManager->extract($quote->loadMissing('additionalCharges'), 'quote');
    $selectedBillingAddress = trim((string) data_get($quote->billing_address, 'address'));
    $selectedShippingAddress = trim((string) data_get($quote->shipping_address, 'address'));
    $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))
        ->map(fn ($line) => trim((string) $line))
        ->filter()
        ->values()
        ->all();

    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#b91c1c';
    $configuredLogo = core()->getConfigData('general.general.admin_logo.logo_image');
    $configuredLogoPath = $configuredLogo ? public_path('storage/'.$configuredLogo) : null;
    $fallbackLogoPath = public_path('logo/mcmmain-pdf.png');
    $logoPath = $configuredLogoPath && file_exists($configuredLogoPath)
        ? $configuredLogoPath
        : (file_exists($fallbackLogoPath) ? $fallbackLogoPath : null);
    $logoSource = $logoPath
        ? 'data:'.(mime_content_type($logoPath) ?: 'image/png').';base64,'.base64_encode(file_get_contents($logoPath))
        : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Tel: '.core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: '.core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: '.core()->getConfigData('general.general.company_info.email') : null,
    ]);

    $billToLines = array_filter([
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
    ]);

    $shipToLines = array_filter([
        $organization?->name,
        ...($selectedShippingAddress !== '' ? $addressLines($selectedShippingAddress) : [
            $organization?->shipping_street ?: $organization?->billing_street,
            trim(implode(', ', array_filter([
                $organization?->shipping_city ?: $organization?->billing_city,
                $organization?->shipping_state ?: $organization?->billing_state,
                $organization?->shipping_postcode ?: $organization?->billing_postcode,
                $organization?->shipping_country ?: $organization?->billing_country,
            ]))),
        ]),
    ]);

    $paymentTerms = data_get($quote, 'payment_terms') ?: data_get($quote, 'payment_term') ?: '-';
    $shippingMethod = data_get($quote, 'shipping_method') ?: '-';
    $productionTime = data_get($quote, 'production_time') ?: '-';
    $transitTime = data_get($quote, 'transit_time') ?: '-';
    $remarks = trim((string) ($quote->description ?: $quote->notes ?: ''));
    $terms = trim((string) ($quote->terms ?: ''));
    $formatDate = function ($value) {
        if (blank($value)) {
            return '-';
        }

        try {
            $date = \Carbon\Carbon::parse($value);
        } catch (\Throwable) {
            return '-';
        }

        return $date->year > 1 ? $date->format('M d, Y') : '-';
    };
    $formatQuantity = fn ($value) => rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
    $formatMoney = fn ($value) => core()->formatBasePrice((float) $value, 2);
    $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge')
        .(($charge['type'] ?? 'value') === 'percentage'
            ? ' ('.rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ','), '0'), '.').'%)'
            : '');
    $etd = $formatDate(data_get($quote, 'etd') ?: $quote->expired_at);
    $eta = $formatDate(data_get($quote, 'eta'));

    $resolveImagePath = function (?string $image) {
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            $path = parse_url($image, PHP_URL_PATH) ?: '';

            foreach (['/public/storage/', '/storage/'] as $marker) {
                if (str_contains($path, $marker)) {
                    $candidate = public_path('storage/'.ltrim(\Illuminate\Support\Str::after($path, $marker), '/'));

                    return file_exists($candidate) ? $candidate : null;
                }
            }
        }

        if (str_starts_with($image, 'storage/')) {
            $candidate = public_path($image);

            return file_exists($candidate) ? $candidate : null;
        }

        $candidate = public_path('storage/'.ltrim($image, '/'));

        return file_exists($candidate) ? $candidate : null;
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quotation {{ $quote->quote_number }}</title>
    <style>
        @page { margin: 28px 34px 42px; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        tr { page-break-inside: avoid; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #6b7280; }
        .document-header td { vertical-align: top; }
        .brand-column { width: 57%; padding-right: 28px; }
        .document-column { width: 43%; text-align: right; }
        .logo { display: block; width: 235px; max-height: 64px; object-fit: contain; object-position: left top; margin-bottom: 9px; }
        .company-name { color: #111827; font-size: 17px; font-weight: 700; margin-bottom: 7px; }
        .company-details { color: #4b5563; font-size: 9px; line-height: 1.55; }
        .document-title { color: #111827; font-size: 25px; font-weight: 700; letter-spacing: .4px; line-height: 1.1; margin: 4px 0 7px; }
        .document-number { color: {{ $brandColor }}; font-size: 12px; font-weight: 700; }
        .accent-rule { height: 3px; margin: 16px 0 14px; background: {{ $brandColor }}; }
        .meta-table { margin-left: auto; width: 100%; }
        .meta-table td { padding: 4px 0 4px 10px; border-bottom: 1px solid #e5e7eb; }
        .meta-table .meta-label { width: 43%; color: #6b7280; font-size: 8px; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; }
        .meta-table .meta-value { color: #111827; font-size: 9px; font-weight: 600; }
        .party-table { margin-top: 17px; table-layout: fixed; }
        .party-table > tbody > tr > td { width: 50%; vertical-align: top; }
        .party-left { padding-right: 7px; }
        .party-right { padding-left: 7px; }
        .party-box { min-height: 82px; border: 1px solid #d9dee7; }
        .box-heading { padding: 6px 9px; border-bottom: 1px solid #d9dee7; background: #f3f4f6; color: #374151; font-size: 8px; font-weight: 700; letter-spacing: .55px; text-transform: uppercase; }
        .party-content { padding: 9px 10px; color: #4b5563; }
        .party-content div:first-child { color: #111827; font-weight: 700; margin-bottom: 2px; }
        .summary-table { margin: 14px 0 17px; table-layout: fixed; }
        .summary-table td { width: 33.33%; padding: 7px 9px; border: 1px solid #d9dee7; vertical-align: top; }
        .summary-label { display: block; color: #6b7280; font-size: 7.5px; font-weight: 700; letter-spacing: .35px; text-transform: uppercase; }
        .summary-value { display: block; margin-top: 2px; color: #111827; font-size: 9px; font-weight: 600; }
        .items-table { table-layout: fixed; }
        .items-table th { padding: 7px 6px; border-right: 1px solid #4b5563; background: #28313d; color: #ffffff; font-size: 7.5px; font-weight: 700; letter-spacing: .35px; text-align: left; text-transform: uppercase; }
        .items-table th:last-child { border-right: 0; }
        .items-table td { padding: 8px 6px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #d9dee7; color: #374151; vertical-align: top; }
        .items-table td:first-child { border-left: 1px solid #d9dee7; }
        .items-table tbody tr:nth-child(even) td { background: #fafafa; }
        .items-table .item-name { color: #111827; font-weight: 600; }
        .item-image { width: 38px; height: 38px; border: 1px solid #d9dee7; object-fit: cover; }
        .totals-wrap { margin-top: 14px; }
        .totals-spacer { width: 52%; }
        .totals-cell { width: 48%; }
        .totals-table td { padding: 6px 9px; border-bottom: 1px solid #e5e7eb; }
        .totals-label { color: #4b5563; }
        .totals-value { color: #111827; font-weight: 600; text-align: right; }
        .grand-row td { padding-top: 9px; padding-bottom: 9px; border-top: 2px solid #28313d; border-bottom: 0; background: #f3f4f6; color: #111827; font-size: 11px; font-weight: 700; }
        .grand-row .totals-label { border-left: 3px solid {{ $brandColor }}; }
        .notes-table { margin-top: 17px; table-layout: fixed; }
        .notes-table td { vertical-align: top; }
        .note-left { padding-right: 7px; }
        .note-right { padding-left: 7px; }
        .note-box { min-height: 72px; padding: 9px 10px; border: 1px solid #d9dee7; color: #4b5563; }
        .note-title { margin-bottom: 5px; color: #374151; font-size: 8px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
        .preline { white-space: pre-line; }
        .signature-table { margin-top: 58px; table-layout: fixed; }
        .signature-cell { width: 29%; vertical-align: bottom; }
        .signature-spacer { width: 6.5%; }
        .signature-line { border-top: 1px solid #4b5563; padding-top: 6px; color: #6b7280; font-size: 8px; text-align: center; text-transform: uppercase; letter-spacing: .35px; }
        .footer { position: fixed; right: 0; bottom: -20px; left: 0; padding-top: 7px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 7.5px; }
        .footer-right { text-align: right; }
    </style>
</head>
<body>
    <table class="document-header">
        <tr>
            <td class="brand-column">
                @if($logoSource)
                    <img src="{{ $logoSource }}" alt="{{ $companyName }}" class="logo">
                @else
                    <div class="company-name">{{ $companyName }}</div>
                @endif

                <div class="company-details">
                    @foreach($companyLines as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </td>
            <td class="document-column">
                <div class="document-title">QUOTATION</div>
                <div class="document-number">{{ $quote->quote_number ?: 'Q'.$quote->id }}</div>

                <table class="meta-table">
                    <tr><td class="meta-label">Quote date</td><td class="meta-value">{{ $formatDate($quote->quote_date ?: $quote->created_at) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="accent-rule"></div>

    <table class="party-table">
        <tr>
            <td class="party-left">
                <div class="party-box">
                    <div class="box-heading">Bill to</div>
                    <div class="party-content">@foreach($billToLines as $line)<div>{{ $line }}</div>@endforeach</div>
                </div>
            </td>
            <td class="party-right">
                <div class="party-box">
                    <div class="box-heading">Ship to</div>
                    <div class="party-content">@foreach($shipToLines as $line)<div>{{ $line }}</div>@endforeach</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td><span class="summary-label">Payment terms</span><span class="summary-value">{{ $paymentTerms }}</span></td>
            <td><span class="summary-label">Shipping method</span><span class="summary-value">{{ $shippingMethod }}</span></td>
            <td><span class="summary-label">Production time</span><span class="summary-value">{{ $productionTime }}</span></td>
        </tr>
        <tr>
            <td><span class="summary-label">Transit time</span><span class="summary-value">{{ $transitTime }}</span></td>
            <td><span class="summary-label">Estimated departure</span><span class="summary-value">{{ $etd }}</span></td>
            <td><span class="summary-label">Estimated arrival</span><span class="summary-value">{{ $eta }}</span></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">Image</th>
                <th style="width: 9%;" class="text-right">Qty</th>
                <th style="width: 14%;">Item code</th>
                <th style="width: 13%;">Color</th>
                <th style="width: 30%;">Description</th>
                <th style="width: 12%;" class="text-right">Rate</th>
                <th style="width: 14%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quote->items as $item)
                @php
                    $qty = (float) ($item->quantity ?: $item->qty ?: 0);
                    $price = (float) ($item->price ?: $item->unit_price ?: 0);
                    $lineTotal = $price * $qty;
                    $imagePath = $resolveImagePath($item->preview_image);
                @endphp
                <tr>
                    <td class="text-center">@if($imagePath)<img src="{{ $imagePath }}" alt="Item" class="item-image">@else<span class="muted">-</span>@endif</td>
                    <td class="text-right">{{ $formatQuantity($qty) }}</td>
                    <td>{{ $item->item_code ?: $item->sku ?: '-' }}</td>
                    <td>{{ $item->color_variant_name ?: '-' }}</td>
                    <td class="item-name">{{ $item->item_name ?: $item->name ?: '-' }}</td>
                    <td class="text-right">{{ $formatMoney($price) }}</td>
                    <td class="text-right">{{ $formatMoney($lineTotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center muted">No line items available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-wrap">
        <tr>
            <td class="totals-spacer"></td>
            <td class="totals-cell">
                <table class="totals-table">
                    <tr><td class="totals-label">Subtotal</td><td class="totals-value">{{ $formatMoney($quote->sub_total ?: 0) }}</td></tr>
                    @foreach($charges as $charge)
                        <tr><td class="totals-label">{{ $formatChargeLabel($charge) }}</td><td class="totals-value">{{ $formatMoney($charge['amount'] ?: 0) }}</td></tr>
                    @endforeach
                    <tr class="grand-row"><td class="totals-label">Grand total</td><td class="totals-value">{{ $formatMoney($quote->grand_total ?: 0) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    @if($remarks || $terms)
        <table class="notes-table">
            <tr>
                @if($remarks)<td class="{{ $terms ? 'note-left' : '' }}"><div class="note-box"><div class="note-title">Remarks</div><div class="preline">{{ $remarks }}</div></div></td>@endif
                @if($terms)<td class="{{ $remarks ? 'note-right' : '' }}"><div class="note-box"><div class="note-title">Terms &amp; conditions</div><div class="preline">{{ $terms }}</div></div></td>@endif
            </tr>
        </table>
    @endif

    <table class="signature-table">
        <tr>
            <td class="signature-cell"><div class="signature-line">Prepared by</div></td>
            <td class="signature-spacer"></td>
            <td class="signature-cell"><div class="signature-line">Approved by</div></td>
            <td class="signature-spacer"></td>
            <td class="signature-cell"><div class="signature-line">Customer acceptance</div></td>
        </tr>
    </table>

    <table class="footer">
        <tr>
            <td>{{ $companyName }}</td>
            <td class="footer-right">Quotation {{ $quote->quote_number ?: 'Q'.$quote->id }}</td>
        </tr>
    </table>
</body>
</html>
