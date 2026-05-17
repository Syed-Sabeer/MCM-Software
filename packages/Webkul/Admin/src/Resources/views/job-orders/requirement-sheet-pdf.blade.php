@php
    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#0E90D9';
    $logo = core()->getConfigData('general.general.admin_logo.logo_image');
    $logoPath = $logo && file_exists(public_path('storage/' . $logo)) ? public_path('storage/' . $logo) : null;
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $formatStageQty = fn ($value) => rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    $scope = $scope ?? 'single';
    $vendorRequirementGroups = $vendorRequirementGroups ?? collect([[
        'vendor' => null,
        'requirements' => $jobOrder->requirements ?? collect(),
    ]]);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Requirement Sheet {{ $jobOrder->job_order_number }}</title>
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
                <div class="title brand">Requirement Sheet</div>
                <table class="meta-table">
                    <tr><td class="meta-label">Job Order #</td><td>{{ $jobOrder->job_order_number }}</td></tr>
                    <tr><td class="meta-label">Customer</td><td>{{ optional($jobOrder->organization)->name ?: '-' }}</td></tr>
                    <tr><td class="meta-label">Issue Date</td><td>{{ optional($jobOrder->issue_date)->format('Y-m-d') ?: '-' }}</td></tr>
                    <tr><td class="meta-label">Required Delivery</td><td>{{ optional($jobOrder->required_delivery_date)->format('Y-m-d') ?: '-' }}</td></tr>
                    <tr>
                        <td class="meta-label">Export Scope</td>
                        <td>{{ $scope === 'all' ? 'All Vendors' : 'Single Vendor' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @foreach ($vendorRequirementGroups as $groupIndex => $group)
        @if ($scope === 'all')
            <div style="margin-top: {{ $groupIndex === 0 ? '14px' : '18px' }}; font-weight: 700; color: {{ $brandColor }};">
                Vendor: {{ optional($group['vendor'])->name ?: 'Unassigned Vendor' }}
            </div>
        @endif

        <table class="items-table" style="{{ $scope === 'all' ? 'margin-top:8px;' : '' }}">
            <thead>
                <tr>
                    <th style="width: 14%;">Item</th>
                    <th style="width: 24%;">Material</th>
                    <th style="width: 16%;">Color</th>
                    <th style="width: 12%;">Per Item Required</th>
                    <th style="width: 11%;">Required</th>
                    <th style="width: 9%;">Received</th>
                    <th style="width: 9%;">Balance</th>
                    <th style="width: 11%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($group['requirements'] as $requirement)
                    <tr>
                        <td>{{ $requirement->item_codes ?: '-' }}</td>
                        <td>{{ $requirement->material_name ?: '-' }}</td>
                        <td>{{ $requirement->color_name ?: $requirement->color_code ?: '-' }}</td>
                        <td>{{ $formatStageQty($requirement->qty_per_unit) }} {{ $requirement->unit }}</td>
                        <td>{{ $formatStageQty($requirement->required_qty) }} {{ $requirement->unit }}</td>
                        <td>{{ $formatStageQty($requirement->received_qty) }}</td>
                        <td>{{ $formatStageQty($requirement->balance_qty) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $requirement->status ?: 'pending')) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">No requirements available for this vendor.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 18px; font-weight: 700; color: {{ $brandColor }};">
            Total Requirement from Vendor
        </div>

        <table class="items-table" style="margin-top:8px;">
            <thead>
                <tr>
                    <th style="width: 34%;">Material</th>
                    <th style="width: 18%;">Color</th>
                    <th style="width: 16%;">Total Required</th>
                    <th style="width: 16%;">Received</th>
                    <th style="width: 16%;">Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse (($group['totals'] ?? collect()) as $total)
                    <tr>
                        <td>{{ $total['material_name'] ?: '-' }}</td>
                        <td>{{ $total['color_label'] ?: '-' }}</td>
                        <td>{{ $formatStageQty($total['required_qty']) }} {{ $total['unit'] }}</td>
                        <td>{{ $formatStageQty($total['received_qty']) }} {{ $total['unit'] }}</td>
                        <td>{{ $formatStageQty($total['balance_qty']) }} {{ $total['unit'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No vendor totals available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</div>
</body>
</html>
