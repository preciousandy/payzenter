<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UsersMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $the_subject, public string $the_message)
    {
        //
    }


    public function build()
    {
        $subject = $this->the_subject;
        $htmlMessage = $this->the_message;
        $html = '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #111827;">'
            . '<div style="max-width: 640px; margin: 0 auto; padding: 24px;">'
            . '<div style="font-size: 18px; font-weight: 700; margin-bottom: 12px;">' . e(config('app.name')) . '</div>'
            . '<div style="border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px;">'
            . $htmlMessage
            . '</div>'
            . '<div style="margin-top: 18px; font-size: 12px; color: #6b7280;">'
            . '© ' . now()->year . ' ' . e(config('app.name')) . '. All rights reserved.'
            . '</div>'
            . '</div>'
            . '</div>';

        return $this->subject($subject)
            ->html($html);
    }
}
