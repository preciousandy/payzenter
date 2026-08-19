<?php

namespace Database\Seeders;

use App\Models\ErrorLog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ErrorLogSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get some users for association
    $users = User::limit(10)->get();

    $errorLogs = [
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_WALLET_TRANSACTION,
        'error_message' => 'Failed to process wallet funding transaction - Insufficient funds in source account',
        'error_details' => 'Transaction failed during processing phase. User attempted to fund wallet with ₦50,000 but source account balance was ₦15,000.',
        'error_code' => 'WT_001',
        'status' => ErrorLog::STATUS_OPEN,
        'severity' => 'high',
        'file_path' => 'app/Services/WalletService.php',
        'line_number' => 142,
        'request_url' => '/wallet/fund',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'ip_address' => '192.168.1.100',
        'created_at' => now()->subDays(3),
      ],
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_CRYPTO_TRADING,
        'error_message' => 'Quidax API connection timeout during crypto purchase',
        'error_details' => 'API request to Quidax timed out after 30 seconds. User was attempting to purchase 0.01 BTC.',
        'error_code' => 'CT_005',
        'status' => ErrorLog::STATUS_RESOLVED,
        'severity' => 'medium',
        'admin_notes' => 'Resolved by switching to backup API endpoint. Quidax confirmed temporary connectivity issues.',
        'resolved_by' => 'Admin User',
        'resolved_at' => now()->subDays(2),
        'file_path' => 'app/Services/CryptoService.php',
        'line_number' => 89,
        'request_url' => '/crypto/buy',
        'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X)',
        'ip_address' => '192.168.1.101',
        'created_at' => now()->subDays(5),
      ],
      [
        'user_id' => null, // System error
        'error_source' => ErrorLog::SOURCE_DATABASE,
        'error_message' => 'Database connection lost during transaction processing',
        'error_details' => 'Connection to primary database server lost. Error: SQLSTATE[HY000] [2002] Connection refused',
        'error_code' => 'DB_002',
        'status' => ErrorLog::STATUS_CLOSED,
        'severity' => 'critical',
        'admin_notes' => 'Database server was restarted. Issue resolved after implementing connection retry logic.',
        'resolved_by' => 'System Administrator',
        'resolved_at' => now()->subDays(1),
        'file_path' => 'vendor/laravel/framework/src/Illuminate/Database/Connectors/Connector.php',
        'line_number' => 70,
        'request_url' => '/api/transactions',
        'user_agent' => 'FitPay-Mobile/1.2.0',
        'ip_address' => '10.0.0.1',
        'created_at' => now()->subDays(7),
      ],
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_BILL_PAYMENT,
        'error_message' => 'E-Bills API returned invalid response format',
        'error_details' => 'Expected JSON response but received HTML error page. Response: <!DOCTYPE html><html><head><title>503 Service Temporarily Unavailable</title>',
        'error_code' => 'BP_003',
        'status' => ErrorLog::STATUS_IN_PROGRESS,
        'severity' => 'high',
        'admin_notes' => 'Contacted E-Bills support team. Investigating API endpoint issues.',
        'file_path' => 'app/Services/BillsService.php',
        'line_number' => 156,
        'request_url' => '/bills/electricity',
        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
        'ip_address' => '192.168.1.102',
        'created_at' => now()->subDays(1),
      ],
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_KYC_VERIFICATION,
        'error_message' => 'Face verification service returned error - Image quality too low',
        'error_details' => 'Face recognition API rejected uploaded image. Response: {"error": "QUALITY_TOO_LOW", "confidence": 0.23, "min_required": 0.75}',
        'error_code' => 'KYC_004',
        'status' => ErrorLog::STATUS_OPEN,
        'severity' => 'low',
        'file_path' => 'app/Http/Controllers/Api/KycController.php',
        'line_number' => 112,
        'request_url' => '/api/kyc/submit-step3',
        'user_agent' => 'FitPay-Mobile/1.2.0',
        'ip_address' => '192.168.1.103',
        'created_at' => now()->subHours(12),
      ],
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_BANK_TRANSFER,
        'error_message' => 'DOT MFB virtual account creation failed',
        'error_details' => 'Failed to create virtual account for user. DOT API response: {"status": "error", "message": "Daily limit exceeded"}',
        'error_code' => 'BT_007',
        'status' => ErrorLog::STATUS_OPEN,
        'severity' => 'medium',
        'file_path' => 'app/Services/UserService.php',
        'line_number' => 95,
        'request_url' => '/admin/kyc-verification',
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'ip_address' => '192.168.1.104',
        'created_at' => now()->subHours(6),
      ],
      [
        'user_id' => null,
        'error_source' => ErrorLog::SOURCE_AUTHENTICATION,
        'error_message' => 'Multiple failed login attempts detected - Potential brute force attack',
        'error_details' => 'IP address 192.168.1.200 attempted 50 failed logins in 5 minutes for user: john@example.com',
        'error_code' => 'AUTH_001',
        'status' => ErrorLog::STATUS_RESOLVED,
        'severity' => 'critical',
        'admin_notes' => 'IP address has been blocked. User account temporarily suspended for security.',
        'resolved_by' => 'Security Team',
        'resolved_at' => now()->subHours(4),
        'file_path' => 'app/Http/Controllers/Auth/AuthenticatedSessionController.php',
        'line_number' => 45,
        'request_url' => '/login',
        'user_agent' => 'curl/7.68.0',
        'ip_address' => '192.168.1.200',
        'created_at' => now()->subHours(8),
      ],
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_PAYMENT_GATEWAY,
        'error_message' => 'Flutterwave webhook signature verification failed',
        'error_details' => 'Webhook payload signature does not match expected signature. Potential security issue.',
        'error_code' => 'PG_002',
        'status' => ErrorLog::STATUS_OPEN,
        'severity' => 'high',
        'file_path' => 'app/Http/Controllers/WebhookController.php',
        'line_number' => 28,
        'request_url' => '/webhook/flutterwave',
        'user_agent' => 'Flutterwave-Webhook/1.0',
        'ip_address' => '52.49.173.169',
        'created_at' => now()->subMinutes(30),
      ],
      [
        'user_id' => $users->random()->id,
        'error_source' => ErrorLog::SOURCE_INTERNATIONAL_TRANSFER,
        'error_message' => 'Currency exchange rate not found for USD to EUR',
        'error_details' => 'Exchange rate API returned empty response for USD/EUR pair. User transfer of $500 failed.',
        'error_code' => 'IT_001',
        'status' => ErrorLog::STATUS_IN_PROGRESS,
        'severity' => 'medium',
        'admin_notes' => 'Manually added exchange rate. Investigating API provider issues.',
        'file_path' => 'app/Services/TransferService.php',
        'line_number' => 203,
        'request_url' => '/transfer/international',
        'user_agent' => 'Mozilla/5.0 (Linux; Android 11; SM-G991B)',
        'ip_address' => '192.168.1.105',
        'created_at' => now()->subMinutes(45),
      ],
      [
        'user_id' => null,
        'error_source' => ErrorLog::SOURCE_SYSTEM,
        'error_message' => 'Background job queue processing failed',
        'error_details' => 'Queue worker stopped unexpectedly. Last processed job: SendTransactionNotification. Memory usage: 512MB',
        'error_code' => 'SYS_003',
        'status' => ErrorLog::STATUS_RESOLVED,
        'severity' => 'medium',
        'admin_notes' => 'Queue worker restarted. Added memory limit monitoring.',
        'resolved_by' => 'DevOps Team',
        'resolved_at' => now()->subMinutes(15),
        'file_path' => 'artisan',
        'line_number' => 1,
        'request_url' => null,
        'user_agent' => null,
        'ip_address' => '127.0.0.1',
        'created_at' => now()->subMinutes(60),
      ],
    ];

    foreach ($errorLogs as $errorLogData) {
      ErrorLog::create($errorLogData);
    }
  }
}
