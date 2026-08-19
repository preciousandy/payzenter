<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Users extends Component
{
    public $rolesFilter = [];

    public function mount()
    {
        $roles = Role::all();

        $this->rolesFilter = ['' => 'All'];

        foreach ($roles as $role) {
            $this->rolesFilter[$role->name] = $role->name;
        }
    }


    public function render()
    {
        return view('livewire.admin.users', [
            'users' => User::orderBy('first_name')->paginate(20),
        ])->extends('layouts.admin');
    }
}
