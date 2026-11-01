<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
  const TYPE_FIXED = 'fixed';
  const TYPE_PERCENTAGE = 'percentage';


  const REFERRAL_BONUS = 'referral_bonus';
  const TRANSFER_FEE = 'transfer_fee';
  const SAVE_HOLDINGS_FEE = 'save_holdings_fee';
  const HOLDINGS_PERCENTAGE_FEE = 'holdings_percentage_fee';
  const INVESTMENT_FEE = 'investment_fee';
  const INVESTMENT_PERCENTAGE_FEE = "investment_percentage_fee";
  const BUREAU_DA_CHANGE_BUY_FEE = "bureau_da_change_buy_fee";
  const BUREAU_DA_CHANGE_SELL_FEE = "bureau_da_change_sell_fee";
  const BUY_CRYPTO_FEE = "buy_crypto_fee";
  const SELL_CRYPTO_FEE = "sell_crypto_fee";
  const BUY_GIFT_CARD_FEE = "buy_gift_card_fee";
  const LOCKED_SAVINGS_INTEREST = 'locked_savings_interest';
  const UNLOCKED_SAVINGS_INTEREST = 'unlocked_savings_interest';
  const BREAKING_LOCKED_SAVINGS_FEE = 'breaking_locked_savings_fee';
  const AJO_SAVINGS_FEE = 'ajo_savings_fee';
  const AJO_SAVINGS_PERCENTAGE = 'ajo_savings_percentage';
  const AJO_SAVINGS_EARLY_CLOSE_FEE = 'ajo_savings_early_close_fee';
  const AJO_SAVINGS_REFERRAL_BONUS = 'ajo_savings_referral_bonus';
  const AJO_CONTRIBUTION_FEE = 'ajo_contribution_fee';
  const AJO_CONTRIBUTION_PERCENTAGE = 'ajo_contribution_percentage';
  const AJO_CONTRIBUTION_REFERRAL_BONUS = 'ajo_contribution_referral_bonus';
  const AIRTIME_CASHBACK_FIXED = 'airtime_cashback_fixed';
  const AIRTIME_CASHBACK_PERCENTAGE = 'airtime_cashback_percentage';
  const DATA_CARD_CASHBACK_FIXED = 'data_card_cashback_fixed';
  const DATA_CARD_CASHBACK_PERCENTAGE = 'data_card_cashback_percentage';
  const INTERNATIONAL_TRANFER_FEE_FIXED = 'international_transfer_fee_fixed';
  const INTERNATIONAL_TRANFER_FEE_PERCENTAGE = 'international_transfer_fee_percentage';
  const CREATE_INTEREST_SAVINGS_FEE = 'create_interest_savings_fee';
  const CLOSE_UNLOCKED_SAVINGS_FEE = 'close_unlocked_savings_fee';
  const CLOSE_LOCKED_SAVINGS_FEE = 'close_locked_savings_fee';
  const FIXED_TRANSFER_FEE = 'fixed_transfer_fee';
  const TRANSFER_FLAT_RATE = 'transfer_flat_rate';
  const GROUP_SAVINGS_WITHDRAWAL_FEE = 'group_savings_withdrawal_fee';
  const MULTIPLE_TRANSFER_FIXED_FEE = 'multiple_transfer_fixed_fee';
  const FUND_BETTING_FIXED_FEE = 'fund_betting_fixed_fee';
  const FUND_BETTING_PERCENTAGE_FEE = 'fund_betting_percentage_fee';
  const WITHDRAWAL_FIXED_FEE = 'withdrawal_fixed_fee';
  const WITHDRAWAL_PERCENTAGE_FEE = 'withdrawal_percentage_fee';
  const VIRTUAL_ACCOUNT_FIXED_FEE = 'virtual_account_fixed_fee';
  const VIRTUAL_ACCOUNT_PERCENTAGE_FEE = 'virtual_account_percentage_fee';
  const GATEWAY_PAYMENT_FIXED_FEE = 'gateway_payment_fixed_fee';
  const GATEWAY_PAYMENT_PERCENTAGE_FEE = 'gateway_payment_percentage_fee';
  const ENABLE_FLUTTERWAVE = 'enable_flutterwave';
  const ENABLE_PALMPAY = 'enable_palmpay';
  const ENABLE_UDARA360 = 'enable_udara360';
  const ENABLE_PAYSTACK_CARD = 'enable_paystack_card';
  const DEFAULT_VIRTUAL_ACCOUNT_PROVIDER = 'default_virtual_account_provider';
  const UDARA360_BASE_URL = 'udara360_base_url';
  const UDARA360_CLIENT_ID = 'udara360_client_id';
  const UDARA360_CLIENT_SECRET = 'udara360_client_secret';
  const UDARA360_PARENT_ACCOUNT = 'udara360_parent_account';
  const UDARA360_PRODUCT_CODE = 'udara360_product_code';
  const UDARA360_BANK_NAME = 'udara360_bank_name';
  const UDARA360_BANK_CODE = 'udara360_bank_code';

  const RELOADLY_ACCESS_TOKEN = 'reload_access_token';


  public static function get($key, $default = null)
  {
    $setting = Setting::where('key', $key)->first();
    return $setting ? $setting->value : $default;
  }

  public static function set($key, $value)
  {
    return Setting::updateOrCreate(
      ['key' => $key],
      ['value' => $value]
    );
  }

  public static function isPalmPayEnabled(): bool
  {
    return filter_var(self::get(self::ENABLE_PALMPAY, '1'), FILTER_VALIDATE_BOOLEAN);
  }

  public static function isUdara360Enabled(): bool
  {
    return filter_var(self::get(self::ENABLE_UDARA360, '1'), FILTER_VALIDATE_BOOLEAN);
  }

  public static function isFlutterwaveEnabled(): bool
  {
    return filter_var(self::get(self::ENABLE_FLUTTERWAVE, '0'), FILTER_VALIDATE_BOOLEAN);
  }

  public static function isPaystackCardEnabled(): bool
  {
    return filter_var(self::get(self::ENABLE_PAYSTACK_CARD, '1'), FILTER_VALIDATE_BOOLEAN);
  }
}
