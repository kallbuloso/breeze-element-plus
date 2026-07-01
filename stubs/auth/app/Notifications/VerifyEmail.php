<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $expiration = (int) config('auth.verification.expire', 60);

        return (new MailMessage)
            ->subject(__('mail.verification.subject'))
            ->view([
                'html' => 'mail.auth.action',
                'text' => 'mail.auth.action-text',
            ], [
                'appName' => config('app.name'),
                'previewText' => __('mail.verification.preview', ['app' => config('app.name')]),
                'greeting' => __('mail.common.greeting', ['name' => $notifiable->name]),
                'heading' => __('mail.verification.heading'),
                'intro' => __('mail.verification.intro', ['app' => config('app.name')]),
                'actionText' => __('mail.verification.action'),
                'actionUrl' => $this->verificationUrl($notifiable),
                'expiration' => trans_choice('mail.common.expiration', $expiration, ['count' => $expiration]),
                'outro' => __('mail.verification.outro'),
            ]);
    }
}
