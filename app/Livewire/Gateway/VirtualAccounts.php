<?php

namespace App\Livewire\Gateway;

use App\Models\UserVirtualAccount;
use App\Models\VirtualAccount;
use App\Services\UserService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class VirtualAccounts extends Component
{
    use LivewireAlert, WithPagination;

    public $bvn;
    public $provider = 'palmpay';

    public function mount()
    {
        $this->bvn = auth()->user()->bvn;
    }

    public function generate(UserService $userService)
    {
        $this->validate([
            'bvn' => 'required|digits:11',
            'provider' => 'required|in:palmpay,flutterwave',
        ]);

        $user = auth()->user();

        if ($this->provider === 'palmpay') {
            $res = $userService->generatePalmPayVirtualAccount($user, $this->bvn, [
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone ?? $user->contact ?? '',
                'merchant_ref' => 'merchant_va_' . $user->id,
            ]);
        } else {
            $res = $userService->generateAccountNumber($user, $this->bvn);
        }

        if (($res['status'] ?? null) !== 'success') {
            $this->alert('error', $res['message'] ?? 'Unable to generate virtual account');
            return;
        }

        $data = $res['data'] ?? [];
        $accountNumber = $data['account_number'] ?? $data['accountNo'] ?? $data['accountnumber'] ?? $data['virtualAccountNo'] ?? null;
        $bankName = $data['bank_name'] ?? $data['bankName'] ?? ($data['bank']['name'] ?? null) ?? ($this->provider === 'palmpay' ? 'PalmPay' : 'Wema Bank');
        $bankCode = $data['bank_code'] ?? $data['bankCode'] ?? ($data['bank']['code'] ?? null);
        if (!is_string($bankCode) || trim($bankCode) === '') {
            $bankCode = '000000';
        }

        if (!is_string($accountNumber) || $accountNumber === '' || !is_string($bankName) || $bankName === '') {
            $this->alert('error', 'Virtual account response is missing required fields');
            return;
        }

        UserVirtualAccount::updateOrCreate(
            ['user_id' => $user->id],
            [
                'account_number' => $accountNumber,
                'bank_name' => $bankName,
                'bank_code' => $bankCode,
                'account_name' => $data['account_name'] ?? $data['virtualAccountName'] ?? ($user->first_name . ' ' . $user->last_name),
                'external_id' => $data['order_ref'] ?? $data['tx_ref'] ?? $data['accountReference'] ?? $user->email,
            ]
        );

        VirtualAccount::updateOrCreate(
            [
                'user_id' => $user->id,
                'account_number' => $accountNumber,
            ],
            [
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? $user->contact ?? '',
                'bank_name' => $bankName,
                'bank_code' => $bankCode,
                'account_name' => $data['account_name'] ?? $data['virtualAccountName'] ?? ($user->first_name . ' ' . $user->last_name),
                'account_reference' => $data['order_ref'] ?? $data['accountReference'] ?? ('va_' . time()),
                'provider' => $this->provider,
                'merchant_ref' => 'merchant_va_' . $user->id,
            ]
        );

        $this->alert('success', ucfirst($this->provider) . ' virtual account generated successfully');
    }

    public function render()
    {
        $virtualAccount = auth()->user()->virtualAccount;

        $allAccounts = VirtualAccount::where('user_id', auth()->id())
            ->latest('id')
            ->paginate(15);

        return view('livewire.gateway.virtual-accounts', [
            'virtualAccount' => $virtualAccount,
            'allAccounts' => $allAccounts,
        ])->title('Virtual Accounts');
    }
}
