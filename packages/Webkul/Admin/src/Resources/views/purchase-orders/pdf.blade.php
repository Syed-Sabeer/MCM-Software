@php
    $chargeManager = app(\Webkul\Core\Support\DocumentChargeManager::class);
    $organization = $purchaseOrder->organization;
    $charges = $chargeManager->extract($purchaseOrder->loadMissing('additionalCharges'), 'purchase_order');
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
    $selectedBillingAddress = trim((string) data_get($purchaseOrder->billing_address, 'address'));
    $selectedShippingAddress = trim((string) data_get($purchaseOrder->shipping_address, 'address'));
    $vendorLines = array_filter([
        $organization?->name,
        ...($selectedBillingAddress !== '' ? $addressLines($selectedBillingAddress) : [
            $organization?->billing_street ?: $organization?->shipping_street,
            trim(implode(', ', array_filter([$organization?->billing_city ?: $organization?->shipping_city, $organization?->billing_state ?: $organization?->shipping_state, $organization?->billing_postcode ?: $organization?->shipping_postcode, $organization?->billing_country ?: $organization?->shipping_country]))),
        ]),
        $organization?->phone ? 'Phone: '.$organization->phone : null,
        data_get($organization, 'email') ? 'Email: '.data_get($organization, 'email') : null,
    ]);
    $shipToLines = array_filter([
        $companyName,
        ...($selectedShippingAddress !== '' ? $addressLines($selectedShippingAddress) : [core()->getConfigData('general.general.company_info.address')]),
        core()->getConfigData('general.general.company_info.telephone') ? 'Phone: '.core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: '.core()->getConfigData('general.general.company_info.email') : null,
    ]);
    $remarks = trim((string) ($purchaseOrder->notes ?: ''));
    $terms = trim((string) ($purchaseOrder->terms ?: ''));
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
    $formatPkr = fn ($value) => 'PKR '.number_format((float) $value, 2, '.', ',');
    $formatChargeLabel = fn (array $charge) => ($charge['name'] ?? 'Charge').(($charge['type'] ?? 'value') === 'percentage' ? ' ('.rtrim(rtrim(number_format((float) ($charge['value'] ?? 0), 2, '.', ','), '0'), '.').'%)' : '');
    $documentTitle = 'PURCHASE ORDER';
    $documentNumber = $purchaseOrder->po_number;
    $documentMeta = [
        ['label' => 'Issue date', 'value' => $formatDate($purchaseOrder->created_at)],
        ['label' => 'Vendor quote', 'value' => optional($purchaseOrder->vendorQuote)->vendor_quote_number ?: '-'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Order {{ $documentNumber }}</title>
    <style>@include('admin::documents.pdf.styles')</style>
</head>
<body>
    @include('admin::documents.pdf.header')

    <table class="party-table"><tr>
        <td class="party-left"><div class="party-box"><div class="box-heading">Vendor</div><div class="party-content">@foreach($vendorLines as $line)<div>{{ $line }}</div>@endforeach</div></div></td>
        <td class="party-right"><div class="party-box"><div class="box-heading">Ship to / factory</div><div class="party-content">@foreach($shipToLines as $line)<div>{{ $line }}</div>@endforeach</div></div></td>
    </tr></table>

    <table class="summary-table"><tr>
        <td style="width:25%"><span class="summary-label">Payment terms</span><span class="summary-value">{{ $purchaseOrder->payment_term ?: '-' }}</span></td>
        <td style="width:25%"><span class="summary-label">Shipping method</span><span class="summary-value">{{ $purchaseOrder->shipping_method ?: '-' }}</span></td>
        <td style="width:25%"><span class="summary-label">First delivery</span><span class="summary-value">{{ $formatDate($purchaseOrder->completion_date) }}</span></td>
        <td style="width:25%"><span class="summary-label">Last delivery</span><span class="summary-value">{{ $formatDate($purchaseOrder->last_delivery_date) }}</span></td>
    </tr></table>

    <table class="items-table">
        <thead><tr><th style="width:10%" class="text-right">Qty</th><th style="width:20%">Item code</th><th style="width:42%">Description</th><th style="width:14%" class="text-right">Rate</th><th style="width:14%" class="text-right">Amount</th></tr></thead>
        <tbody>
            @forelse($purchaseOrder->items as $item)
                <tr>
                    <td class="text-right">{{ $formatQuantity($item->ordered_quantity ?: $item->quantity ?: 0) }}</td>
                    <td>{{ $item->item_code ?: $item->sku ?: $item->material_name ?: $item->item ?: '-' }}</td>
                    <td class="item-name">{{ $item->description ?: $item->material_name ?: $item->item ?: '-' }}@if($item->unit)<div class="muted" style="margin-top:3px;font-size:8px">UOM: {{ $item->unit }}</div>@endif</td>
                    <td class="text-right">{{ $formatPkr($item->price ?: 0) }}</td><td class="text-right">{{ $formatPkr($item->total ?: 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center muted">No line items available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals-wrap"><tr><td class="totals-spacer"></td><td class="totals-cell"><table class="totals-table">
        <tr><td class="totals-label">Subtotal</td><td class="totals-value">{{ $formatPkr($purchaseOrder->sub_total ?: 0) }}</td></tr>
        @foreach($charges as $charge)<tr><td class="totals-label">{{ $formatChargeLabel($charge) }}</td><td class="totals-value">{{ $formatPkr($charge['amount'] ?: 0) }}</td></tr>@endforeach
        <tr class="grand-row"><td class="totals-label">Grand total</td><td class="totals-value">{{ $formatPkr($purchaseOrder->grand_total ?: 0) }}</td></tr>
    </table></td></tr></table>

    @if($remarks || $terms)<table class="notes-table"><tr>
        @if($remarks)<td class="{{ $terms ? 'note-left' : '' }}"><div class="note-box"><div class="note-title">Remarks</div><div class="preline">{{ $remarks }}</div></div></td>@endif
        @if($terms)<td class="{{ $remarks ? 'note-right' : '' }}"><div class="note-box"><div class="note-title">Terms &amp; conditions</div><div class="preline">{{ $terms }}</div></div></td>@endif
    </tr></table>@endif

    @include('admin::documents.pdf.signatures', ['signatureLabels' => ['Prepared by', 'Approved by', 'Vendor confirmation']])
    @include('admin::documents.pdf.footer')
</body>
</html>
