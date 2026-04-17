@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $organization = $quote->organization;
    $salesPerson = $quote->user;
    $charges = $chargeManager->extract($quote->loadMissing('additionalCharges'), 'quote');
    $selectedBillingAddress = trim((string) data_get($quote->billing_address, 'address'));
    $selectedShippingAddress = trim((string) data_get($quote->shipping_address, 'address'));
    $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))->map(fn ($line) => trim((string) $line))->filter()->values()->all();

    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9';
    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Tel: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: ' . core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
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
    $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge')
        . (($charge['type'] ?? 'value') === 'percentage' ? ' (' . rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ''), '0'), '.') . '%)' : '');
    $etd = data_get($quote, 'etd')
    ? \Carbon\Carbon::parse(data_get($quote, 'etd'))->format('Y-m-d')
    : (optional($quote->expired_at)?->format('Y-m-d'));

$eta = data_get($quote, 'eta')
    ? \Carbon\Carbon::parse(data_get($quote, 'eta'))->format('Y-m-d')
    : '-';

    $resolveImagePath = function (?string $image) {
        if (! $image) {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            $parts = parse_url($image);
            $path = $parts['path'] ?? '';

            if (str_contains($path, '/public/storage/')) {
                $relative = ltrim(Str::after($path, '/public/storage/'), '/');
                $candidate = public_path('storage/' . $relative);

                return file_exists($candidate) ? $candidate : null;
            }

            if (str_contains($path, '/storage/')) {
                $relative = ltrim(Str::after($path, '/storage/'), '/');
                $candidate = public_path('storage/' . $relative);

                return file_exists($candidate) ? $candidate : null;
            }
        }

        if (str_starts_with($image, 'storage/')) {
            $candidate = public_path($image);

            return file_exists($candidate) ? $candidate : null;
        }

        $candidate = public_path('storage/' . ltrim($image, '/'));

        return file_exists($candidate) ? $candidate : null;
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quote {{ $quote->quote_number }}</title>
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
        .summary-table td { border: 1px solid #d8e0ea; padding: 8px; width: 16.66%; }
        .summary-label { display: block; color: #6b7280; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
        .summary-value { font-weight: 700; }
        .items-table th { background: {{ $brandColor }}; color: #fff; border: 1px solid {{ $brandColor }}; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { border: 1px solid #d8e0ea; padding: 8px; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .item-image { width: 42px; height: 42px; object-fit: cover; border: 1px solid #d8e0ea; border-radius: 6px; }
        .totals-table { width: 340px; margin-left: auto; margin-top: 16px; }
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
                <div class="title brand">Quote</div>
                <table class="meta-table">
                    <tr><td class="meta-label">Quote #</td><td>{{ $quote->quote_number ?: ('Q' . $quote->id) }}</td></tr>
                    <tr><td class="meta-label">Quote Date</td><td>{{ $quote->quote_date ? core()->formatDate($quote->quote_date, 'Y-m-d') : core()->formatDate($quote->created_at, 'Y-m-d') }}</td></tr>
                    <tr><td class="meta-label">Sales Person</td><td>{{ $salesPerson?->name ?: '-' }}</td></tr>
                    <tr><td class="meta-label">Status</td><td>{{ ucfirst($quote->status ?: 'draft') }}</td></tr>
                    <tr><td class="meta-label">Expiry Date</td><td>{{ $quote->expired_at ? core()->formatDate($quote->expired_at, 'Y-m-d') : '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="party-table" style="margin-top: 16px;">
        <tr>
            <td style="width: 50%; padding-right: 8px; vertical-align: top;"><div class="section-box"><div class="section-title">Bill To</div>@foreach ($billToLines as $line)<div>{{ $line }}</div>@endforeach</div></td>
            <td style="width: 50%; padding-left: 8px; vertical-align: top;"><div class="section-box"><div class="section-title">Ship To</div>@foreach ($shipToLines as $line)<div>{{ $line }}</div>@endforeach</div></td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td><span class="summary-label">Payment Terms</span><span class="summary-value">{{ $paymentTerms }}</span></td>
            <td><span class="summary-label">Shipping Method</span><span class="summary-value">{{ $shippingMethod }}</span></td>
            <td><span class="summary-label">Production Time</span><span class="summary-value">{{ $productionTime }}</span></td>
            <td><span class="summary-label">Transit Time</span><span class="summary-value">{{ $transitTime }}</span></td>
            <td><span class="summary-label">ETD</span><span class="summary-value">{{ $etd ?: '-' }}</span></td>
            <td><span class="summary-label">ETA</span><span class="summary-value">{{ $eta }}</span></td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 9%;" class="text-center">Image</th>
                <th style="width: 9%;" class="text-right">Qty</th>
                <th style="width: 14%;">Item #</th>
                <th style="width: 12%;">Colors</th>
                <th style="width: 31%;">Description</th>
                <th style="width: 12%;" class="text-right">Rate</th>
                <th style="width: 13%;" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($quote->items as $item)
                @php
                    $qty = (float) ($item->quantity ?: $item->qty ?: 0);
                    $price = (float) ($item->price ?: $item->unit_price ?: 0);
                    $lineTotal = $price * $qty;
                    $imagePath = $resolveImagePath($item->preview_image);
                @endphp
                <tr>
                    <td class="text-center">
                        @if ($imagePath)
                            <img src="{{ $imagePath }}" alt="Item Image" class="item-image">
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">{{ rtrim(rtrim(number_format($qty, 4, '.', ''), '0'), '.') }}</td>
                    <td>{{ $item->item_code ?: $item->sku ?: '-' }}</td>
                    <td>{{ $item->color_variant_name ?: '-' }}</td>
                    <td>{{ $item->item_name ?: $item->name ?: '-' }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($price, 2) }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($lineTotal, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No line items available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr><td class="totals-label">Sub Total</td><td class="totals-value">{{ core()->formatBasePrice($quote->sub_total ?: 0, 2) }}</td></tr>
        @foreach ($charges as $charge)
            <tr><td class="totals-label">{{ $formatChargeLabel($charge) }}</td><td class="totals-value">{{ core()->formatBasePrice($charge['amount'] ?: 0, 2) }}</td></tr>
        @endforeach
        <tr class="grand-row"><td class="totals-label">Grand Total</td><td class="totals-value">{{ core()->formatBasePrice($quote->grand_total ?: 0, 2) }}</td></tr>
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
            <td class="sign-cell" style="padding-right:0;"><div class="sign-line">Customer Acceptance</div></td>
        </tr>
    </table>
</div>
</body>
</html>
