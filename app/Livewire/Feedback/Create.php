<?php

namespace App\Livewire\Feedback;

use App\Models\Feedback;
use Livewire\Component;

class Create extends Component
{
    public $subject, $category, $message;

    public function create()
    {

        $this->validate([
            'subject' => 'required|string|min:5|max:255',
            'category' => 'required|string',
            'message' => 'required|string',
        ]);


        Feedback::create([
            'subject' => $this->subject,
            'category' => $this->category,
            'message' => $this->message,
            'status' => Feedback::STATUS_PENDING,
            'user_name' => auth()->user()->name,
            'user_email' => auth()->user()->email,
        ]);

        return redirect()->route('home')->with('success', 'Feedback submitted successfully');
    }


    public function render()
    {
        return view('livewire.feedback.create')->title('Submit Feedback');
    }
}
