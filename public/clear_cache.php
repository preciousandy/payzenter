<?php

header('Content-Type: text/plain');

$output = [];

// 1. Reset OPcache (reloads modified PHP files into memory)
if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        $output[] = "✓ OPcache successfully reset.";
    } else {
        $output[] = "✗ OPcache reset call failed.";
    }
} else {
    $output[] = "- OPcache extension not loaded.";
}

// 2. Remove Laravel bootstrap cache files
$cacheFiles = [
    __DIR__ . '/../bootstrap/cache/config.php',
    __DIR__ . '/../bootstrap/cache/routes-v7.php',
    __DIR__ . '/../bootstrap/cache/events.php',
    __DIR__ . '/../bootstrap/cache/services.php',
    __DIR__ . '/../bootstrap/cache/packages.php',
];

$removedFiles = [];
foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        if (@unlink($file)) {
            $removedFiles[] = basename($file);
        }
    }
}

if (!empty($removedFiles)) {
    $output[] = "✓ Removed cached bootstrap files: " . implode(', ', $removedFiles);
} else {
    $output[] = "- No bootstrap cache files needed deletion.";
}

// 3. Clear compiled views
$viewDir = __DIR__ . '/../storage/framework/views';
if (is_dir($viewDir)) {
    $viewFiles = glob($viewDir . '/*.php');
    $viewCount = 0;
    foreach ($viewFiles as $vf) {
        if (is_file($vf) && @unlink($vf)) {
            $viewCount++;
        }
    }
    $output[] = "✓ Cleared $viewCount compiled view templates.";
}

// 4. Test Paystack connection from current server environment
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $ps = new \App\Services\PaystackService();
    $testRes = $ps->initializeTransaction(100.0, 'test@payzenter.com', 'test_ping_' . time(), 'https://payzenter.com');

    if (!empty($testRes['status'])) {
        $output[] = "✓ Paystack API initialization TEST: SUCCESS (Authorization URL generated).";
    } else {
        $output[] = "✗ Paystack API initialization TEST: FAILED - " . ($testRes['message'] ?? 'Unknown error');
    }
} catch (\Throwable $e) {
    $output[] = "✗ Paystack test error: " . $e->getMessage();
}

echo implode("\n", $output) . "\n\nServer refresh complete. You can now delete this file or visit checkout again.";
