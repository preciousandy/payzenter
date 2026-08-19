<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditProfile extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public User $user;
    public $name;
    public $email;
    public $photo;


    public $old_password;
    public $password;
    public $password_confirmation;

    public $tempCoverImage;



    public function mount()
    {
        $this->user = auth()->user();

        $this->name = $this->user->name;
        $this->email = $this->user->email;


        $this->tempCoverImage = $this->user->profile_image ? $this->user->profile_photo : null;
    }

    public function updatedPhoto()
    {
        if ($this->photo) {
            $this->tempCoverImage = '/storage/' . $this->splitLivewireTmpPath($this->photo->getRealPath());
        }
    }

    function splitLivewireTmpPath($path)
    {
        // Find the position of "/livewire-tmp"
        $position = strpos($path, '/livewire-tmp');

        if ($position === false) {
            return ['path' => $path, 'livewire_tmp' => null];
        }

        // Split the string
        $basePath = substr($path, 0, $position);
        $livewireTmpPath = substr($path, $position);

        return $livewireTmpPath;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($this->photo) {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
                'profile_image' => $this->photo->store('photos', 'public'),
            ]);
        } else {
            $this->user->update([
                'name' => $this->name,
                'email' => $this->email,
            ]);
        }

        $this->flash('success', 'Profile updated successfully!', [], route('admin.edit-profile'));
    }

    public function changePassword()
    {
        $this->validate([
            'old_password' => 'required|min:8|current_password',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $this->user->update([
            'password' => Hash::make($this->password),
        ]);

        auth()->logout();

        $this->flash('success', 'Password updated successfully!', [], route('admin.login'));
    }


    public function render()
    {
        return view('livewire.admin.edit-profile')->layout('layouts.admin');
    }
}
