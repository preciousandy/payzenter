<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditUserInfo extends Component
{
  use LivewireAlert;

  public User $user;

  public $first_name;
  public $last_name;
  public $email;
  public $phone;

  public function mount()
  {
    $this->fill([
      'first_name' => $this->user->first_name,
      'last_name' => $this->user->last_name,
      'email' => $this->user->email,
      'phone' => $this->user->phone,
    ]);
  }

  public function updateUserInfo()
  {
    $data = $this->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user->id)],
      'phone' => 'required|numeric|digits_between:10,15|unique:users,phone,' . $this->user->id,
    ]);

    $this->user->update($data);

    $this->alert('success', 'User information updated successfully');

    // Refresh the parent component to show updated data
    $this->dispatch('userUpdated');

    // Close modal by dispatching browser event
    $this->js('$dispatch("close-modal")');
  }

  public function render()
  {
    return view('livewire.edit-user-info');
  }
}
