<?php

namespace Webkul\Admin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerPortalInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $url, public $expiresAt, public bool $temporaryPasswordWasSet = false)
    {
        $this->afterCommit();
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Set up your '.config('customer_portal.name').' access')
            ->greeting('Hello '.$notifiable->name.',')
            ->line($this->temporaryPasswordWasSet
                ? 'An administrator created your portal account with a temporary password.'
                : 'You have been invited to access your company account.')
            ->line('Login email: '.$notifiable->email);

        if ($this->temporaryPasswordWasSet) {
            $message->line('For security, the temporary password is not included in this email. Use the secure link below to replace it before accessing the portal.');
        }

        return $message
            ->action($this->temporaryPasswordWasSet ? 'Secure your account' : 'Set password', $this->url)
            ->line('This secure link expires '.$this->expiresAt->toDayDateTimeString().'.')
            ->line('Need help? Contact '.config('customer_portal.support_email').'.');
    }
}
