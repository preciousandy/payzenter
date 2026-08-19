<?php

namespace App\Livewire;

use App\Models\SocialLink;
use Livewire\Component;

class ManageSocialLinks extends Component
{
    public $socialLinks = [];

    public function mount()
    {
        $this->socialLinks = SocialLink::all()->toArray();
    }

    public function update()
    {
        foreach ($this->socialLinks as $socialLink) {
            SocialLink::find($socialLink['id'])->update($socialLink);
        }

        return redirect()->route('admin.website-content')->with('success', 'Social links updated successfully');
    }

    public function render()
    {
        return view('livewire.manage-social-links');
    }
}
