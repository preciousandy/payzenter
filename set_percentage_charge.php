<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingHelper;
use App\Models\Setting;

// For gateway payments: set fixed to 0, percentage to your desired value (e.g., 1.5 for 1.5%)
Setting::updateOrCreate(
    ['key' => SettingHelper::GATEWAY_PAYMENT_FIXED_FEE],
    ['value' => 0, 'type' => SettingHelper::TYPE_FIXED]
);

// Example: Set to 1.5% - change this value to your desired percentage!
$percentage = 1.5;
Setting::updateOrCreate(
    ['key' => SettingHelper::GATEWAY_PAYMENT_PERCENTAGE_FEE],
    ['value' => $percentage, 'type' => SettingHelper::TYPE_PERCENTAGE]
);

// You can also do the same for other charge types if needed:
// Withdrawals
Setting::updateOrCreate(
    ['key' => SettingHelper::WITHDRAWAL_FIXED_FEE],
    ['value' => 0, 'type' => SettingHelper::TYPE_FIXED]
);

// Virtual accounts
Setting::updateOrCreate(
    ['key' => SettingHelper::VIRTUAL_ACCOUNT_FIXED_FEE],
    ['value' => 0, 'type' => SettingHelper::TYPE_FIXED]
);

echo "Settings updated successfully!\n";
echo "Gateway Payment Fixed Fee: 0\n";
echo "Gateway Payment Percentage Fee: {$percentage}%\n";
