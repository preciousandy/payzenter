<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UserNotifications extends Component
{
    use LivewireAlert;

    public $perPage = 10;


    public function render()
    {
        return view('livewire.user-notifications', [
            'notifications' => auth()->user()->notifications()->paginate($this->perPage)
        ]);
    }

    public function loadMore()
    {
        $this->perPage += 20;
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        $this->notifications = Auth::user()->notifications;

        $this->alert('success', 'All notifications marked as read');
    }

    public function markAsRead(Notification $notification)
    {
        $link = json_decode($notification->data)->link;
        $notification->update([
            'read_at' => now()
        ]);

        return redirect($link);
    }
}
