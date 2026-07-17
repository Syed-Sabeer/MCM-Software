<?php

namespace Webkul\Admin\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOtpNotification extends Notification
{
    public function __construct(public string $otp, public $expiresAt) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your MCM password verification code')
            ->view('admin::emails.auth.password-reset-otp', [
                'name'      => $notifiable->name,
                'otp'       => $this->otp,
                'expiresAt' => $this->expiresAt,
            ]);
    }
}
