<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteUser extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $password)
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
            ->subject('Invite to join the ' . config('app.name') . ' platform')
            ->greeting('Hello! ' . $notifiable->first_name)
            ->line('You have been invited to join the ' . config('app.name') . ' platform')
            ->line("Here is your login details:")
            ->line("Email: " . $notifiable->email)
            ->line("Password: " . $this->password)
            ->line('Use the button below to login to your account')
            ->action('Login', route('admin.login'))
            ->line('Have a great day!');
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
