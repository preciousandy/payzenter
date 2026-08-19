<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\InviteUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class AddUser extends Component
{
    use LivewireAlert;

    public $first_name, $last_name, $email, $password;
    public $user_roles = [];

    public $roles;

    public function mount()
    {
        $this->roles = Role::all()->pluck('name');
    }

    public function addUser()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'user_roles' => 'required|array|min:1'
        ]);

        DB::beginTransaction();
        $user =    User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'wallet_balance' => 0
        ]);

        $user->syncRoles($this->user_roles);

        DB::commit();

        if ($user) {
            $user->notify(new InviteUser($this->password));
        }

        $this->flash('success', 'User added successfully', [], route('admin.users.index'));
    }

    public function render()
    {
        return view('livewire.add-user');
    }
}
