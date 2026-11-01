<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'raw_provider_response' => 'array',
        'raw_provider_webhook' => 'array',
    ];

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

