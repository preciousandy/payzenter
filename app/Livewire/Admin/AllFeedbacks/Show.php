<?php

namespace App\Livewire\Admin\AllFeedbacks;

use App\Models\Feedback;
use Livewire\Component;

class Show extends Component
{
    public $feedback;

    public function render()
    {
        return view('livewire.admin.all-feedbacks.show');
    }

    public function markAsRead()
    {
        $this->feedback->update([
            'status' => Feedback::STATUS_SEEN
        ]);

        return redirect()->route('admin.all-feedbacks')->with('success', 'Feedback marked as read');
    }
}
