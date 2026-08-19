<?php

namespace App\Livewire;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ChangeUserLevel extends Component
{
    use LivewireAlert;

    public $levels = User::LEVEL_LIMITS;

    public User $user;

    public $current_level;

    public function mount()
    {
        $this->current_level = $this->user->level;
    }

    public function changeBadge()
    {
        $this->user->level = $this->current_level;
        $this->user->save();

        $this->flash('success', 'User Badge has been changed successfully', [], route('admin.users.show', $this->user->hash_id));
    }

    public function render()
    {
        return view('livewire.change-user-level');
    }
}
