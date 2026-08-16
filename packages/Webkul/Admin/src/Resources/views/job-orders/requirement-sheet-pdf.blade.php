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
    $requirementGroup = $requirementGroup ?? ['requirements' => $jobOrder->requirements ?? collect(), 'totals' => collect()];
    $documentTitle = 'REQUIREMENT SHEET';
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
    <title>Requirement Sheet {{ $documentNumber }}</title>
    <style>@include('admin::documents.pdf.styles')</style>
</head>
<body>
    @include('admin::documents.pdf.header')

    <div class="section-heading">Material requirements</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 14%;">Item</th>
                <th style="width: 27%;">Material</th>
                <th style="width: 16%;">Color</th>
                <th style="width: 13%;" class="text-right">Per item</th>
                <th style="width: 11%;" class="text-right">Required</th>
                <th style="width: 10%;" class="text-right">From stock</th>
                <th style="width: 8%;" class="text-right">Received</th>
                <th style="width: 8%;" class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requirementGroup['requirements'] as $requirement)
                <tr>
                    <td>{{ $requirement->item_codes ?: '-' }}</td>
                    <td class="item-name">{{ $requirement->material_name ?: '-' }}</td>
                    <td>{{ $requirement->color_name ?: $requirement->color_code ?: '-' }}</td>
                    <td class="text-right">{{ $formatQuantity($requirement->qty_per_unit) }} {{ $requirement->unit }}</td>
                    <td class="text-right">{{ $formatQuantity($requirement->required_qty) }} {{ $requirement->unit }}</td>
                    <td class="text-right">{{ $formatQuantity($requirement->inventory_allocated_qty) }} {{ $requirement->unit }}</td>
                    <td class="text-right">{{ $formatQuantity($requirement->received_qty) }} {{ $requirement->unit }}</td>
                    <td class="text-right">{{ $formatQuantity($requirement->balance_qty) }} {{ $requirement->unit }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center muted">No requirements available.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-heading">Total requirement from vendor</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 34%;">Material</th>
                <th style="width: 18%;">Color</th>
                <th style="width: 16%;" class="text-right">Total required</th>
                <th style="width: 16%;" class="text-right">Received</th>
                <th style="width: 16%;" class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($requirementGroup['totals'] ?? collect()) as $total)
                <tr>
                    <td class="item-name">{{ $total['material_name'] ?: '-' }}</td>
                    <td>{{ $total['color_label'] ?: '-' }}</td>
                    <td class="text-right">{{ $formatQuantity($total['required_qty']) }} {{ $total['unit'] }}</td>
                    <td class="text-right">{{ $formatQuantity($total['received_qty']) }} {{ $total['unit'] }}</td>
                    <td class="text-right">{{ $formatQuantity($total['balance_qty']) }} {{ $total['unit'] }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center muted">No vendor totals available.</td></tr>
            @endforelse
        </tbody>
    </table>

    @include('admin::documents.pdf.footer')
</body>
</html>
