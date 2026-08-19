<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Services\HashIdService;
use App\Services\UserActivityService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Show extends Component
{
    use LivewireAlert;

    public $user;

    public $transactions = [];

    public $userBalance;

    protected $listeners = ['userUpdated' => 'refreshUser'];

    public function mount($hash_id)
    {
        $this->user = (new HashIdService)->getRecord($hash_id, User::class);

        $this->userBalance = $this->user->wallet_balance;

        $this->transactions = $this->user->transactions()->latest()->get();
    }

    public function refreshUser()
    {
        $this->user = $this->user->fresh();
        $this->userBalance = $this->user->wallet_balance;
    }

    public function updateBalance()
    {
        $this->validate([
            'userBalance' => 'required|numeric'
        ]);

        $this->user->update([
            'wallet_balance' => $this->userBalance
        ]);

        $activityService = new UserActivityService();

        $activityService->logActivity(auth()->user()->name . ' updated ' . $this->user->name . ' balance to ' . $this->userBalance);

        $this->alert('success', 'Balance updated successfully');
    }

    public function render()
    {
        return view('livewire.admin.users.show')->extends('layouts.admin');
    }
}
