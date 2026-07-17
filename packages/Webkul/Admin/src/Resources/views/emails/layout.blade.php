@php
    $brandColor = core()->getConfigData('general.settings.menu_color.brand_color') ?: '#b91c1c';
    $logoUrl = asset('logo/mcmmain-pdf.png');
    $companyName = core()->getConfigData('general.general.company_info.company_name') ?: config('app.name');
    $supportEmail = core()->getConfigData('general.general.company_info.email');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $companyName }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:28px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px;border:1px solid #e2e8f0;border-radius:8px;background:#ffffff;overflow:hidden;">
                <tr><td style="height:4px;background:{{ $brandColor }};"></td></tr>
                <tr><td style="padding:24px 30px;border-bottom:1px solid #e2e8f0;">
                    <a href="{{ config('app.url') }}" style="display:inline-block;text-decoration:none;"><img src="{{ $logoUrl }}" width="220" alt="{{ $companyName }}" style="display:block;width:220px;max-width:100%;height:auto;border:0;"></a>
                </td></tr>
                <tr><td style="padding:30px;">{{ $slot }}</td></tr>
                <tr><td style="padding:20px 30px;border-top:1px solid #e2e8f0;background:#f8fafc;">
                    <p style="margin:0;color:#64748b;font-size:12px;line-height:20px;">This is an automated message from {{ $companyName }}.@if($supportEmail) Need help? Contact <a href="mailto:{{ $supportEmail }}" style="color:{{ $brandColor }};text-decoration:none;">{{ $supportEmail }}</a>.@endif</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
