<?php

namespace App\Livewire;

use Livewire\Component;

class EditFaqQuestion extends Component
{
    public $faq;


    protected $rules = [
        'faq.question' => 'required',
        'faq.answer' => 'required'
    ];

    public function update()
    {
        $this->validate();

        $this->faq->save();

        return redirect()->route('admin.website-content')->with('success', 'FAQ updated successfully');
    }


    public function render()
    {
        return view('livewire.edit-faq-question');
    }
}
