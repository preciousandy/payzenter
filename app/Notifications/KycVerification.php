<?php

namespace App\Notifications;

use App\Models\Kyc;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycVerification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $kyc, public $reason = '')
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
        if ($this->kyc->user->kyc_status == Kyc::STATUS_VERIFIED) {
            return (new MailMessage)
                ->subject('KYC Verification')
                ->greeting('Hello ' . $notifiable->first_name)
                ->line('This is to inform you that your KYC verification was successful and you now have full access to use ' . config('app.name'))
                ->action('Visit ' . config('app.name'), config('app.url'))
                ->line('Have a great day ahead!');
        }


        if ($this->kyc->user->kyc_status == Kyc::STATUS_REJECTED) {
            return (new MailMessage)
                ->subject('KYC Verification')
                ->greeting('Hello ' . $notifiable->first_name)
                ->line('Sorry to inform you that your KYC verification was rejected due to the reason below:')
                ->line($this->reason)
                ->action('Retry KYC', config('app.url'))
                ->line('Have a great day ahead!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->kyc->user->kyc_status == Kyc::STATUS_VERIFIED) {
            return [
                'message' => 'This is to inform you that your KYC verification was successful and you now have full access to use ' . config('app.name'),
            ];
        }

        if ($this->kyc->user->kyc_status == Kyc::STATUS_REJECTED) {
            return [
                'message' => 'Sorry to inform you that your KYC verification was rejected due to the reason below:',
            ];
        }
    }
}
