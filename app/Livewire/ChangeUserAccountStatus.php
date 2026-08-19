<?php

namespace App\Livewire;

use App\Mail\AccountBlockedMail;
use App\Mail\AccountReactivatedMail;
use App\Mail\AccountSuspendedMail;
use App\Models\User;
use App\Services\UserActivityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ChangeUserAccountStatus extends Component
{
    use LivewireAlert;

    public User $user;

    public $account_statuses = [];

    public $status;
    public $suspensionDuration = null;
    public $suspensionReason = '';
    public $blockReason = '';

    public function mount()
    {
        $this->account_statuses = [
            User::ACCOUNT_STATUS_ACTIVE => [
                'name' => 'Active',
                'icon' => 'fa fa-check-circle',
                'color' => 'green'
            ],
            User::ACCOUNT_STATUS_SUSPENDED => [
                'name' => 'Suspended',
                'icon' => 'fa fa-ban',
                'color' => 'yellow'
            ],
            User::ACCOUNT_STATUS_BLOCKED => [
                'name' => 'Blocked',
                'icon' => 'fa fa-lock',
                'color' => 'red'
            ]
        ];

        $this->status = $this->user->account_status;
    }

    public function changeStatus()
    {
        // Validation rules
        $rules = [];

        if ($this->status === User::ACCOUNT_STATUS_SUSPENDED) {
            $rules['suspensionDuration'] = 'required|integer|min:1|max:365';
            $rules['suspensionReason'] = 'required|string|max:500';
        }

        if ($this->status === User::ACCOUNT_STATUS_BLOCKED) {
            $rules['blockReason'] = 'required|string|max:500';
        }

        if ($this->status !== User::ACCOUNT_STATUS_ACTIVE) {
            $this->validate($rules);
        }

        $previousStatus = $this->user->account_status;

        // Update user status
        $updateData = ['account_status' => $this->status];

        if ($this->status === User::ACCOUNT_STATUS_SUSPENDED) {
            $suspendedUntil = Carbon::now()->addDays($this->suspensionDuration);
            $updateData['suspension_duration_days'] = $this->suspensionDuration;
            $updateData['suspended_until'] = $suspendedUntil;
            $updateData['suspension_reason'] = $this->suspensionReason;
        } elseif ($this->status === User::ACCOUNT_STATUS_ACTIVE) {
            // Clear suspension data when activating
            $updateData['suspension_duration_days'] = null;
            $updateData['suspended_until'] = null;
            $updateData['suspension_reason'] = null;
        }

        $this->user->update($updateData);

        // Send appropriate emails
        try {
            if ($this->status === User::ACCOUNT_STATUS_SUSPENDED && $previousStatus !== User::ACCOUNT_STATUS_SUSPENDED) {
                Mail::to($this->user->email)->send(
                    new AccountSuspendedMail(
                        $this->user,
                        $this->suspensionReason,
                        $this->suspensionDuration,
                        $this->user->suspended_until
                    )
                );
            } elseif ($this->status === User::ACCOUNT_STATUS_BLOCKED && $previousStatus !== User::ACCOUNT_STATUS_BLOCKED) {
                Mail::to($this->user->email)->send(new AccountBlockedMail($this->user, $this->blockReason));
            } elseif (
                $this->status === User::ACCOUNT_STATUS_ACTIVE &&
                ($previousStatus === User::ACCOUNT_STATUS_SUSPENDED || $previousStatus === User::ACCOUNT_STATUS_BLOCKED)
            ) {
                Mail::to($this->user->email)->send(new AccountReactivatedMail($this->user));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the status change
            logger('Failed to send account status email: ' . $e->getMessage());
        }

        // Record activity
        $activityService = new UserActivityService();
        $activityMessage = auth()->user()->name . ' changed account status for ' . $this->user->name . ' to ' . $this->status;



        $activityService->logActivity($activityMessage);

        $this->flash('success', 'User account status has been changed successfully', [], route('admin.users.show', $this->user->hash_id));
    }

    public function render()
    {
        return view('livewire.change-user-account-status');
    }
}
