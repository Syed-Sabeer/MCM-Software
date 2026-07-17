@component('admin::emails.layout')
    <p style="margin:0;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Account security</p>
    <h1 style="margin:8px 0 16px;color:#111827;font-size:24px;line-height:32px;">Verify your password reset</h1>
    <p style="margin:0 0 18px;color:#475569;font-size:15px;line-height:24px;">Hello {{ $name }}, use the verification code below to continue resetting your MCM password.</p>

    <div style="margin:24px 0;padding:22px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;text-align:center;">
        <p style="margin:0 0 8px;color:#64748b;font-size:12px;font-weight:700;text-transform:uppercase;">Verification code</p>
        <p style="margin:0;color:#111827;font-family:monospace;font-size:34px;font-weight:700;letter-spacing:8px;">{{ $otp }}</p>
    </div>

    <p style="margin:0 0 10px;color:#475569;font-size:14px;line-height:22px;">This code expires in {{ \Webkul\Admin\Services\Auth\PasswordResetOtpService::EXPIRY_MINUTES }} minutes and can be used once.</p>
    <p style="margin:0;color:#64748b;font-size:13px;line-height:21px;">If you did not request a password reset, no action is required. Never share this code with anyone.</p>
@endcomponent
