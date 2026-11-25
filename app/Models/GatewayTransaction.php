<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayTransaction extends Model
{
    use HasFactory;

    public const STATUS_SUCCESS = 'success';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_FAILED = 'failed';
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PENDING = 'pending';

    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'raw_provider_response' => 'array',
        'raw_provider_webhook' => 'array',
    ];

    public function getDisplayStatusAttribute(): string
    {
        $status = strtolower((string) $this->status);
        if (in_array($status, ['paying', 'processing'])) {
            return 'Processing';
        }
        if (in_array($status, ['success', 'successful'])) {
            return 'Success';
        }
        if (in_array($status, ['failed', 'fail', 'closed', 'cancelled', 'canceled'])) {
            return 'Failed';
        }
        return ucfirst($status ?: 'Processing');
    }

    public function isSuccessful(): bool
    {
        return in_array(strtolower((string) $this->status), ['success', 'successful'], true);
    }

    public function isProcessing(): bool
    {
        return in_array(strtolower((string) $this->status), ['processing', 'paying', 'pending', 'unpaid'], true);
    }

    public function isFailed(): bool
    {
        return in_array(strtolower((string) $this->status), ['failed', 'closed', 'cancelled', 'canceled'], true);
    }

    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function webhookDeliveries()
    {
        return $this->hasMany(GatewayWebhookDelivery::class, 'gateway_transaction_id');
    }

    public function latestWebhookDelivery()
    {
        return $this->hasOne(GatewayWebhookDelivery::class, 'gateway_transaction_id')->latestOfMany();
    }
}

