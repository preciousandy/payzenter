<?php

namespace App\Livewire\Admin;

use App\Models\Kyc;
use App\Models\MerchantKyb;
use App\Models\User;
use App\Models\UserVirtualAccount;
use App\Notifications\KycVerification;
use App\Services\UserActivityService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class VerifyKycModal extends Component
{
    use LivewireAlert;

    public $kyc;
    public $merchantKyb;
    public $user;
    public $reason;

    public function mount($user = null, $kyc = null)
    {
        if ($user) {
            $this->user = $user instanceof User ? $user : User::with(['kyc', 'merchantKyb'])->find($user);
        } elseif ($kyc) {
            if ($kyc instanceof Kyc) {
                $this->kyc = $kyc;
                $this->user = $kyc->user;
            } elseif ($kyc instanceof User) {
                $this->user = $kyc;
            } else {
                $this->kyc = Kyc::find($kyc);
                $this->user = $this->kyc?->user;
            }
        }

        if ($this->user) {
            $this->merchantKyb = $this->user->merchantKyb;
            if (!$this->kyc) {
                $this->kyc = $this->user->kyc;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.verify-kyc-modal');
    }

    public function approve()
    {
        $user = $this->user ?? $this->kyc?->user;

        if (!$user) {
            return $this->alert('error', 'User record not found for this KYC.');
        }

        $userService = new UserService();
        $userVirtualAccount = UserVirtualAccount::where('user_id', $user->id)->first();

        $data = null;
        $virtual_account = null;

        if (!$userVirtualAccount) {
            try {
                $data = $userService->generateDotVirtualAccount($user);
                if (($data['status'] ?? '') === 'error') {
                    \Log::info('Dot account not provisioned during KYC approval: ' . ($data['message'] ?? 'Skipped'));
                    $data = null;
                } else {
                    $virtual_account = $data['data'] ?? null;
                }
            } catch (\Throwable $th) {
                \Log::warning('Dot account generation exception: ' . $th->getMessage());
            }
        }

        DB::beginTransaction();
        try {
            if ($data && $virtual_account) {
                UserVirtualAccount::create([
                    'user_id' => $user->id,
                    'account_number' => $virtual_account['accountNo'],
                    "external_id" => $virtual_account['externalId'],
                    "account_name" => ($virtual_account["firstname"] ?? '') . " " . ($virtual_account["lastname"] ?? ''),
                    "bank_code" => "999357",
                    "bank_name" => "DOT MFB"
                ]);
            }

            $user->update([
                'kyc_status' => Kyc::STATUS_VERIFIED,
            ]);

            if ($this->merchantKyb) {
                $this->merchantKyb->update([
                    'settlement_verified_at' => $this->merchantKyb->settlement_verified_at ?? now(),
                ]);
            }

            if ($this->kyc) {
                $this->kyc->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->alert('error', 'Failed to update KYC status: ' . $th->getMessage());
        }

        try {
            $targetNotifyObj = $this->kyc ?? $this->merchantKyb ?? (object)['user' => $user];
            $user->notify(new KycVerification($targetNotifyObj));
        } catch (\Throwable $e) {
            \Log::warning('KycVerification notification warning: ' . $e->getMessage());
        }

        // record activity
        try {
            $activityService = new UserActivityService();
            $activityService->logActivity(auth()->user()->name . ' approved KYC for ' . $user->name);
        } catch (\Throwable $e) {}

        $this->user->refresh();
        $this->flash('success', "KYC approved successfully", [], route('admin.kyc-verification'));
    }

    public function reject()
    {
        $user = $this->user ?? $this->kyc?->user;

        if (!$user) {
            return $this->alert('error', 'User record not found for this KYC.');
        }

        $reason = !empty($this->reason) ? trim($this->reason) : 'Submitted documents did not meet verification criteria.';

        DB::beginTransaction();
        try {
            $user->update([
                'kyc_status' => Kyc::STATUS_REJECTED
            ]);

            if ($this->kyc) {
                $this->kyc->save();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->alert('error', 'Failed to update KYC rejection: ' . $th->getMessage());
        }

        try {
            $targetNotifyObj = $this->kyc ?? $this->merchantKyb ?? (object)['user' => $user];
            $user->notify(new KycVerification($targetNotifyObj, $reason));
        } catch (\Throwable $e) {
            \Log::warning('KycVerification rejection notification warning: ' . $e->getMessage());
        }

        // record activity
        try {
            $activityService = new UserActivityService();
            $activityService->logActivity(auth()->user()->name . ' rejected KYC for ' . $user->name);
        } catch (\Throwable $e) {}

        $this->user->refresh();
        $this->flash('success', "KYC rejected successfully", [], route('admin.kyc-verification'));
    }
}
