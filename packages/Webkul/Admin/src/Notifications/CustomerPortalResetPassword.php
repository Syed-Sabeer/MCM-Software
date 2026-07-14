<?php

namespace Webkul\Admin\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;

class CustomerPortalResetPassword extends ResetPassword
{
    protected function resetUrl($notifiable): string
    {
        return route('customer_portal.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
