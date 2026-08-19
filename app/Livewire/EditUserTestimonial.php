<?php

namespace App\Livewire;

use Livewire\Component;

class EditUserTestimonial extends Component
{
    public $testimonial;

    protected $rules = [
        'testimonial.name' => 'required',
        'testimonial.profession' => 'required',
        'testimonial.message' => 'required',
    ];

    public function update()
    {
        $this->validate();

        $this->testimonial->save();

        return redirect()->route('admin.website-content')->with('success', 'Testimonial updated successfully');
    }


    public function render()
    {
        return view('livewire.edit-user-testimonial');
    }
}
