@component('admin::emails.layout')
    <div style="margin-bottom: 34px;">
        <p style="font-weight: bold;font-size: 20px;color: #121A26;line-height: 24px;margin-bottom: 24px">
            @lang('admin::app.emails.common.user.dear', ['username' => $user_name]), 👋
        </p>

        <p style="font-size: 16px;color: #384860;line-height: 24px;">
            @lang('admin::app.emails.common.user.create-body')
        </p>

        @if ($plain_password)
            <div style="margin-top: 24px;padding: 20px;border: 1px solid #E5E7EB;border-radius: 12px;background: #F8FAFC;">
                <p style="margin: 0 0 10px;font-size: 14px;color: #526277;">
                    Login Email
                </p>

                <p style="margin: 0 0 16px;font-size: 16px;font-weight: 600;color: #121A26;">
                    {{ $user_email }}
                </p>

                <p style="margin: 0 0 10px;font-size: 14px;color: #526277;">
                    Temporary Password
                </p>

                <p style="margin: 0 0 18px;font-size: 16px;font-weight: 600;color: #121A26;">
                    {{ $plain_password }}
                </p>

                <a
                    href="{{ $login_url }}"
                    style="display: inline-block;padding: 10px 18px;border-radius: 10px;background: #B91C1C;color: #FFFFFF;text-decoration: none;font-weight: 600;"
                >
                    Open Login
                </a>
            </div>
        @endif
    </div>
@endcomponent
