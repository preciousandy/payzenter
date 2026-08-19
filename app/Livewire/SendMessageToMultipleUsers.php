<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\MessageToUsers;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class SendMessageToMultipleUsers extends Component
{
    use LivewireAlert;

    public $selectedUsers = [];

    // Listeners to receive selected users from the table
    protected $listeners = ['openBulkMessageModal' => 'setSelectedUsers'];

    public $message;
    public $subject;
    public $showModal = false;


    // Set selected users when modal is opened
    public function setSelectedUsers($users)
    {
        $this->selectedUsers = $users;
        $this->showModal = true;
    }

    public function sendMessage()
    {
        $this->validate([
            'message' => 'required',
            'subject' => 'required',
        ]);

        $users = User::whereIn('id', $this->selectedUsers)->get();

        $message = convertBase64ImagesToFiles($this->message);

        Notification::send($users, new MessageToUsers($this->subject, $message));

        return $this->flash('success', 'Message sent successfully to selected users!', [], route('admin.users.index'));
    }

    public function render()
    {
        return view('livewire.send-message-to-multiple-users');
    }
}
