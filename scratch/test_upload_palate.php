<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\MerchantKyb;
use App\Livewire\Onboarding;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

Storage::fake('public');

$user = User::first();
$user->is_otp_verified = true;
auth()->login($user);

echo "Testing File Uploads on Onboarding Component with User: {$user->email}\n";

$fakePdf = UploadedFile::fake()->create('cac_status_report.pdf', 500, 'application/pdf');

try {
    $test = Livewire::test(Onboarding::class)
        ->set('currentStep', 4)
        ->set('cac_registration_number', 'RC1234567')
        ->set('status_report_upload', $fakePdf);

    echo "✓ Livewire accepted fake status_report_upload!\n";
    
    // Check if status_report_path was populated in component
    $savedPath = $test->get('status_report_path');
    echo "Saved status_report_path in component: " . ($savedPath ?: 'None') . "\n";
    
    // Check if updated in database
    $kyb = MerchantKyb::where('user_id', $user->id)->first();
    echo "Saved status_report_path in database: " . ($kyb->status_report_path ?: 'None') . "\n";
    
    if ($savedPath && $kyb->status_report_path) {
        echo "✓ File was automatically persisted to disk and database immediately upon upload!\n";
    }
} catch (\Throwable $e) {
    echo "ERROR in upload test: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nFILE UPLOAD TEST COMPLETED!\n";
