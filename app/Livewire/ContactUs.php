<?php

namespace App\Livewire;

use App\Models\Feedback;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ContactUs extends Component
{
    use LivewireAlert;

    public $name, $email, $phone, $subject, $message;


    public function submit()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        Feedback::create([
            'subject' => $this->subject,
            'phone' => $this->phone,
            'message' => $this->message,
            'status' => Feedback::STATUS_PENDING,
            'user_name' => $this->name,
            'user_email' => $this->email,
        ]);

        // Reset form
        $this->reset();
        $this->alert('success', 'Thank you for contacting us! We will get back to you soon.');
    }

    public function render()
    {
        return view('livewire.contact-us')->extends('layouts.guest')->title('Contact Us');
    }
}
