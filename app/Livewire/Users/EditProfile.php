<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditProfile extends Component
{
    use LivewireAlert;

    public $user;

    public $first_name;
    public $last_name;
    public $email;
    public $phone;

    public function mount()
    {
        $this->user = auth()->user();

        $this->fill([
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ]);
    }

    public function save()
    {
        $data =  $this->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
            'phone' => 'nullable|numeric|digits_between:10,15|unique:users,phone,' . $this->user->id,
        ]);


        $this->user->update($data);

        return $this->flash('success', 'Profile updated successfully!', [], route('profile'));
    }

    public function render()
    {
        return view('livewire.users.edit-profile')->title('Edit Profile');
    }
}
