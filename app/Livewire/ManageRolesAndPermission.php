<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageRolesAndPermission extends Component
{
    public $role_name;

    public $roles = [];

    public $permissions = [];

    public $selectedRole;

    public $selectedPermissions = [];

    public $roleId;

    public function mount()
    {
        $this->roles = Role::where('name', '!=', 'admin')->orderBy('name')->get();


        $this->selectedRole = $this->roles->first();

        $this->permissions = Permission::orderBy('name')->get();

        $this->selectedPermissions = $this->selectedRole->permissions->pluck('name')->toArray();
    }

    public function updatedRoleId($roleId)
    {

        $this->selectedRole = Role::find($roleId);

        $this->selectedPermissions = $this->selectedRole->permissions->pluck('name')->toArray();
    }

    public function updatePermissions()
    {


        $this->selectedRole->syncPermissions($this->selectedPermissions);

        return redirect()->route('admin.manage-roles-and-permissions')->with('success', 'Permissions updated successfully');
    }

    public function deleteRole($roleId)
    {

        $role = Role::find($roleId);

        $role->syncPermissions([]);

        $role->delete();

        return redirect()->route('admin.manage-roles-and-permissions')->with('success', 'Role deleted successfully');
    }

    public function render()
    {
        return view('livewire.manage-roles-and-permission')->extends('layouts.admin');
    }

    public function addRole()
    {
        $this->validate([
            'role_name' => 'required'
        ]);

        Role::create(['name' => $this->role_name]);

        return redirect()->route('admin.manage-roles-and-permissions')->with('success', 'Role created successfully');
    }
}
