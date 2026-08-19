<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $loginData;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, array $loginData)
    {
        $this->user = $user;
        $this->loginData = $loginData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Login to Your Account')
            ->markdown('mails.login-notification');
    }
}
