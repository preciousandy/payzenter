<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
  use HasFactory;

  protected $guarded = [];

  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'resolved_at' => 'datetime',
  ];

  // Status constants
  const STATUS_OPEN = 'open';
  const STATUS_IN_PROGRESS = 'in_progress';
  const STATUS_RESOLVED = 'resolved';
  const STATUS_CLOSED = 'closed';

  // Error source constants
  const SOURCE_AUTHENTICATION = 'authentication';
  const SOURCE_WALLET_TRANSACTION = 'wallet_transaction';
  const SOURCE_CRYPTO_TRADING = 'crypto_trading';
  const SOURCE_BILL_PAYMENT = 'bill_payment';
  const SOURCE_BANK_TRANSFER = 'bank_transfer';
  const SOURCE_INTERNATIONAL_TRANSFER = 'international_transfer';
  const SOURCE_KYC_VERIFICATION = 'kyc_verification';
  const SOURCE_API_INTEGRATION = 'api_integration';
  const SOURCE_DATABASE = 'database';
  const SOURCE_SYSTEM = 'system';
  const SOURCE_PAYMENT_GATEWAY = 'payment_gateway';
  const SOURCE_EBILLS_API = 'ebills_api';
  const SOURCE_PALMPAY_API = 'palmpay_api';
  const SOURCE_DOT_API = 'dot_api';
  const SOURCE_FLUTTER_API = 'flutter_api';
  const SOURCE_QUIDAX_API = 'quidax_api';
  const SOURCE_OTHER = 'other';

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function getFormattedErrorSourceAttribute()
  {
    return ucfirst(str_replace('_', ' ', $this->error_source));
  }

  public function getFormattedStatusAttribute()
  {
    return ucfirst(str_replace('_', ' ', $this->status));
  }

  public static function getErrorSources()
  {
    return [
      self::SOURCE_AUTHENTICATION => 'Authentication',
      self::SOURCE_WALLET_TRANSACTION => 'Wallet Transaction',
      self::SOURCE_CRYPTO_TRADING => 'Crypto Trading',
      self::SOURCE_BILL_PAYMENT => 'Bill Payment',
      self::SOURCE_BANK_TRANSFER => 'Bank Transfer',
      self::SOURCE_INTERNATIONAL_TRANSFER => 'International Transfer',
      self::SOURCE_KYC_VERIFICATION => 'KYC Verification',
      self::SOURCE_API_INTEGRATION => 'API Integration',
      self::SOURCE_DATABASE => 'Database',
      self::SOURCE_SYSTEM => 'System',
      self::SOURCE_PAYMENT_GATEWAY => 'Payment Gateway',
      self::SOURCE_OTHER => 'Other',
      self::SOURCE_EBILLS_API => 'eBills API',
      self::SOURCE_PALMPAY_API => 'Palmpay API',
      self::SOURCE_FLUTTER_API => 'Flutter API',
      self::SOURCE_DOT_API => 'Dot API',
      self::SOURCE_QUIDAX_API => 'Quidax API',
    ];
  }

  public static function getStatuses()
  {
    return [
      self::STATUS_OPEN => 'Open',
      self::STATUS_IN_PROGRESS => 'In Progress',
      self::STATUS_RESOLVED => 'Resolved',
      self::STATUS_CLOSED => 'Closed',
    ];
  }

  /**
   * Log an error to the error logs table
   *
   * @param string $source Error source (use ErrorLog::SOURCE_* constants)
   * @param string $message Error message
   * @param array $options Additional options (user_id, error_details, error_code, severity, etc.)
   * @return ErrorLog
   * 
   * Example usage:
   * ErrorLog::logError(
   *     ErrorLog::SOURCE_WALLET_TRANSACTION,
   *     'Failed to process wallet funding',
   *     [
   *         'error_details' => $exception->getMessage(),
   *         'error_code' => 'WT_001',
   *         'severity' => 'high'
   *     ]
   * );
   */
  public static function logError(string $source, string $message, array $options = [])
  {
    return self::create([
      'user_id' => $options['user_id'] ?? auth()->id(),
      'error_source' => $source,
      'error_message' => $message,
      'error_details' => $options['error_details'] ?? null,
      'error_code' => $options['error_code'] ?? null,
      'status' => $options['status'] ?? self::STATUS_OPEN,
      'severity' => $options['severity'] ?? 'medium',
      'file_path' => $options['file_path'] ?? null,
      'line_number' => $options['line_number'] ?? null,
      'request_url' => $options['request_url'] ?? request()->fullUrl(),
      'user_agent' => $options['user_agent'] ?? request()->userAgent(),
      'ip_address' => $options['ip_address'] ?? request()->ip(),
    ]);
  }
}
