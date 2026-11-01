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

        $palmpayEnabled = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $udaraEnabled = \App\Helpers\SettingHelper::isUdara360Enabled();
        $flwEnabled = \App\Helpers\SettingHelper::isFlutterwaveEnabled();

        $default = strtolower((string) \App\Helpers\SettingHelper::get(\App\Helpers\SettingHelper::DEFAULT_VIRTUAL_ACCOUNT_PROVIDER, 'palmpay'));

        if ($default === 'udara360' && $udaraEnabled) {
            $this->provider = 'udara360';
        } elseif ($default === 'palmpay' && $palmpayEnabled) {
            $this->provider = 'palmpay';
        } elseif ($default === 'flutterwave' && $flwEnabled) {
            $this->provider = 'flutterwave';
        } elseif ($palmpayEnabled) {
            $this->provider = 'palmpay';
        } elseif ($udaraEnabled) {
            $this->provider = 'udara360';
        } elseif ($flwEnabled) {
            $this->provider = 'flutterwave';
        } else {
            $this->provider = '';
        }
    }

    public function generate(UserService $userService)
    {
        $palmpayEnabled = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $udaraEnabled = \App\Helpers\SettingHelper::isUdara360Enabled();
        $flwEnabled = \App\Helpers\SettingHelper::isFlutterwaveEnabled();

        $enabledProviders = [];
        if ($palmpayEnabled) $enabledProviders[] = 'palmpay';
        if ($udaraEnabled) $enabledProviders[] = 'udara360';
        if ($flwEnabled) $enabledProviders[] = 'flutterwave';

        if (empty($enabledProviders)) {
            $this->alert('error', 'All virtual account providers are currently disabled by administrator.');
            return;
        }

        $this->validate([
            'bvn' => 'required|digits:11',
            'provider' => 'required|in:' . implode(',', $enabledProviders),
        ]);

        if ($this->provider === 'palmpay' && !$palmpayEnabled) {
            $this->alert('error', 'PalmPay virtual account generation is currently disabled by administrator');
            return;
        }
        if ($this->provider === 'udara360' && !$udaraEnabled) {
            $this->alert('error', 'Udara360 virtual account generation is currently disabled by administrator');
            return;
        }
        if ($this->provider === 'flutterwave' && !$flwEnabled) {
            $this->alert('error', 'Flutterwave virtual account generation is currently disabled by administrator');
            return;
        }

        $user = auth()->user();

        $customerDetails = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone ?? $user->contact ?? '',
            'merchant_ref' => 'merchant_va_' . $user->id,
        ];

        if ($this->provider === 'palmpay') {
            $res = $userService->generatePalmPayVirtualAccount($user, $this->bvn, $customerDetails);
        } elseif ($this->provider === 'udara360') {
            $res = $userService->generateUdara360VirtualAccount($user, $this->bvn, $customerDetails);
        } else {
            $res = $userService->generateAccountNumber($user, $this->bvn, $customerDetails);
        }

        if (($res['status'] ?? null) !== 'success') {
            $this->alert('error', $res['message'] ?? 'Unable to generate virtual account');
            return;
        }

        $data = $res['data'] ?? [];
        $accountNumber = $data['account_number'] ?? $data['accountNo'] ?? $data['accountnumber'] ?? $data['virtualAccountNo'] ?? null;
        
        $defaultBankName = match ($this->provider) {
            'udara360' => (new \App\Services\Udara360Service())->getBankName(),
            'flutterwave' => 'Wema Bank',
            default => 'PalmPay',
        };
        $bankName = $data['bank_name'] ?? $data['bankName'] ?? ($data['bank']['name'] ?? null) ?? $defaultBankName;

        $defaultBankCode = match ($this->provider) {
            'udara360' => (new \App\Services\Udara360Service())->getBankCode(),
            'flutterwave' => '035',
            default => '000000',
        };
        $bankCode = $data['bank_code'] ?? $data['bankCode'] ?? ($data['bank']['code'] ?? null) ?? $defaultBankCode;

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

        $palmpayEnabled = \App\Helpers\SettingHelper::isPalmPayEnabled();
        $udaraEnabled = \App\Helpers\SettingHelper::isUdara360Enabled();
        $flwEnabled = \App\Helpers\SettingHelper::isFlutterwaveEnabled();

        $allAccounts = VirtualAccount::where('user_id', auth()->id())
            ->when(!$palmpayEnabled, fn($q) => $q->where('provider', '!=', 'palmpay'))
            ->when(!$udaraEnabled, fn($q) => $q->where('provider', '!=', 'udara360'))
            ->when(!$flwEnabled, fn($q) => $q->where('provider', '!=', 'flutterwave'))
            ->latest('id')
            ->paginate(15);

        return view('livewire.gateway.virtual-accounts', [
            'virtualAccount' => $virtualAccount,
            'allAccounts' => $allAccounts,
            'palmpayEnabled' => $palmpayEnabled,
            'udaraEnabled' => $udaraEnabled,
            'flwEnabled' => $flwEnabled,
        ])->title('Virtual Accounts');
    }
}
