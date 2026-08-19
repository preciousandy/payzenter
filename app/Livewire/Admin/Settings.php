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
    public $enable_flutterwave = true;
    public $enable_palmpay = true;

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
        Setting::firstOrCreate(
            ['key' => SettingHelper::ENABLE_FLUTTERWAVE],
            ['value' => '1', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::firstOrCreate(
            ['key' => SettingHelper::ENABLE_PALMPAY],
            ['value' => '1', 'type' => SettingHelper::TYPE_FIXED]
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
        $this->enable_flutterwave = (bool) Setting::where('key', SettingHelper::ENABLE_FLUTTERWAVE)->value('value', '1');
        $this->enable_palmpay = (bool) Setting::where('key', SettingHelper::ENABLE_PALMPAY)->value('value', '1');
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
        ]);

        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_FLUTTERWAVE],
            ['value' => $this->enable_flutterwave ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        Setting::updateOrCreate(
            ['key' => SettingHelper::ENABLE_PALMPAY],
            ['value' => $this->enable_palmpay ? '1' : '0', 'type' => SettingHelper::TYPE_FIXED]
        );

        $this->reloadSettings();
        $this->loadGatewaySettings();

        $this->alert('success', 'Gateway settings updated');
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
