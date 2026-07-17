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
        return (new MailMessage)
            ->subject('Set up your '.config('customer_portal.name').' access')
            ->view('admin::emails.customer-portal.invitation', [
                'user'                    => $notifiable,
                'url'                     => $this->url,
                'expiresAt'               => $this->expiresAt,
                'temporaryPasswordWasSet' => $this->temporaryPasswordWasSet,
            ]);
    }
}
