@php
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
        core()->getConfigData('general.general.company_info.email') ? 'Email: '.core()->getConfigData('general.general.company_info.email') : null,
    ]);
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
    $formatQuantity = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ','), '0'), '.');
    $documentTitle = 'JOB CARD';
    $documentNumber = $jobOrder->job_order_number;
    $documentMeta = [
        ['label' => 'Customer', 'value' => optional($jobOrder->organization)->name ?: '-'],
        ['label' => 'Issue date', 'value' => $formatDate($jobOrder->issue_date)],
        ['label' => 'Required delivery', 'value' => $formatDate($jobOrder->required_delivery_date)],
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Job Card {{ $documentNumber }}</title>
    <style>@include('admin::documents.pdf.styles')</style>
</head>
<body>
    @include('admin::documents.pdf.header')

    <div class="section-heading">Production processes</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 18%;">Item</th>
                <th style="width: 22%;">Section</th>
                <th style="width: 35%;">Process</th>
                <th style="width: 15%;" class="text-right">Requirement qty</th>
                <th style="width: 10%;">Unit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jobOrder->jobCards as $jobCard)
                @php($itemLabel = $jobCard->display_item_label)
                @foreach($jobCard->sections as $section)
                    @forelse($section->items as $sectionItem)
                        <tr>
                            <td class="item-name">{{ $itemLabel }}</td>
                            <td>{{ $section->section_name }}</td>
                            <td>{{ $sectionItem->name ?: '-' }}</td>
                            <td class="text-right">{{ $sectionItem->qty ? $formatQuantity($sectionItem->qty) : '-' }}</td>
                            <td>{{ $sectionItem->unit ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td class="item-name">{{ $itemLabel }}</td><td>{{ $section->section_name }}</td><td>-</td><td class="text-right">-</td><td>-</td></tr>
                    @endforelse
                @endforeach
            @empty
                <tr><td colspan="5" class="text-center muted">No job cards available.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('admin::documents.pdf.footer')
</body>
</html>
