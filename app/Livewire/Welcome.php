<?php

namespace App\Livewire;

use App\Models\Faq;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\UserTestimonial;
use App\Models\WelcomePageContent;
use Livewire\Component;

class Welcome extends Component
{

    public $testimonials;


    public $pageContent;

    public $faqs = [];

    public $category = 'enquiry';

    public $user_name, $user_email, $subject, $message;


    public function mount()
    {

        $this->pageContent = WelcomePageContent::first() ?? new WelcomePageContent();
        $appName = config('app.name');
        foreach ($this->pageContent->getAttributes() as $key => $value) {
            if (is_string($value) && $value !== '') {
                $this->pageContent->{$key} = str_replace(['SwiftPay', 'FitPay', 'fitpay'], $appName, $value);
            }
        }

        $this->testimonials = UserTestimonial::latest()->take(10)->get();

        $this->faqs = Faq::all();
    }

    public function sendMessage()
    {
        $data =  $this->validate([
            'user_name' => 'required',
            'user_email' => 'required|email',
            'subject' => 'required',
            'category' => 'required',
            'message' => 'required'
        ]);

        $data['status'] = Feedback::STATUS_PENDING;

        Feedback::create($data);

        return redirect()->route('welcome')->with('success', 'Message sent successfully');
    }

    public function render()
    {
        return view('livewire.welcome', [])->extends('layouts.guest')->title('Welcome');
    }
}
