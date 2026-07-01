<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends BaseResetPassword
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $expiration = (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject(__('mail.password_reset.subject'))
            ->view([
                'html' => 'mail.auth.action',
                'text' => 'mail.auth.action-text',
            ], [
                'appName' => config('app.name'),
                'previewText' => __('mail.password_reset.preview', ['app' => config('app.name')]),
                'greeting' => __('mail.common.greeting', ['name' => $notifiable->name]),
                'heading' => __('mail.password_reset.heading'),
                'intro' => __('mail.password_reset.intro', ['app' => config('app.name')]),
                'actionText' => __('mail.password_reset.action'),
                'actionUrl' => $this->resetUrl($notifiable),
                'expiration' => trans_choice('mail.common.expiration', $expiration, ['count' => $expiration]),
                'outro' => __('mail.password_reset.outro'),
            ]);
    }
}
