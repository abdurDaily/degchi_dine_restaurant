<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberResetPassword extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('frontend.member.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset your Degchi Dine member password')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('You are receiving this email because we received a password reset request for your membership account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in '.config('auth.passwords.members.expire', 60).' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
