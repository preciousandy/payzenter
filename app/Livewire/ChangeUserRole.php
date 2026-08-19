<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class ChangeUserRole extends Component
{
    use LivewireAlert;
    public $user;

    public $user_roles = [];

    public $roles;

    public function mount()
    {
        $this->roles = Role::all()->pluck('name');
        $this->user_roles = $this->user->roles->pluck('name')->toArray();
    }

    public function updateRoles()
    {

        $this->validate([
            'user_roles' => 'required|array|min:1',
        ]);

        $this->user->syncRoles($this->user_roles);

        $this->flash('success', 'User roles updated successfully!', [], route('admin.users.show', $this->user->hash_id));
    }

    public function render()
    {
        return view('livewire.change-user-role');
    }
}
