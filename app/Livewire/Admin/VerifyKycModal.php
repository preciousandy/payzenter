<?php

namespace App\Livewire\Admin;

use App\Models\Kyc;
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
    public $reason;

    public function mount()
    {
        // $this->kyc = Kyc::first();
    }
    public function render()
    {
        return view('livewire.admin.verify-kyc-modal');
    }

    public function approve()
    {
        $user = $this->kyc->user;

        $userService = new UserService();

        $userVirtualAccount = UserVirtualAccount::where('user_id', $user->id)->first();

        $data = null;

        $virtual_account = null;

        if (!$userVirtualAccount) {
            $data = $userService->generateDotVirtualAccount($user);

            if ($data['status'] == 'error') {
                $message = 'Dot account : ' . $data['message'];
                return $this->alert('error', $message);
            }

            $virtual_account = $data['data'];
        }

        // $quidaxData = null;

       /* if (!$user->quidax_id) {
            $response = $userService->generateQuidaxWallet($user);


            if ($response['status'] == 'error') {

                $message = 'Quidax wallet creation failed: ' . $response['message'];

                return $this->alert('error', $message);
            }

            $quidax_wallet_id = $response['data']['id'];
            $quidax_sn = $response['data']['sn'];
            $quidax_email = $response['data']['email'];
        }*/



        DB::beginTransaction();

        if ($data) {
            UserVirtualAccount::create([
                'user_id' => $this->kyc->user_id,
                'account_number' => $virtual_account['accountNo'],
                "external_id" => $virtual_account['externalId'],
                "account_name" => $virtual_account["firstname"] . " " . $virtual_account["lastname"],
                "bank_code" => "999357",
                "bank_name" => "DOT MFB"
            ]);
        }


        $user->update([
            'kyc_status' => Kyc::STATUS_VERIFIED,
           // 'quidax_id' => $quidax_wallet_id ?? $user->quidax_id,
          //  'quidax_sn' => $quidax_sn ?? $user->quidax_sn,
            //'quidax_email' => $quidax_email ?? $user->quidax_email,
        ]);

        $this->kyc->save();

        DB::commit();



        $this->kyc->user->notify(new KycVerification($this->kyc));

        // record activity
        $activityService = new UserActivityService();

        $activityService->logActivity(auth()->user()->name . ' approved KYC for ' . $this->kyc->user->name);

        $this->flash('success', "KYC approved successfully", [], route('admin.kyc-verification'));
    }

    public function reject()
    {
        $this->kyc->user->update([
            'kyc_status' => Kyc::STATUS_REJECTED
        ]);

        $this->kyc->save();

        $this->kyc->user->notify(new KycVerification($this->kyc, $this->reason));

        // record activity
        $activityService = new UserActivityService();

        $activityService->logActivity(auth()->user()->name . ' rejected KYC for ' . $this->kyc->user->name);

        $this->flash('success', "KYC rejected successfully", [], route('admin.kyc-verification'));
    }
}
