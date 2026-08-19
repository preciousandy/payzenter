<?php

namespace App\Livewire\Admin;

use App\Models\WebsiteContent;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditWebsiteContent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $content;

    public $logo, $favicon, $description, $contact_phone, $contact_email, $contact_address;
    public $app_store_link, $play_store_link;

    public $logo_path, $favicon_path;

    public function mount()
    {
        $this->content = WebsiteContent::first();

        if ($this->content) {
            $this->description = $this->content->description;
            $this->contact_phone = $this->content->contact_phone;
            $this->contact_email = $this->content->contact_email;
            $this->contact_address = $this->content->contact_address;
            $this->app_store_link = $this->content->app_store_link;
            $this->play_store_link = $this->content->play_store_link;

            $this->logo_path = $this->content->logo ? asset('storage/' . $this->content->logo) : null;
            $this->favicon_path = $this->content->favicon ? asset('storage/' . $this->content->favicon) : null;
        }
    }

    public function saveChanges()
    {
        $this->validate([
            'logo' => 'nullable|file',
            'favicon' => 'nullable|file',
            'description' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|string',
            'contact_address' => 'nullable|string',
            'app_store_link' => 'nullable|string',
            'play_store_link' => 'nullable|string',

        ]);

        if ($this->content) {
            $this->content->update([
                'logo' => $this->logo ? $this->logo->store('website content', 'public') : $this->content->logo,
                'favicon' => $this->favicon ? $this->favicon->store('website content', 'public') : $this->content->favicon,
                'description' => $this->description,
                'contact_phone' => $this->contact_phone,
                'contact_email' => $this->contact_email,
                'contact_address' => $this->contact_address,
                'app_store_link' => $this->app_store_link,
                'play_store_link' => $this->play_store_link,
            ]);
        } else {
            WebsiteContent::create([
                'logo' => $this->logo ? $this->logo->store('website content', 'public') : null,
                'favicon' => $this->favicon ? $this->favicon->store('website content', 'public') : null,
                'description' => $this->description,
                'contact_phone' => $this->contact_phone,
                'contact_email' => $this->contact_email,
                'contact_address' => $this->contact_address,
                'app_store_link' => $this->app_store_link,
                'play_store_link' => $this->play_store_link,
            ]);
        }

        return $this->flash('success', 'Website content updated successfully!', [], route('admin.website-content'));
    }

    public function render()
    {
        return view('livewire.admin.edit-website-content');
    }
}
