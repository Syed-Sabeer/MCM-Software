@php
    $organization = $proformaInvoice->organization;
    $salesPerson = $proformaInvoice->salesOwner;

    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Tel: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Tel: ' . core()->getConfigData('general.general.company_info.cell') : null,
    ]);

    $billToLines = array_filter([
        $organization?->name,
        data_get($proformaInvoice->billing_address, 'address') ?: $organization?->billing_street ?: $organization?->shipping_street,
        trim(implode(', ', array_filter([
            $organization?->billing_city ?: $organization?->shipping_city,
            $organization?->billing_state ?: $organization?->shipping_state,
            $organization?->billing_postcode ?: $organization?->shipping_postcode,
            $organization?->billing_country ?: $organization?->shipping_country,
        ]))),
    ]);

    $shipToLines = array_filter([
        $organization?->name,
        data_get($proformaInvoice->shipping_address, 'address') ?: $organization?->shipping_street ?: $organization?->billing_street,
        trim(implode(', ', array_filter([
            $organization?->shipping_city ?: $organization?->billing_city,
            $organization?->shipping_state ?: $organization?->billing_state,
            $organization?->shipping_postcode ?: $organization?->billing_postcode,
            $organization?->shipping_country ?: $organization?->billing_country,
        ]))),
    ]);

    $paymentTerms = $proformaInvoice->payment_term ?: data_get($proformaInvoice, 'payment_terms') ?: '-';
    $shippingMethod = data_get($proformaInvoice, 'shipping_method') ?: '-';
    $productionTime = data_get($proformaInvoice, 'production_time') ?: '-';
    $transitTime = data_get($proformaInvoice, 'transit_time') ?: '-';
    $shipDateRequired = optional($proformaInvoice->due_date)->format('Y-m-d') ?: '-';
    $etd = data_get($proformaInvoice, 'etd') ?: '-';

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
    <title>Proforma {{ $proformaInvoice->proforma_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; margin: 0; }
        .page { padding: 24px; }
        .header-table, .party-table, .items-table, .totals-table, .summary-table, .notes-table, .sign-table { width: 100%; border-collapse: collapse; }
        .brand { color: #0E90D9; }
        .title { font-size: 24px; font-weight: 700; text-align: right; }
        .logo { max-width: 120px; max-height: 70px; margin-bottom: 8px; }
        .meta-label { font-weight: 700; width: 35%; background: #f5f7fb; }
        .meta-table td { border: 1px solid #d8e0ea; padding: 7px 10px; }
        .section-box { border: 1px solid #d8e0ea; padding: 12px; min-height: 90px; }
        .section-title { color: #0E90D9; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; }
        .summary-table { margin: 16px 0; }
        .summary-table td { border: 1px solid #d8e0ea; padding: 8px; width: 16.66%; }
        .summary-label { display: block; color: #6b7280; font-size: 10px; text-transform: uppercase; margin-bottom: 4px; }
        .summary-value { font-weight: 700; }
        .items-table th { background: #0E90D9; color: #fff; border: 1px solid #0E90D9; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { border: 1px solid #d8e0ea; padding: 8px; vertical-align: top; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .item-image { width: 42px; height: 42px; object-fit: cover; border: 1px solid #d8e0ea; border-radius: 6px; }
        .totals-table { width: 320px; margin-left: auto; margin-top: 16px; }
        .totals-table td { border: 1px solid #d8e0ea; padding: 8px 10px; }
        .totals-label { background: #f5f7fb; font-weight: 700; }
        .totals-value { text-align: right; font-weight: 700; }
        .grand-row td { background: #eef6ff; color: #0E90D9; font-weight: 700; }
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
                <div class="brand" style="font-size: 20px; font-weight: 700; margin-bottom: 6px;">{{ $companyName }}</div>
                @foreach ($companyLines as $line)
                    <div>{{ $line }}</div>
                @endforeach
            </td>
            <td style="width: 48%; vertical-align: top;">
                <div class="title brand">Proforma Invoice</div>
                <table class="meta-table">
                    <tr><td class="meta-label">Proforma #</td><td>{{ $proformaInvoice->proforma_number ?: ('PF-' . $proformaInvoice->id) }}</td></tr>
                    <tr><td class="meta-label">Quote #</td><td>{{ optional($proformaInvoice->quote)->quote_number ? 'Q' . ltrim((string) optional($proformaInvoice->quote)->quote_number, 'Q') : '-' }}</td></tr>
                    <tr><td class="meta-label">Issue Date</td><td>{{ $proformaInvoice->issue_date ? core()->formatDate($proformaInvoice->issue_date, 'Y-m-d') : core()->formatDate($proformaInvoice->created_at, 'Y-m-d') }}</td></tr>
                    <tr><td class="meta-label">Sales Person</td><td>{{ $salesPerson?->name ?: '-' }}</td></tr>
                    <tr><td class="meta-label">Status</td><td>{{ ucfirst($proformaInvoice->status ?: 'draft') }}</td></tr>
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
            <td><span class="summary-label">Ship Date Required</span><span class="summary-value">{{ $shipDateRequired }}</span></td>
            <td><span class="summary-label">ETD</span><span class="summary-value">{{ $etd }}</span></td>
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
            @forelse ($proformaInvoice->items as $item)
                @php
                    $qty = (float) ($item->qty ?: 0);
                    $price = (float) ($item->unit_price ?: 0);
                    $lineTotal = (float) ($item->line_total ?: (($price * $qty) + ($item->tax_amount ?: 0) - ($item->discount_amount ?: 0)));
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
                    <td>{{ $item->item_code ?: '-' }}</td>
                    <td>{{ $item->color_variant_name ?: '-' }}</td>
                    <td>{{ $item->item_name ?: '-' }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($price, 2) }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($lineTotal, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No line items available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-table">
        <tr><td class="totals-label">Sub Total</td><td class="totals-value">{{ core()->formatBasePrice($proformaInvoice->subtotal ?: 0, 2) }}</td></tr>
        <tr><td class="totals-label">Tarrifs</td><td class="totals-value">{{ core()->formatBasePrice($proformaInvoice->tax_amount ?: 0, 2) }}</td></tr>
        <tr><td class="totals-label">Freight</td><td class="totals-value">{{ core()->formatBasePrice($proformaInvoice->adjustment_amount ?: 0, 2) }}</td></tr>
        <tr class="grand-row"><td class="totals-label">Grand Total</td><td class="totals-value">{{ core()->formatBasePrice($proformaInvoice->grand_total ?: 0, 2) }}</td></tr>
    </table>

    <table class="notes-table">
        <tr>
            <td><div class="note-card"><div class="section-title">Remarks</div><div class="preline">{{ $proformaInvoice->notes ?: '-' }}</div></div></td>
            <td><div class="note-card"><div class="section-title">Terms & Conditions</div><div class="preline">{{ $proformaInvoice->terms ?: '-' }}</div></div></td>
        </tr>
    </table>

    <table class="sign-table">
        <tr>
            <td class="sign-cell"><div class="sign-line">Prepared By</div></td>
            <td class="sign-cell"><div class="sign-line">Approved By</div></td>
            <td class="sign-cell" style="padding-right:0;"><div class="sign-line">Customer Confirmation</div></td>
        </tr>
    </table>
</div>
</body>
</html>
