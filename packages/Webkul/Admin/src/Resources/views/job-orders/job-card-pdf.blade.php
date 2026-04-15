@php
    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9';
    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $formatStageQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Card {{ $jobOrder->job_order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; margin: 0; }
        .page { padding: 24px; }
        .header-table, .items-table, .meta-table { width: 100%; border-collapse: collapse; }
        .brand { color: {{ $brandColor }}; }
        .title { font-size: 24px; font-weight: 700; text-align: right; }
        .logo { max-width: 120px; max-height: 70px; margin-bottom: 8px; }
        .meta-label { font-weight: 700; width: 35%; background: #f5f7fb; }
        .meta-table td { border: 1px solid #d8e0ea; padding: 7px 10px; }
        .items-table { margin-top: 18px; }
        .items-table th { background: {{ $brandColor }}; color: #fff; border: 1px solid {{ $brandColor }}; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .items-table td { border: 1px solid #d8e0ea; padding: 8px; vertical-align: top; }
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
                <div class="brand" style="font-size: 16px; font-weight: 700;">{{ $companyName }}</div>
            </td>
            <td style="width: 48%; vertical-align: top;">
                <div class="title brand">Job Card</div>
                <table class="meta-table">
                    <tr><td class="meta-label">Job Order #</td><td>{{ $jobOrder->job_order_number }}</td></tr>
                    <tr><td class="meta-label">Customer</td><td>{{ optional($jobOrder->organization)->name ?: '-' }}</td></tr>
                    <tr><td class="meta-label">Issue Date</td><td>{{ optional($jobOrder->issue_date)->format('Y-m-d') ?: '-' }}</td></tr>
                    <tr><td class="meta-label">Required Delivery</td><td>{{ optional($jobOrder->required_delivery_date)->format('Y-m-d') ?: '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 18%;">Item</th>
                <th style="width: 22%;">Section</th>
                <th style="width: 35%;">Process</th>
                <th style="width: 15%;">Requirement Qty</th>
                <th style="width: 10%;">Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jobOrder->jobCards as $jobCard)
                @php
                    $itemCode = optional($jobCard->jobOrderItem)->display_code;
                    $itemLabel = $itemCode && $itemCode !== '-' ? $itemCode : ($jobCard->title ?: 'Job Card');
                @endphp
                @foreach ($jobCard->sections as $section)
                    @forelse ($section->items as $sectionItem)
                        <tr>
                            <td>{{ $itemLabel }}</td>
                            <td>{{ $section->section_name }}</td>
                            <td>{{ $sectionItem->name ?: '-' }}</td>
                            <td>{{ $sectionItem->qty ? $formatStageQty($sectionItem->qty) : '-' }}</td>
                            <td>{{ $sectionItem->unit ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td>{{ $itemLabel }}</td>
                            <td>{{ $section->section_name }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endforelse
                @endforeach
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No job cards available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
