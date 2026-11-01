<?php

namespace App\Livewire\Admin;

use App\Helpers\SettingHelper;
use App\Models\Setting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Settings extends Component
{
    use LivewireAlert;

    public $values = [];
    public $settings;
    public $new_key;
    public $new_value;
    public $enable_flutterwave = false;
    public $enable_palmpay = true;
    public $enable_udara360 = true;
    public $enable_paystack_card = true;
    public $default_va_provider = 'palmpay';

    public $udara360_client_id = '';
    public $udara360_client_secret = '';
    public $udara360_base_url = 'https://openapi.udara360.io';
    public $udara360_parent_account = '';
    public $udara360_product_code = '301';
    public $udara360_bank_name = 'Providus Bank';
    public $udara360_bank_code = '000023';
    public $udara360_webhook_secret = '';

    public function mount()
    {
        // ensure transfer_flat_rate exists
        Setting::firstOrCreate(
            ['key' => SettingHelper::TRANSFER_FLAT_RATE],
            ['value' => 0, 'type' => SettingHelper::TYPE_FIXED]
        );

        // ensure withdrawal charges exist
        Setting::firstOrCreate(
            ['key' => SettingHelper::WITHDRAWAL_FIXED_FEE],
            ['value' => 100, 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::WITHDRAWAL_PERCENTAGE_FEE],
            ['value' => 1, 'type' => SettingHelper::TYPE_PERCENTAGE]
        );

        // ensure virtual account charges exist
        Setting::firstOrCreate(
            ['key' => SettingHelper::VIRTUAL_ACCOUNT_FIXED_FEE],
            ['value' => 50, 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::VIRTUAL_ACCOUNT_PERCENTAGE_FEE],
            ['value' => 0.5, 'type' => SettingHelper::TYPE_PERCENTAGE]
        );

        // ensure gateway payment charges exist
        Setting::firstOrCreate(
            ['key' => SettingHelper::GATEWAY_PAYMENT_FIXED_FEE],
            ['value' => 100, 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::GATEWAY_PAYMENT_PERCENTAGE_FEE],
            ['value' => 1.0, 'type' => SettingHelper::TYPE_PERCENTAGE]
        );

        // ensure gateway provider toggles exist
        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_FLUTTERWAVE],
            ['value' => '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::ENABLE_PALMPAY],
            ['value' => '1', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::ENABLE_UDARA360],
            ['value' => '1', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::ENABLE_PAYSTACK_CARD],
            ['value' => '1', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::DEFAULT_VIRTUAL_ACCOUNT_PROVIDER],
            ['value' => 'palmpay', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_BASE_URL],
            ['value' => env('UDARA360_BASE_URL', 'https://openapi.udara360.io'), 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_CLIENT_ID],
            ['value' => env('UDARA360_CLIENT_ID', ''), 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_CLIENT_SECRET],
            ['value' => env('UDARA360_CLIENT_SECRET', ''), 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_PARENT_ACCOUNT],
            ['value' => env('UDARA360_PARENT_ACCOUNT', ''), 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_PRODUCT_CODE],
            ['value' => env('UDARA360_PRODUCT_CODE', '301'), 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_BANK_NAME],
            ['value' => env('UDARA360_BANK_NAME', 'Providus Bank'), 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::UDARA360_BANK_CODE],
            ['value' => env('UDARA360_BANK_CODE', '000023'), 'type' => SettingHelper::TYPE_FIXED]
        );

        $this->reloadSettings();
        $this->loadGatewaySettings();
    }

    private function reloadSettings(): void
    {
        $this->settings = Setting::orderBy('key')->get();
        $this->values = $this->settings->toArray();
    }

    private function loadGatewaySettings(): void
    {
        $this->enable_flutterwave = (bool) Setting::where('key', SettingHelper::ENABLE_FLUTTERWAVE)->value('value', '0');
        $this->enable_palmpay = (bool) Setting::where('key', SettingHelper::ENABLE_PALMPAY)->value('value', '1');
        $this->enable_udara360 = (bool) Setting::where('key', SettingHelper::ENABLE_UDARA360)->value('value', '1');
        $this->enable_paystack_card = (bool) Setting::where('key', SettingHelper::ENABLE_PAYSTACK_CARD)->value('value', '1');
        $this->default_va_provider = (string) Setting::where('key', SettingHelper::DEFAULT_VIRTUAL_ACCOUNT_PROVIDER)->value('value', 'palmpay');

        $this->udara360_base_url = (string) Setting::where('key', SettingHelper::UDARA360_BASE_URL)->value('value', env('UDARA360_BASE_URL', 'https://openapi.udara360.io'));
        $this->udara360_client_id = (string) Setting::where('key', SettingHelper::UDARA360_CLIENT_ID)->value('value', env('UDARA360_CLIENT_ID', ''));
        $this->udara360_client_secret = (string) Setting::where('key', SettingHelper::UDARA360_CLIENT_SECRET)->value('value', env('UDARA360_CLIENT_SECRET', ''));
        $this->udara360_parent_account = (string) Setting::where('key', SettingHelper::UDARA360_PARENT_ACCOUNT)->value('value', env('UDARA360_PARENT_ACCOUNT', ''));
        $this->udara360_product_code = (string) Setting::where('key', SettingHelper::UDARA360_PRODUCT_CODE)->value('value', env('UDARA360_PRODUCT_CODE', '301'));
        $this->udara360_bank_name = (string) Setting::where('key', SettingHelper::UDARA360_BANK_NAME)->value('value', env('UDARA360_BANK_NAME', 'Providus Bank'));
        $this->udara360_bank_code = (string) Setting::where('key', SettingHelper::UDARA360_BANK_CODE)->value('value', env('UDARA360_BANK_CODE', '000023'));
        $this->udara360_webhook_secret = (string) Setting::where('key', 'udara360_webhook_secret')->value('value', env('UDARA360_WEBHOOK_SECRET', ''));
    }

    public function togglePaystackCard(): void
    {
        $this->enable_paystack_card = !$this->enable_paystack_card;
        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_PAYSTACK_CARD],
            ['value' => $this->enable_paystack_card ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );
        \Illuminate\Support\Facades\Cache::flush();
        $status = $this->enable_paystack_card ? 'enabled' : 'disabled';
        $this->alert('success', "Paystack card payment has been {$status} successfully.");
    }

    public function toggleUdara360(): void
    {
        $this->enable_udara360 = !$this->enable_udara360;
        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_UDARA360],
            ['value' => $this->enable_udara360 ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );
        \Illuminate\Support\Facades\Cache::flush();
        $status = $this->enable_udara360 ? 'enabled' : 'disabled';
        $this->alert('success', "Udara360 provider has been {$status} successfully.");
    }

    public function toggleFlutterwave(): void
    {
        $this->enable_flutterwave = !$this->enable_flutterwave;
        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_FLUTTERWAVE],
            ['value' => $this->enable_flutterwave ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );
        \Illuminate\Support\Facades\Cache::flush();
        $status = $this->enable_flutterwave ? 'enabled' : 'disabled';
        $this->alert('success', "Flutterwave provider has been {$status} successfully.");
    }

    public function togglePalmPay(): void
    {
        $this->enable_palmpay = !$this->enable_palmpay;
        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_PALMPAY],
            ['value' => $this->enable_palmpay ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );
        \Illuminate\Support\Facades\Cache::flush();
        $status = $this->enable_palmpay ? 'enabled' : 'disabled';
        $this->alert('success', "PalmPay provider has been {$status} successfully.");
    }

    public function addSetting()
    {
        $data = $this->validate([
            'new_key' => 'required|string|max:100',
            'new_value' => 'required|string|max:500',
        ]);

        Setting::updateOrCreate(
            ['key' => trim($data['new_key'])],
            ['value' => $data['new_value']]
        );

        $this->new_key = null;
        $this->new_value = null;

        $this->reloadSettings();
        $this->alert('success', 'Setting added');
    }

    public function saveGatewaySettings()
    {
        $this->validate([
            'enable_flutterwave' => 'boolean',
            'enable_palmpay' => 'boolean',
            'enable_udara360' => 'boolean',
            'enable_paystack_card' => 'boolean',
            'default_va_provider' => 'required|in:palmpay,udara360,flutterwave',
            'udara360_client_id' => 'nullable|string|max:255',
            'udara360_client_secret' => 'nullable|string|max:255',
            'udara360_base_url' => 'nullable|url|max:255',
            'udara360_parent_account' => 'nullable|string|max:50',
            'udara360_product_code' => 'nullable|string|max:20',
            'udara360_bank_name' => 'nullable|string|max:100',
            'udara360_bank_code' => 'nullable|string|max:20',
            'udara360_webhook_secret' => 'nullable|string|max:255',
        ]);

        // If the selected default VA provider was disabled, automatically switch default to an enabled provider
        if ($this->default_va_provider === 'udara360' && !$this->enable_udara360) {
            $this->default_va_provider = $this->enable_palmpay ? 'palmpay' : ($this->enable_flutterwave ? 'flutterwave' : 'palmpay');
        } elseif ($this->default_va_provider === 'flutterwave' && !$this->enable_flutterwave) {
            $this->default_va_provider = $this->enable_palmpay ? 'palmpay' : ($this->enable_udara360 ? 'udara360' : 'palmpay');
        } elseif ($this->default_va_provider === 'palmpay' && !$this->enable_palmpay) {
            $this->default_va_provider = $this->enable_udara360 ? 'udara360' : ($this->enable_flutterwave ? 'flutterwave' : 'udara360');
        }

        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_FLUTTERWAVE],
            ['value' => $this->enable_flutterwave ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_PALMPAY],
            ['value' => $this->enable_palmpay ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_UDARA360],
            ['value' => $this->enable_udara360 ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_PAYSTACK_CARD],
            ['value' => $this->enable_paystack_card ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::DEFAULT_VIRTUAL_ACCOUNT_PROVIDER],
            ['value' => $this->default_va_provider, 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_BASE_URL],
            ['value' => $this->udara360_base_url ?: 'https://openapi.udara360.io', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_CLIENT_ID],
            ['value' => $this->udara360_client_id ?? '', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_CLIENT_SECRET],
            ['value' => $this->udara360_client_secret ?? '', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_PARENT_ACCOUNT],
            ['value' => $this->udara360_parent_account ?? '', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_PRODUCT_CODE],
            ['value' => $this->udara360_product_code ?: '301', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_BANK_NAME],
            ['value' => $this->udara360_bank_name ?: 'Providus Bank', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::UDARA360_BANK_CODE],
            ['value' => $this->udara360_bank_code ?: '000023', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => 'udara360_webhook_secret'],
            ['value' => $this->udara360_webhook_secret ?? '', 'type' => SettingHelper::TYPE_FIXED]
        );

        // Clear any cached access token so new credentials take effect immediately
        \Illuminate\Support\Facades\Cache::flush();

        $this->reloadSettings();
        $this->loadGatewaySettings();

        $this->alert('success', 'Gateway & Udara360 settings updated successfully');
    }

    public function save()
    {
        $this->validate([
            'values.*.value' => 'required',
        ]);

        foreach ($this->values as $key => $value) {
            $setting = Setting::find($value['id']);
            $setting->update([
                'value' => $value['value'],
            ]);
        }

        $this->flash('success', 'Settings updated successfully', [], route('admin.settings'));
    }


    public function render()
    {
        return view('livewire.admin.settings',)->extends('layouts.admin');
    }
}
