<?php

namespace App\Livewire\Admin;

use App\Jobs\SendExpoPushNotification;
use App\Models\User;
use App\Models\UserNotificationToken;
use App\Notifications\NotifyMultipleUsers;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class SendNotificationsToUsers extends Component
{
    use LivewireAlert;

    public $selectedUsers = [];

    // Listeners to receive selected users from the table
    protected $listeners = ['openBulkNotificationsModal' => 'setSelectedUsers'];

    public $message;
    public $subject;
    public $showModal = false;
    public $channel = 'Both';

    public $channels = ['Both', 'Email', 'Push-Notification'];


    // Set selected users when modal is opened
    public function setSelectedUsers($users)
    {
        $this->selectedUsers = $users;
        $this->showModal = true;
    }

    public function sendMessage()
    {
        $data =  $this->validate([
            'message' => 'required',
            'subject' => 'required',
            'channel' => 'required|in:Both,Email,Push-Notification',
        ]);


        $users = User::whereIn('id', $this->selectedUsers)->get();

        if ($this->channel == 'Push-Notification' || $this->channel == 'Both') {
            $usersTokens = UserNotificationToken::whereIn('user_id', $this->selectedUsers)->get()->pluck('token')->toArray();


            SendExpoPushNotification::dispatch(
                $usersTokens,
                $this->subject,
                $this->message,
                []
            );
        }

        if ($this->channel == 'Email' || $this->channel == 'Both') {
            // Send email notification to users
            Notification::send($users, new NotifyMultipleUsers($this->subject, $this->message));
        }

        return $this->flash('success', 'Message sent successfully to selected users!', [], route('admin.users.index'));
    }


    public function render()
    {
        return view('livewire.admin.send-notifications-to-users');
    }
}
