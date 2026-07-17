@component('admin::emails.layout')
    <p style="margin:0;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Customer portal</p>
    <h1 style="margin:8px 0 16px;color:#111827;font-size:24px;line-height:32px;">Set up your portal access</h1>
    <p style="margin:0 0 14px;color:#475569;font-size:15px;line-height:24px;">Hello {{ $user->name }}, {{ $temporaryPasswordWasSet ? 'an administrator created your portal account with a temporary password.' : 'you have been invited to access your company account.' }}</p>
    <div style="margin:20px 0;padding:16px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;"><p style="margin:0 0 6px;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Login email</p><p style="margin:0;color:#111827;font-size:15px;font-weight:600;">{{ $user->email }}</p></div>
    @if($temporaryPasswordWasSet)<p style="margin:0 0 16px;color:#475569;font-size:14px;line-height:22px;">For security, the temporary password is not included here. Use the secure setup button to replace it.</p>@endif
    <p style="margin:24px 0;"><a href="{{ $url }}" style="display:inline-block;padding:11px 18px;border-radius:6px;background:{{ core()->getConfigData('general.settings.menu_color.brand_color') ?: '#b91c1c' }};color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">{{ $temporaryPasswordWasSet ? 'Secure account' : 'Set password' }}</a></p>
    <p style="margin:0;color:#64748b;font-size:13px;line-height:21px;">This secure setup link expires {{ $expiresAt->toDayDateTimeString() }}.</p>
@endcomponent
