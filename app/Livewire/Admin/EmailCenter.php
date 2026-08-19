<?php

namespace App\Livewire\Admin;

use App\Mail\UsersMessageMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EmailCenter extends Component
{
    use LivewireAlert;

    public $mode = 'single';

    public $to_email;
    public $audience = 'merchants';

    public $subject;
    public $message;

    public function sendSingle()
    {
        $validated = $this->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:150',
            'message' => 'required|string',
        ]);

        Mail::to($validated['to_email'])->send(new UsersMessageMail($validated['subject'], $validated['message']));

        $this->alert('success', 'Email sent');
    }

    public function sendBulk()
    {
        $validated = $this->validate([
            'audience' => 'required|in:merchants,all',
            'subject' => 'required|string|max:150',
            'message' => 'required|string',
        ]);

        $query = User::query()->whereNotNull('email');
        if ($validated['audience'] === 'merchants') {
            $query->whereNotNull('merchant_id');
        }

        $sent = 0;
        $query->orderBy('id')->chunkById(100, function ($users) use (&$sent, $validated) {
            foreach ($users as $user) {
                Mail::to($user->email)->send(new UsersMessageMail($validated['subject'], $validated['message']));
                $sent++;
            }
        });

        $this->alert('success', "Bulk email sent ({$sent} recipients)");
    }

    public function render()
    {
        return view('livewire.admin.email-center')->title('Email Center');
    }
}

