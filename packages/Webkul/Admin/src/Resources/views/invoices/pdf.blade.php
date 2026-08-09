@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $organization = $invoice->organization;
    $charges = $chargeManager->extract($invoice->loadMissing('additionalCharges'), 'proforma');
    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#b91c1c';
    $configuredLogo = core()->getConfigData('general.general.admin_logo.logo_image');
    $configuredLogoPath = $configuredLogo ? public_path('storage/'.$configuredLogo) : null;
    $fallbackLogoPath = public_path('logo/mcmmain-pdf.png');
    $logoPath = $configuredLogoPath && file_exists($configuredLogoPath) ? $configuredLogoPath : (file_exists($fallbackLogoPath) ? $fallbackLogoPath : null);
    $logoSource = $logoPath ? 'data:'.(mime_content_type($logoPath) ?: 'image/png').';base64,'.base64_encode(file_get_contents($logoPath)) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Tel: '.core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: '.core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: '.core()->getConfigData('general.general.company_info.email') : null,
    ]);
    $addressLines = fn ($value) => collect(preg_split("/\r\n|\n|\r/", (string) $value))->map(fn ($line) => trim((string) $line))->filter()->values()->all();
    $selectedBillingAddress = trim((string) data_get($invoice->billing_address, 'address'));
    $selectedShippingAddress = trim((string) data_get($invoice->shipping_address, 'address'));
    $billToLines = array_filter([
        $organization?->name,
        ...($selectedBillingAddress !== '' ? $addressLines($selectedBillingAddress) : [
            $organization?->billing_street ?: $organization?->shipping_street,
            trim(implode(', ', array_filter([$organization?->billing_city ?: $organization?->shipping_city, $organization?->billing_state ?: $organization?->shipping_state, $organization?->billing_postcode ?: $organization?->shipping_postcode, $organization?->billing_country ?: $organization?->shipping_country]))),
        ]),
        $organization?->phone ? 'Phone: '.$organization->phone : null,
        data_get($organization, 'email') ? 'Email: '.data_get($organization, 'email') : null,
    ]);
    $shipToLines = array_filter([
        $organization?->name,
        ...($selectedShippingAddress !== '' ? $addressLines($selectedShippingAddress) : [
            $organization?->shipping_street ?: $organization?->billing_street,
            trim(implode(', ', array_filter([$organization?->shipping_city ?: $organization?->billing_city, $organization?->shipping_state ?: $organization?->billing_state, $organization?->shipping_postcode ?: $organization?->billing_postcode, $organization?->shipping_country ?: $organization?->billing_country]))),
        ]),
        $organization?->phone ? 'Phone: '.$organization->phone : null,
        data_get($organization, 'email') ? 'Email: '.data_get($organization, 'email') : null,
    ]);
    $sourceProforma = $invoice->proformaInvoice;
    $sourceQuote = $invoice->quote ?: $sourceProforma?->quote;
    $paymentTerms = $invoice->payment_term ?: data_get($invoice, 'payment_terms') ?: $sourceProforma?->payment_term ?: $sourceQuote?->payment_term ?: '-';
    $shippingMethod = data_get($invoice, 'shipping_method') ?: data_get($sourceProforma, 'shipping_method') ?: $sourceQuote?->shipping_method ?: '-';
    $productionTime = data_get($invoice, 'production_time') ?: data_get($sourceProforma, 'production_time') ?: $sourceQuote?->production_time ?: '-';
    $transitTime = data_get($invoice, 'transit_time') ?: data_get($sourceProforma, 'transit_time') ?: $sourceQuote?->transit_time ?: '-';
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
    $etd = $formatDate(data_get($invoice, 'etd') ?: data_get($sourceProforma, 'etd') ?: $sourceQuote?->etd);
    $eta = $formatDate(data_get($invoice, 'eta') ?: data_get($sourceProforma, 'eta') ?: $sourceQuote?->eta);
    $remarks = trim((string) ($invoice->notes ?: ''));
    $terms = trim((string) ($invoice->terms ?: ''));
    $advanceApplied = (float) ($invoice->advance_applied ?: 0);
    $invoicePayments = (float) ($invoice->received_amount ?: 0);
    $formatQuantity = fn ($value) => rtrim(rtrim(number_format((float) $value, 4, '.', ','), '0'), '.');
    $formatMoney = fn ($value) => core()->formatBasePrice((float) $value, 2);
    $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge').(($charge['type'] ?? 'value') === 'percentage' ? ' ('.rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ','), '0'), '.').'%)' : '');
    $resolveImagePath = function (?string $image) {
        if (! $image) return null;
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            $path = parse_url($image, PHP_URL_PATH) ?: '';
            foreach (['/public/storage/', '/storage/'] as $marker) {
                if (str_contains($path, $marker)) {
                    $candidate = public_path('storage/'.ltrim(\Illuminate\Support\Str::after($path, $marker), '/'));
                    return file_exists($candidate) ? $candidate : null;
                }
            }
        }
        $candidate = str_starts_with($image, 'storage/') ? public_path($image) : public_path('storage/'.ltrim($image, '/'));
        return file_exists($candidate) ? $candidate : null;
    };
    $documentTitle = 'FINAL INVOICE';
    $documentNumber = $invoice->invoice_number ?: 'INV-'.$invoice->id;
    $documentMeta = [
        ['label' => 'Issue date', 'value' => $formatDate($invoice->issue_date ?: $invoice->created_at)],
        ['label' => 'Proforma reference', 'value' => optional($invoice->proformaInvoice)->proforma_number ?: '-'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Final Invoice {{ $documentNumber }}</title>
    <style>@include('admin::documents.pdf.styles')</style>
</head>
<body>
    @include('admin::documents.pdf.header')

    <table class="party-table"><tr>
        <td class="party-left"><div class="party-box"><div class="box-heading">Bill to</div><div class="party-content">@foreach($billToLines as $line)<div>{{ $line }}</div>@endforeach</div></div></td>
        <td class="party-right"><div class="party-box"><div class="box-heading">Ship to</div><div class="party-content">@foreach($shipToLines as $line)<div>{{ $line }}</div>@endforeach</div></div></td>
    </tr></table>

    <table class="summary-table">
        <tr>
            <td style="width:33.33%"><span class="summary-label">Payment terms</span><span class="summary-value">{{ $paymentTerms }}</span></td>
            <td style="width:33.33%"><span class="summary-label">Shipping method</span><span class="summary-value">{{ $shippingMethod }}</span></td>
            <td style="width:33.33%"><span class="summary-label">Production time</span><span class="summary-value">{{ $productionTime }}</span></td>
        </tr>
        <tr>
            <td><span class="summary-label">Transit time</span><span class="summary-value">{{ $transitTime }}</span></td>
            <td><span class="summary-label">ETD</span><span class="summary-value">{{ $etd }}</span></td>
            <td><span class="summary-label">ETA</span><span class="summary-value">{{ $eta }}</span></td>
        </tr>
    </table>

    <table class="items-table">
        <thead><tr>
            <th style="width:8%" class="text-center">Image</th><th style="width:9%" class="text-right">Qty</th><th style="width:14%">Item code</th><th style="width:13%">Color</th><th style="width:30%">Description</th><th style="width:12%" class="text-right">Rate</th><th style="width:14%" class="text-right">Amount</th>
        </tr></thead>
        <tbody>
            @forelse($invoice->items as $item)
                @php
                    $qty = (float) ($item->qty ?: 0);
                    $price = (float) ($item->unit_price ?: 0);
                    $lineTotal = (float) ($item->line_total ?: (($price * $qty) + ($item->tax_amount ?: 0) - ($item->discount_amount ?: 0)));
                    $imagePath = $resolveImagePath($item->preview_image);
                @endphp
                <tr>
                    <td class="text-center">@if($imagePath)<img src="{{ $imagePath }}" alt="Item" class="item-image">@else<span class="muted">-</span>@endif</td>
                    <td class="text-right">{{ $formatQuantity($qty) }}</td><td>{{ $item->item_code ?: '-' }}</td><td>{{ $item->color_variant_name ?: '-' }}</td><td class="item-name">{{ $item->item_name ?: '-' }}</td><td class="text-right">{{ $formatMoney($price) }}</td><td class="text-right">{{ $formatMoney($lineTotal) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center muted">No line items available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-wrap"><tr><td class="totals-spacer"></td><td class="totals-cell"><table class="totals-table">
        <tr><td class="totals-label">Subtotal</td><td class="totals-value">{{ $formatMoney($invoice->subtotal ?: 0) }}</td></tr>
        @foreach($charges as $charge)<tr><td class="totals-label">{{ $formatChargeLabel($charge) }}</td><td class="totals-value">{{ $formatMoney($charge['amount'] ?: 0) }}</td></tr>@endforeach
        <tr><td class="totals-label">Grand total</td><td class="totals-value">{{ $formatMoney($invoice->grand_total ?: 0) }}</td></tr>
        @if($advanceApplied > 0)
            <tr><td class="totals-label">Advance applied</td><td class="totals-value">- {{ $formatMoney($advanceApplied) }}</td></tr>
        @endif
        @if($invoicePayments > 0)
            <tr><td class="totals-label">Invoice payments</td><td class="totals-value">- {{ $formatMoney($invoicePayments) }}</td></tr>
        @endif
        <tr class="grand-row"><td class="totals-label">Balance due</td><td class="totals-value">{{ $formatMoney($invoice->remaining_amount ?: 0) }}</td></tr>
    </table></td></tr></table>

    @if($remarks || $terms)<table class="notes-table"><tr>
        @if($remarks)<td class="{{ $terms ? 'note-left' : '' }}"><div class="note-box"><div class="note-title">Remarks</div><div class="preline">{{ $remarks }}</div></div></td>@endif
        @if($terms)<td class="{{ $remarks ? 'note-right' : '' }}"><div class="note-box"><div class="note-title">Terms &amp; conditions</div><div class="preline">{{ $terms }}</div></div></td>@endif
    </tr></table>@endif

    @include('admin::documents.pdf.signatures', ['signatureLabels' => ['Prepared by', 'Approved by', 'Customer confirmation']])
    @include('admin::documents.pdf.footer')
</body>
</html>
