<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory, HashIdTrait;

    protected $guarded = [];
    protected $casts = [
        'response_data' => 'array',
        'last_status_check_at' => 'datetime',
    ];

    public $appends = ['hash_id'];


    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    const DOT_TRANSFER_STATUS_RUNNING = 'RUNNING';
    const DOT_TRANSFER_STATUS_SUCCEEDED = 'SUCCEEDED';
    const DOT_TRANSFER_STATUS_FAILED = 'FAILED';

    const TYPE_BANK = 'bank';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFailureReason(): ?string
    {
        if ($this->status !== self::STATUS_FAILED) {
            return null;
        }

        $resData = $this->response_data;
        if (!$resData || !is_array($resData)) {
            return null;
        }

        // 1. Check status_check (Flutterwave/PalmPay status query)
        if (isset($resData['status_check'])) {
            $sc = $resData['status_check'];
            // Flutterwave check details
            if (isset($sc['raw']['data'])) {
                $flwData = $sc['raw']['data'];
                if (is_array($flwData) && array_key_exists(0, $flwData)) {
                    $flwData = $flwData[0];
                }
                if (isset($flwData['complete_message']) && trim((string)$flwData['complete_message']) !== '') {
                    return $flwData['complete_message'];
                }
            }
            // PalmPay check details
            if (isset($sc['raw']['data']['errorMsg']) && trim((string)$sc['raw']['data']['errorMsg']) !== '') {
                return $sc['raw']['data']['errorMsg'];
            }
        }

        // 2. Check initial response (raw)
        if (isset($resData['raw'])) {
            $raw = $resData['raw'];
            // Flutterwave raw details
            if (isset($raw['data']['complete_message']) && trim((string)$raw['data']['complete_message']) !== '') {
                return $raw['data']['complete_message'];
            }
            if (isset($raw['message']) && trim((string)$raw['message']) !== '') {
                return $raw['message'];
            }
            // PalmPay raw details
            if (isset($raw['respMsg']) && trim((string)$raw['respMsg']) !== '') {
                return $raw['respMsg'];
            }
        }

        // 3. Check webhook payload
        if (isset($resData['webhook'])) {
            $wh = $resData['webhook'];
            if (isset($wh['data']['complete_message']) && trim((string)$wh['data']['complete_message']) !== '') {
                return $wh['data']['complete_message'];
            }
            if (isset($wh['complete_message']) && trim((string)$wh['complete_message']) !== '') {
                return $wh['complete_message'];
            }
            if (isset($wh['respMsg']) && trim((string)$wh['respMsg']) !== '') {
                return $wh['respMsg'];
            }
        }

        return 'Transfer failed. Please check with your bank or contact support.';
    }
}
