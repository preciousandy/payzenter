<?php

namespace App\Livewire;

use App\Notifications\MessageToUsers;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SendMessageToSingleUser extends Component
{
    use LivewireAlert;

    public $subject;
    public $message;
    public $user;


    public function sendMessage()
    {
        $this->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required',
        ]);

        $this->user->notify(new MessageToUsers($this->subject, $this->message));

        $this->flash('success', 'Message sent successfully to user', [], route('admin.users.show', $this->user->hash_id));
    }


    public function render()
    {
        return view('livewire.send-message-to-single-user');
    }
}
