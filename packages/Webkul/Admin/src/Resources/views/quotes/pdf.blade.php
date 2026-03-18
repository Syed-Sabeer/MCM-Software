@php
    $organization = $quote->organization;
    $salesPerson = $quote->user;

    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $companyLines = array_filter([
        core()->getConfigData('general.general.company_info.address'),
        core()->getConfigData('general.general.company_info.telephone') ? 'Phone: ' . core()->getConfigData('general.general.company_info.telephone') : null,
        core()->getConfigData('general.general.company_info.cell') ? 'Cell: ' . core()->getConfigData('general.general.company_info.cell') : null,
        core()->getConfigData('general.general.company_info.email') ? 'Email: ' . core()->getConfigData('general.general.company_info.email') : null,
    ]);

    $resolveImage = function ($path) {
        if (! $path) {
            return null;
        }

        $imageUrlPath = parse_url($path, PHP_URL_PATH) ?: $path;

        if (str_contains($imageUrlPath, '/public/storage/')) {
            $relativeStoragePath = \Illuminate\Support\Str::after($imageUrlPath, '/public/storage/');
            $localStoragePath = public_path('storage/' . $relativeStoragePath);

            return file_exists($localStoragePath) ? $localStoragePath : null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        return file_exists($path) ? $path : null;
    };

    $billToLines = array_filter([
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
        $organization?->name,
        $organization?->shipping_street ?: $organization?->billing_street,
        trim(implode(' ', array_filter([
            $organization?->shipping_city ?: $organization?->billing_city,
            $organization?->shipping_state ?: $organization?->billing_state,
            $organization?->shipping_postcode ?: $organization?->billing_postcode,
        ]))),
        $organization?->shipping_country ?: $organization?->billing_country,
        $organization?->phone ? 'Phone: ' . $organization->phone : null,
    ]);

    $items = $quote->items->map(function ($item) use ($resolveImage) {
        $qty = (float) ($item->quantity ?: $item->qty ?: 0);
        $price = (float) ($item->price ?: $item->unit_price ?: 0);
        $lineTotal = ($price * $qty) + (float) ($item->tax_amount ?: 0) - (float) ($item->discount_amount ?: 0);

        return [
            'preview_image' => $resolveImage($item->preview_image),
            'item_code'     => $item->item_code ?: $item->sku,
            'item_name'     => $item->item_name ?: $item->name,
            'color'         => $item->color_variant_name,
            'quantity'      => rtrim(rtrim(number_format($qty, 4, '.', ''), '0'), '.'),
            'price'         => core()->formatBasePrice($price),
            'discount'      => core()->formatBasePrice($item->discount_amount ?: 0),
            'tax'           => core()->formatBasePrice($item->tax_amount ?: 0),
            'total'         => core()->formatBasePrice($lineTotal),
        ];
    })->values()->all();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Quote {{ $quote->quote_number }}</title>
</head>
<body>
    <x-admin::documents.standard
        mode="print"
        title="Quote"
        :accent-color="core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9'"
        :company="[
            'logo' => $logoPath,
            'name' => $companyName,
            'lines' => $companyLines,
        ]"
        :meta="[
            ['label' => 'Quote #', 'value' => $quote->quote_number ?: ('#' . $quote->id)],
            ['label' => 'Quote Date', 'value' => $quote->quote_date ? core()->formatDate($quote->quote_date, 'M d, Y') : core()->formatDate($quote->created_at, 'M d, Y')],
            ['label' => 'Sales Person', 'value' => $salesPerson?->name ?: '-'],
            ['label' => 'Status', 'value' => ucfirst($quote->status ?: 'draft')],
            ['label' => 'Expiry Date', 'value' => $quote->expired_at ? core()->formatDate($quote->expired_at, 'M d, Y') : null],
        ]"
        :party-blocks="[
            ['title' => 'Bill To', 'lines' => $billToLines],
            ['title' => 'Ship To', 'lines' => $shipToLines],
        ]"
        :summary-rows="[
            ['label' => 'Customer ID', 'value' => $organization?->id ?: null],
            ['label' => 'Contact', 'value' => optional($quote->person)->name ?: null],
            ['label' => 'Quote Ref', 'value' => $quote->quote_number ?: null],
            ['label' => 'Created On', 'value' => $quote->created_at?->format('M d, Y')],
        ]"
        :columns="[
            ['key' => 'preview_image', 'label' => 'Image', 'type' => 'image', 'align' => 'text-center', 'width' => '10%'],
            ['key' => 'item_code', 'label' => 'Item Code', 'width' => '14%'],
            ['key' => 'item_name', 'label' => 'Description', 'width' => '24%'],
            ['key' => 'color', 'label' => 'Color', 'width' => '10%'],
            ['key' => 'quantity', 'label' => 'Qty', 'align' => 'text-right', 'width' => '8%'],
            ['key' => 'price', 'label' => 'Rate', 'align' => 'text-right', 'width' => '11%'],
            ['key' => 'discount', 'label' => 'Discount', 'align' => 'text-right', 'width' => '11%'],
            ['key' => 'tax', 'label' => 'Tax', 'align' => 'text-right', 'width' => '10%'],
            ['key' => 'total', 'label' => 'Amount', 'align' => 'text-right', 'width' => '12%'],
        ]"
        :items="$items"
        :totals="[
            ['label' => 'Sub Total', 'value' => core()->formatBasePrice($quote->sub_total ?: 0), 'always' => true],
            ['label' => 'Discount', 'value' => core()->formatBasePrice($quote->discount_amount ?: 0), 'always' => true],
            ['label' => 'Tax', 'value' => core()->formatBasePrice($quote->tax_amount ?: 0), 'always' => true],
            ['label' => 'Adjustment', 'value' => core()->formatBasePrice($quote->adjustment_amount ?: 0)],
            ['label' => 'Grand Total', 'value' => core()->formatBasePrice($quote->grand_total ?: 0), 'highlight' => true, 'always' => true],
        ]"
        :notes="[
            ['label' => 'Remarks', 'value' => $quote->description ?: $quote->notes],
            ['label' => 'Commercial Terms', 'value' => $quote->terms],
        ]"
        :signatures="['Prepared By', 'Approved By', 'Customer Acceptance']"
    />
</body>
</html>
