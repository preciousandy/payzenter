<?php

namespace Database\Seeders;

use App\Models\GatewayTransaction;
use App\Models\PaymentTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        $user2 = User::find(2) ?? $admin;
        $user3 = User::find(3) ?? $admin;
        $user4 = User::find(4) ?? $admin;

        // Seed Gateway Transactions if empty
        if (GatewayTransaction::count() === 0) {
            $gatewaySamples = [
                [
                    'merchant_id' => $adminId,
                    'reference' => 'plink_gw_78291048291847192837',
                    'amount' => 2500000, // ₦25,000.00
                    'fee' => 37500,      // ₦375.00
                    'currency' => 'NGN',
                    'method' => 'transfer',
                    'status' => 'success',
                    'provider' => 'palmpay',
                    'provider_reference' => 'PALM_98214791023',
                    'customer_name' => 'Chioma Adeyemi',
                    'customer_email' => 'chioma.adeyemi@gmail.com',
                    'customer_phone' => '08034567890',
                    'paid_at' => Carbon::now()->subHours(2),
                    'created_at' => Carbon::now()->subHours(2),
                    'updated_at' => Carbon::now()->subHours(2),
                ],
                [
                    'merchant_id' => $user2->id,
                    'reference' => 'plink_gw_19284719284719284720',
                    'amount' => 5000000, // ₦50,000.00
                    'fee' => 75000,      // ₦750.00
                    'currency' => 'NGN',
                    'method' => 'card',
                    'status' => 'success',
                    'provider' => 'paystack',
                    'provider_reference' => 'pstk_ch_829103847',
                    'customer_name' => 'Emeka Okafor',
                    'customer_email' => 'emeka.okafor@yahoo.com',
                    'customer_phone' => '08123456789',
                    'paid_at' => Carbon::now()->subHours(5),
                    'created_at' => Carbon::now()->subHours(5),
                    'updated_at' => Carbon::now()->subHours(5),
                ],
                [
                    'merchant_id' => $adminId,
                    'reference' => 'VA_TX_98124018247192847192',
                    'amount' => 1500000, // ₦15,000.00
                    'fee' => 22500,      // ₦225.00
                    'currency' => 'NGN',
                    'method' => 'transfer',
                    'status' => 'success',
                    'provider' => 'udara360',
                    'provider_reference' => 'UDARA_SESSION_849201',
                    'customer_name' => 'Fatima Bello',
                    'customer_email' => 'fatima.bello@gmail.com',
                    'customer_phone' => '07039281742',
                    'paid_at' => Carbon::now()->subDay(),
                    'created_at' => Carbon::now()->subDay(),
                    'updated_at' => Carbon::now()->subDay(),
                ],
                [
                    'merchant_id' => $user3->id,
                    'reference' => 'plink_gw_47291048291847192899',
                    'amount' => 1250000, // ₦12,500.00
                    'fee' => 18750,      // ₦187.50
                    'currency' => 'NGN',
                    'method' => 'transfer',
                    'status' => 'pending',
                    'provider' => 'palmpay',
                    'provider_reference' => 'PALM_PENDING_81923',
                    'customer_name' => 'Tunde Bakare',
                    'customer_email' => 'tunde.bakare@gmail.com',
                    'customer_phone' => '09012345678',
                    'paid_at' => null,
                    'created_at' => Carbon::now()->subMinutes(45),
                    'updated_at' => Carbon::now()->subMinutes(45),
                ],
                [
                    'merchant_id' => $user2->id,
                    'reference' => 'plink_gw_63829104829184719211',
                    'amount' => 800000, // ₦8,000.00
                    'fee' => 12000,     // ₦120.00
                    'currency' => 'NGN',
                    'method' => 'card',
                    'status' => 'failed',
                    'provider' => 'flutterwave',
                    'provider_reference' => 'FLW_FAIL_918237',
                    'customer_name' => 'Amaka Eze',
                    'customer_email' => 'amaka.eze@outlook.com',
                    'customer_phone' => '08098765432',
                    'paid_at' => null,
                    'created_at' => Carbon::now()->subDays(2),
                    'updated_at' => Carbon::now()->subDays(2),
                ],
                [
                    'merchant_id' => $adminId,
                    'reference' => 'VA_TX_55124018247192847334',
                    'amount' => 7500000, // ₦75,000.00
                    'fee' => 112500,     // ₦1,125.00
                    'currency' => 'NGN',
                    'method' => 'transfer',
                    'status' => 'success',
                    'provider' => 'udara360',
                    'provider_reference' => 'UDARA_SESSION_991823',
                    'customer_name' => 'Kudirat Alabi',
                    'customer_email' => 'kudirat.alabi@gmail.com',
                    'customer_phone' => '08023451122',
                    'paid_at' => Carbon::now()->subDays(3),
                    'created_at' => Carbon::now()->subDays(3),
                    'updated_at' => Carbon::now()->subDays(3),
                ],
            ];

            foreach ($gatewaySamples as $sample) {
                GatewayTransaction::create($sample);
            }
        }

        // Seed Payment Transactions (Wallet Top-ups) if empty
        if (PaymentTransaction::count() === 0) {
            $walletSamples = [
                [
                    'user_id' => $user2->id,
                    'transaction_id' => 'pstk_txn_89201928301',
                    'transaction_reference' => 'PZ_FUND_PSTK_91823019',
                    'amount' => 50000,
                    'currency' => 'NGN',
                    'charge_fee' => 750,
                    'payment_method' => 'paystack_card',
                    'created_at' => Carbon::now()->subHours(3),
                    'updated_at' => Carbon::now()->subHours(3),
                ],
                [
                    'user_id' => $user3->id,
                    'transaction_id' => 'flw_txn_77281920381',
                    'transaction_reference' => 'PZ_FUND_FLW_88291024',
                    'amount' => 100000,
                    'currency' => 'NGN',
                    'charge_fee' => 1400,
                    'payment_method' => 'flutterwave',
                    'created_at' => Carbon::now()->subDay(),
                    'updated_at' => Carbon::now()->subDay(),
                ],
                [
                    'user_id' => $user2->id,
                    'transaction_id' => 'pstk_txn_11827391029',
                    'transaction_reference' => 'PZ_FUND_PSTK_47192830',
                    'amount' => 25000,
                    'currency' => 'NGN',
                    'charge_fee' => 375,
                    'payment_method' => 'paystack_card',
                    'created_at' => Carbon::now()->subDays(2),
                    'updated_at' => Carbon::now()->subDays(2),
                ],
                [
                    'user_id' => $user4->id,
                    'transaction_id' => 'flw_txn_33918204712',
                    'transaction_reference' => 'PZ_FUND_FLW_19283741',
                    'amount' => 15000,
                    'currency' => 'NGN',
                    'charge_fee' => 225,
                    'payment_method' => 'flutterwave',
                    'created_at' => Carbon::now()->subDays(3),
                    'updated_at' => Carbon::now()->subDays(3),
                ],
                [
                    'user_id' => $user3->id,
                    'transaction_id' => 'mnf_txn_99182739182',
                    'transaction_reference' => 'PZ_FUND_MNF_55192837',
                    'amount' => 80000,
                    'currency' => 'NGN',
                    'charge_fee' => 1200,
                    'payment_method' => 'monnify',
                    'created_at' => Carbon::now()->subDays(4),
                    'updated_at' => Carbon::now()->subDays(4),
                ],
            ];

            foreach ($walletSamples as $sample) {
                PaymentTransaction::create($sample);
            }
        }
    }
}
