<?php

namespace App\Livewire\Gateway;

use Livewire\Component;

class Documentation extends Component
{
    public function render()
    {
        return view('livewire.gateway.documentation', [
            'enableFlutterwave' => \App\Helpers\SettingHelper::isFlutterwaveEnabled(),
            'enablePalmPay' => \App\Helpers\SettingHelper::isPalmPayEnabled(),
            'enableUdara360' => \App\Helpers\SettingHelper::isUdara360Enabled(),
        ])->title('Developer API');
    }
}

