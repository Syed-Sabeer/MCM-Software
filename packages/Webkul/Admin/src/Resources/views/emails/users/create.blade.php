@component('admin::emails.layout')
    <p style="margin:0;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Account access</p>
    <h1 style="margin:8px 0 16px;color:#111827;font-size:24px;line-height:32px;">Welcome to MCM</h1>
    <p style="margin:0 0 18px;color:#475569;font-size:15px;line-height:24px;">Hello {{ $user_name }}, your MCM account is ready.</p>

    @if ($plain_password)
        <div style="margin:22px 0;padding:18px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;">
            <p style="margin:0 0 6px;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Login email</p>
            <p style="margin:0 0 16px;color:#111827;font-size:15px;font-weight:600;">{{ $user_email }}</p>
            <p style="margin:0 0 6px;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Temporary password</p>
            <p style="margin:0;color:#111827;font-family:monospace;font-size:16px;font-weight:700;">{{ $plain_password }}</p>
        </div>
        <p style="margin:24px 0 0;"><a href="{{ $login_url }}" style="display:inline-block;padding:11px 18px;border-radius:6px;background:{{ core()->getConfigData('general.settings.menu_color.brand_color') ?: '#b91c1c' }};color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">Open MCM login</a></p>
    @endif
@endcomponent
