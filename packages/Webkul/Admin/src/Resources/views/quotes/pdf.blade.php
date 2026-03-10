@php
    $organization = $quote->organization;
    $salesPerson = $quote->user;

    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyAddress = core()->getConfigData('general.general.company_info.address');
    $companyPhone = core()->getConfigData('general.general.company_info.telephone') ?: core()->getConfigData('general.general.company_info.cell');
    $companyEmail = core()->getConfigData('general.general.company_info.email');
    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9';

    $customerAddressLines = array_filter([
        $organization?->billing_street ?: $organization?->shipping_street,
        trim(implode(' ', array_filter([
            $organization?->billing_city ?: $organization?->shipping_city,
            $organization?->billing_state ?: $organization?->shipping_state,
            $organization?->billing_postcode ?: $organization?->shipping_postcode,
        ]))),
        $organization?->billing_country ?: $organization?->shipping_country,
    ]);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        * { box-sizing: border-box; font-family: DejaVu Sans, sans-serif; }
        body { margin: 0; color: #1f2937; font-size: 11px; background: #ffffff; }
        .page { padding: 24px; }
        .header-table, .info-table, .items-table, .summary-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: top; }
        .logo { max-width: 110px; max-height: 80px; margin-bottom: 8px; }
        .title { font-size: 30px; font-weight: bold; text-align: right; color: {{ $brandColor }}; letter-spacing: 2px; }
        .section-title { font-size: 13px; font-weight: bold; color: {{ $brandColor }}; margin-bottom: 8px; text-transform: uppercase; }
        .muted { color: #6b7280; }
        .meta-table td { padding: 3px 0; }
        .meta-label { width: 120px; color: #6b7280; }
        .info-wrap { margin-top: 22px; }
        .info-box { width: 48%; vertical-align: top; }
        .info-box-inner { border: 1px solid #dbe5f0; padding: 14px; min-height: 118px; background: #f8fbff; border-radius: 8px; }
        .hero-strip { margin-top: 18px; height: 12px; background: linear-gradient(90deg, {{ $brandColor }}, #dbeafe); border-radius: 999px; }
        .items-table { margin-top: 22px; }
        .items-table th { background: {{ $brandColor }}; color: #ffffff; border: 1px solid {{ $brandColor }}; padding: 9px 8px; text-align: left; font-weight: bold; }
        .items-table td { border: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
        .items-table tbody tr:nth-child(even) td { background: #f8fbff; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .item-image { width: 44px; height: 44px; border-radius: 6px; border: 1px solid #dbe5f0; object-fit: cover; }
        .meta-card { border: 1px solid #dbe5f0; border-radius: 8px; padding: 14px; background: #ffffff; }
        .summary-wrap { margin-top: 18px; width: 100%; }
        .summary-table { width: 320px; margin-left: auto; }
        .summary-table td { border: 1px solid #dbe5f0; padding: 8px; }
        .summary-table .label { background: #f8fbff; font-weight: bold; }
        .summary-table .grand td { font-weight: bold; background: #eef6ff; color: {{ $brandColor }}; }
    </style>
</head>
<body>
    <div class="page">
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    @if ($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="logo">
                    @endif

                    <div class="section-title">{{ $companyName }}</div>

                    @if ($companyAddress)
                        <div>{{ $companyAddress }}</div>
                    @endif

                    @if ($companyPhone)
                        <div>Phone: {{ $companyPhone }}</div>
                    @endif

                    @if ($companyEmail)
                        <div>Email: {{ $companyEmail }}</div>
                    @endif
                </td>

                <td style="width: 45%;">
                    <div class="title">QUOTE</div>

                    <div class="meta-card" style="margin-top: 10px;">
                        <table class="meta-table" style="width: 100%;">
                            <tr>
                                <td class="meta-label">Quote #</td>
                                <td>{{ $quote->quote_number ?: ('#' . $quote->id) }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Quote Date</td>
                                <td>{{ $quote->quote_date ? core()->formatDate($quote->quote_date, 'M d, Y') : core()->formatDate($quote->created_at, 'M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Sales Person</td>
                                <td>{{ $salesPerson?->name ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Status</td>
                                <td>{{ ucfirst($quote->status ?: 'draft') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="hero-strip"></div>

        <table class="info-table info-wrap">
            <tr>
                <td class="info-box">
                    <div class="info-box-inner">
                        <div class="section-title">Customer Details</div>
                        <div><strong>{{ $organization?->name ?: '—' }}</strong></div>

                        @foreach ($customerAddressLines as $line)
                            @if ($line !== '')
                                <div>{{ $line }}</div>
                            @endif
                        @endforeach

                        @if ($organization?->phone)
                            <div>Phone: {{ $organization->phone }}</div>
                        @endif

                        @if (data_get($organization, 'email'))
                            <div>Email: {{ data_get($organization, 'email') }}</div>
                        @endif
                    </div>
                </td>

                <td style="width: 4%;"></td>

                <td class="info-box">
                    <div class="info-box-inner">
                        <div class="section-title">Company Details</div>
                        <div><strong>{{ $companyName }}</strong></div>

                        @if ($companyAddress)
                            <div>{{ $companyAddress }}</div>
                        @endif

                        @if ($companyPhone)
                            <div>Phone: {{ $companyPhone }}</div>
                        @endif

                        @if ($companyEmail)
                            <div>Email: {{ $companyEmail }}</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Image</th>
                    <th style="width: 14%;">Item Code</th>
                    <th style="width: 24%;">Item</th>
                    <th style="width: 10%;">Color</th>
                    <th style="width: 8%;" class="text-right">Qty</th>
                    <th style="width: 11%;" class="text-right">Price</th>
                    <th style="width: 11%;" class="text-right">Discount</th>
                    <th style="width: 10%;" class="text-right">Tax</th>
                    <th style="width: 12%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quote->items as $item)
                    @php
                        $qty = $item->quantity ?: $item->qty ?: 0;
                        $price = $item->price ?: $item->unit_price ?: 0;
                        $lineTotal = ($price * $qty) + ($item->tax_amount ?: 0) - ($item->discount_amount ?: 0);
                        $itemImage = $item->preview_image ?: null;
                    @endphp
                    <tr>
                        <td class="text-center">
                            @if ($itemImage)
                                <img src="{{ $itemImage }}" alt="Item" class="item-image">
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $item->item_code ?: $item->sku ?: '—' }}</td>
                        <td>{{ $item->item_name ?: $item->name ?: '—' }}</td>
                        <td>{{ $item->color_variant_name ?: '—' }}</td>
                        <td class="text-right">{{ rtrim(rtrim(number_format((float) $qty, 4, '.', ''), '0'), '.') }}</td>
                        <td class="text-right">{{ core()->formatBasePrice($price) }}</td>
                        <td class="text-right">{{ core()->formatBasePrice($item->discount_amount ?: 0) }}</td>
                        <td class="text-right">{{ core()->formatBasePrice($item->tax_amount ?: 0) }}</td>
                        <td class="text-right">{{ core()->formatBasePrice($lineTotal) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-wrap">
            <table class="summary-table">
                <tr>
                    <td class="label">Sub Total</td>
                    <td class="text-right">{{ core()->formatBasePrice($quote->sub_total ?: 0) }}</td>
                </tr>
                <tr>
                    <td class="label">Discount</td>
                    <td class="text-right">{{ core()->formatBasePrice($quote->discount_amount ?: 0) }}</td>
                </tr>
                <tr>
                    <td class="label">Tax</td>
                    <td class="text-right">{{ core()->formatBasePrice($quote->tax_amount ?: 0) }}</td>
                </tr>
                @if ((float) ($quote->adjustment_amount ?: 0) !== 0.0)
                    <tr>
                        <td class="label">Adjustment</td>
                        <td class="text-right">{{ core()->formatBasePrice($quote->adjustment_amount) }}</td>
                    </tr>
                @endif
                <tr class="grand">
                    <td>Grand Total</td>
                    <td class="text-right">{{ core()->formatBasePrice($quote->grand_total ?: 0) }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
