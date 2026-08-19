<?php

namespace App\Livewire;

use App\Models\SocialLink;
use App\Models\WelcomePageContent;
use Livewire\Component;

class Footer extends Component
{
    public $footerContent;

    public $socialLinks = [];

    public function mount()
    {
        $this->footerContent = WelcomePageContent::first();

        $this->socialLinks = SocialLink::all();
    }
    public function render()
    {
        return view('livewire.footer');
    }
}
