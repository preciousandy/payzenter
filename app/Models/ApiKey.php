<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
  use HasFactory;

  protected $guarded = [];

  // API Key Constants
  const FLUTTERWAVE_SECRET_KEY = 'flutterwave_secret_key';
  const QUIDAX_SECRET_API_KEY = 'quidax_secret_api_key';
  const RAPID_API_KEY = 'rapid_api_key';

  /**
   * Get all available API key constants
   */
  public static function getApiKeyConstants()
  {
    return [
      self::FLUTTERWAVE_SECRET_KEY => 'Flutterwave Secret Key',
      self::QUIDAX_SECRET_API_KEY => 'Quidax Secret API Key',
      self::RAPID_API_KEY => 'Rapid API Key',
    ];
  }

  /**
   * Get API key by name
   */
  public static function getApiKey($name)
  {
    $apiKey = self::where('name', $name)->first();
    return $apiKey ? $apiKey->value : null;
  }

  /**
   * Set API key value
   */
  public static function setApiKey($name, $value)
  {
    return self::updateOrCreate(
      ['name' => $name],
      ['value' => $value]
    );
  }
}
