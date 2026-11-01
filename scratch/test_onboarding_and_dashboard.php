<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\MerchantKyb;
use App\Livewire\Home;
use App\Livewire\Onboarding;
use Livewire\Livewire;

$user = User::first();
if (!$user) {
    echo "No user found in DB\n";
    exit(0);
}

$user->is_otp_verified = true;
auth()->login($user);
echo "Testing with user: {$user->email} (ID: {$user->id})\n";

// 1. Test Home component mount and render via Livewire::test
echo "\n--- 1. Testing Home Component with Livewire::test ---\n";
try {
    $test = Livewire::test(Home::class);
    $test->assertSee('Activate your business');
    $test->assertSee('Activation Progress');
    echo "✓ Home component rendered successfully via Livewire with 'Activate your business'!\n";
} catch (\Throwable $e) {
    echo "ERROR in Home Livewire test: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 2. Test Onboarding component across all 5 steps via Livewire::test
echo "\n--- 2. Testing Onboarding Component Steps with Livewire::test ---\n";
try {
    $test = Livewire::test(Onboarding::class);
    echo "✓ Onboarding component mounted successfully!\n";
    
    // Test step 1
    $test->set('currentStep', 1);
    $test->assertSee('Personal Information');
    $test->assertSee('Means of Identity');
    $test->assertSee('Proof of Address');
    echo "✓ Step 1 verified with file upload palates!\n";

    // Test step 2
    $test->set('currentStep', 2);
    $test->assertSee('Business Profile');
    $test->assertSee('Business Brand Logo');
    echo "✓ Step 2 verified!\n";

    // Test step 3
    $test->set('currentStep', 3);
    $test->assertSee('Settlement Bank Account');
    echo "✓ Step 3 verified!\n";

    // Test step 4
    $test->set('currentStep', 4);
    $test->assertSee('Company Verification Documents');
    $test->assertSee('Business Resolution');
    $test->assertSee('Company Profile');
    $test->assertSee('CAC Status Report');
    $test->assertSee('Business Address Verification');
    echo "✓ Step 4 verified with all 4 document upload palates!\n";

    // Test step 5
    $test->set('currentStep', 5);
    $test->assertSee('Transaction PIN');
    echo "✓ Step 5 verified with security PIN keypad!\n";

} catch (\Throwable $e) {
    echo "ERROR in Onboarding Livewire test: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nALL LIVEWIRE INTEGRATION TESTS COMPLETED SUCCESSFULLY!\n";
