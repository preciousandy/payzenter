<?php

namespace Database\Seeders;

use App\Helpers\SettingHelper;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => SettingHelper::REFERRAL_BONUS, 'value' => 100],
            ['key' => SettingHelper::TRANSFER_FEE, 'value' => 10, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::SAVE_HOLDINGS_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::INVESTMENT_FEE, 'value' => 1000, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::BUREAU_DA_CHANGE_BUY_FEE, 'value' => 10, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::BUREAU_DA_CHANGE_SELL_FEE, 'value' => 10, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::SELL_CRYPTO_FEE, 'value' => 10, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::BUY_CRYPTO_FEE, 'value' => 10, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::BUY_GIFT_CARD_FEE, 'value' => 10, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::LOCKED_SAVINGS_INTEREST, 'value' => 0.15, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::UNLOCKED_SAVINGS_INTEREST, 'value' => 5, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::BREAKING_LOCKED_SAVINGS_FEE, 'value' => 0.8, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::AJO_SAVINGS_FEE, 'value' => 200, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::AJO_SAVINGS_PERCENTAGE, 'value' => 0.8, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::AJO_SAVINGS_EARLY_CLOSE_FEE, 'value' => 2, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::AJO_SAVINGS_REFERRAL_BONUS, 'value' => 0.2, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::AJO_CONTRIBUTION_FEE, 'value' => 200, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::AJO_CONTRIBUTION_PERCENTAGE, 'value' => 0.8, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::AJO_CONTRIBUTION_REFERRAL_BONUS, 'value' => 0.2, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::AIRTIME_CASHBACK_FIXED, 'value' => 10, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::AIRTIME_CASHBACK_PERCENTAGE, 'value' => 0.1, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::DATA_CARD_CASHBACK_FIXED, 'value' => 10, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::DATA_CARD_CASHBACK_PERCENTAGE, 'value' => 0.1, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::INTERNATIONAL_TRANFER_FEE_FIXED, 'value' => 10, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::INTERNATIONAL_TRANFER_FEE_PERCENTAGE, 'value' => 0.1, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::HOLDINGS_PERCENTAGE_FEE, 'value' => 2, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::INVESTMENT_PERCENTAGE_FEE, 'value' => 3, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::CREATE_INTEREST_SAVINGS_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::CLOSE_UNLOCKED_SAVINGS_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::CLOSE_LOCKED_SAVINGS_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::FIXED_TRANSFER_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::TRANSFER_FLAT_RATE, 'value' => 0, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::GROUP_SAVINGS_WITHDRAWAL_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::MULTIPLE_TRANSFER_FIXED_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::FUND_BETTING_FIXED_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::FUND_BETTING_PERCENTAGE_FEE, 'value' => 0.1, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::WITHDRAWAL_FIXED_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::WITHDRAWAL_PERCENTAGE_FEE, 'value' => 1, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::VIRTUAL_ACCOUNT_FIXED_FEE, 'value' => 50, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::VIRTUAL_ACCOUNT_PERCENTAGE_FEE, 'value' => 0.5, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::GATEWAY_PAYMENT_FIXED_FEE, 'value' => 100, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::GATEWAY_PAYMENT_PERCENTAGE_FEE, 'value' => 1.0, 'type' => SettingHelper::TYPE_PERCENTAGE],
            ['key' => SettingHelper::ENABLE_FLUTTERWAVE, 'value' => 1, 'type' => SettingHelper::TYPE_FIXED],
            ['key' => SettingHelper::ENABLE_PALMPAY, 'value' => 1, 'type' => SettingHelper::TYPE_FIXED],
        ];

        foreach ($settings as $setting) {
            $settingRecord = Setting::where('key', $setting['key'])->first();
            if (!$settingRecord) {
                Setting::create($setting);
            }
        }
    }
}
