<?php

namespace App\Livewire;

use App\Models\UserTestimonial;
use Livewire\Component;

class ManageUserTestimonials extends Component
{

    public $testimonials;
    public $name;
    public $profession;
    public $message;

    public function mount()
    {
        $this->testimonials = UserTestimonial::latest()->get();
    }

    public function render()
    {
        return view('livewire.manage-user-testimonials');
    }

    public function addTestimonial()
    {
        $this->validate([
            'name' => 'required',
            'message' => 'required',
            'profession' => 'required',
        ]);

        UserTestimonial::create([
            'name' => $this->name,
            'profession' => $this->profession,
            'message' => $this->message,
        ]);

        return redirect()->route('admin.website-content')->with('success', 'Testimonial added successfully');
    }

    public function deleteTestimonial($id)
    {

        UserTestimonial::find($id)->delete();


        return redirect()->route('admin.website-content')->with('success', 'Testimonial deleted successfully');
    }
}
