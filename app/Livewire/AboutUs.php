<?php

namespace App\Livewire;

use App\Models\Page;
use Livewire\Component;

class AboutUs extends Component
{
    public $content;

    public function mount()
    {
        $page = Page::where('code', Page::ABOUT_US)->first();

        if ($page) {
            $this->content = $page->content;
        }
    }
    public function render()
    {
        return view('livewire.about-us')->extends('layouts.guest')->title('About Us');
    }
}
