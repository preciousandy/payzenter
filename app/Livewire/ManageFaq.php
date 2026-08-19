<?php

namespace App\Livewire;

use App\Models\Faq;
use Livewire\Component;

class ManageFaq extends Component
{
    public $question;
    public $answer;

    public $faqs = [];

    public function mount()
    {
        $this->faqs = Faq::latest()->get();
    }

    public function addQuestion()
    {
        $this->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

        Faq::create([
            'question' => $this->question,
            'answer' => $this->answer
        ]);

        return redirect()->route('admin.website-content')->with('success', 'FAQ added successfully');
    }

    // delete faq
    public function deleteFaq($id)
    {
        $faq = Faq::find($id);
        $faq->delete();

        return redirect()->route('admin.website-content')->with('success', 'FAQ deleted successfully');
    }


    public function render()
    {
        return view('livewire.manage-faq');
    }
}
