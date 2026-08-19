<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditPrivacyPolicy extends Component
{
    use LivewireAlert;


    public $page;

    public $content = '';

    public function mount()
    {
        $this->page = Page::where('code', Page::PRIVACY_POLICY)->first();

        if ($this->page) {
            $this->content = $this->page->content;
        }
    }

    public function submit()
    {
        $this->validate([
            'content' => 'required',
        ]);

        if ($this->page) {
            $this->page->update([
                'content' => $this->content,
            ]);
        } else {
            Page::create([
                'code' => Page::PRIVACY_POLICY,
                'content' => $this->content,
            ]);
        }

        $this->alert('success', 'Privacy Page updated successfully');
    }


    public function render()
    {
        return view('livewire.admin.edit-privacy-policy');
    }
}
