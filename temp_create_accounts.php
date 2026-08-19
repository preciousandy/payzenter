<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kyc;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

Role::findOrCreate('admin', 'web');
Role::findOrCreate('customer', 'web');

$admin = User::updateOrCreate(
    ['email' => 'admin@fitpay.local'],
    [
        'first_name' => 'System',
        'last_name' => 'Admin',
        'phone' => '08000000001',
        'username' => 'fitpayadmin',
        'password' => Hash::make('Admin@12345'),
        'email_verified_at' => now(),
        'wallet_balance' => 0,
        'kyc_status' => Kyc::STATUS_VERIFIED,
        'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        'platform' => User::PLATFORM_WEB,
    ]
);
$admin->syncRoles(['admin']);

$user = User::updateOrCreate(
    ['email' => 'user@fitpay.local'],
    [
        'first_name' => 'Demo',
        'last_name' => 'Customer',
        'phone' => '08000000004',
        'username' => 'fitpayuser',
        'password' => Hash::make('User@12345'),
        'email_verified_at' => now(),
        'wallet_balance' => 0,
        'kyc_status' => Kyc::STATUS_VERIFIED,
        'account_status' => User::ACCOUNT_STATUS_ACTIVE,
        'platform' => User::PLATFORM_WEB,
    ]
);
$user->syncRoles(['customer']);

echo 'admin_id=' . $admin->id . PHP_EOL;
echo 'user_id=' . $user->id . PHP_EOL;
