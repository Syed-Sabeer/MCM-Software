@component('admin::emails.layout')
    <p style="margin:0;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Account security</p>
    <h1 style="margin:8px 0 16px;color:#111827;font-size:24px;line-height:32px;">Password reset requested</h1>
    <p style="margin:0 0 16px;color:#475569;font-size:15px;line-height:24px;">Hello {{ $user_name }}, MCM now uses email verification codes for password recovery. Return to the forgot-password page to request a new code.</p>
    <p style="margin:24px 0;"><a href="{{ route('admin.forgot_password.create') }}" style="display:inline-block;padding:11px 18px;border-radius:6px;background:{{ core()->getConfigData('general.settings.menu_color.brand_color') ?: '#b91c1c' }};color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">Request verification code</a></p>
    <p style="margin:0;color:#64748b;font-size:13px;line-height:21px;">If you did not request this, no action is required.</p>
@endcomponent
