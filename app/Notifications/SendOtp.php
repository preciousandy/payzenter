<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SendOtp extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $otp)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Verification OTP - ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('Thank you for registering with ' . config('app.name') . '.')
            ->line('Use the following OTP to verify your account:')
            ->line(new HtmlString('<strong style="display: inline-block; font-size: 24px; letter-spacing: 4px;">' . e($this->otp) . '</strong>'))
            ->line('This OTP will expire in ' . User::OTP_VERIFICATION_TIMEOUT . ' minutes.')
            ->line('If you did not request this, please ignore this email.')
            ->salutation('Best regards,')
            ->line(config('app.name') . ' Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
