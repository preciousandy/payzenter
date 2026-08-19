<?php

namespace App\Livewire\Gateway;

use App\Helpers\SettingHelper;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Preferences extends Component
{
    use LivewireAlert;

    public $preferred_provider;
    public $gateway_charge_mode;

    public function mount()
    {
        $user = Auth::user();
        $this->preferred_provider = $user->preferred_gateway_provider;
        $this->gateway_charge_mode = $user->gateway_charge_mode ?? 'merchant';
    }

    public function savePreferences()
    {
        $validated = $this->validate([
            'preferred_provider' => 'nullable|in:flutterwave,palmpay',
            'gateway_charge_mode' => 'required|in:merchant,customer',
        ]);

        $enableFlutterwave = (bool) SettingHelper::get(SettingHelper::ENABLE_FLUTTERWAVE, '1');
        $enablePalmPay = (bool) SettingHelper::get(SettingHelper::ENABLE_PALMPAY, '1');

        $provider = $validated['preferred_provider'] ?? null;
        if ($provider === '') {
            $provider = null;
        }

        if ($provider === 'flutterwave' && !$enableFlutterwave) {
            $this->addError('preferred_provider', 'Flutterwave is currently disabled by admin');
            return;
        }
        if ($provider === 'palmpay' && !$enablePalmPay) {
            $this->addError('preferred_provider', 'PalmPay is currently disabled by admin');
            return;
        }

        $user = Auth::user();
        $user->update([
            'preferred_gateway_provider' => $provider,
            'gateway_charge_mode' => $validated['gateway_charge_mode'],
        ]);

        $this->preferred_provider = $user->fresh()->preferred_gateway_provider;
        $this->gateway_charge_mode = $user->fresh()->gateway_charge_mode;

        $this->alert('success', 'Gateway preferences updated successfully!');
    }

    public function render()
    {
        $enableFlutterwave = (bool) SettingHelper::get(SettingHelper::ENABLE_FLUTTERWAVE, '1');
        $enablePalmPay = (bool) SettingHelper::get(SettingHelper::ENABLE_PALMPAY, '1');

        return view('livewire.gateway.preferences', [
            'enableFlutterwave' => $enableFlutterwave,
            'enablePalmPay' => $enablePalmPay,
        ])->title('Gateway Preferences');
    }
}
