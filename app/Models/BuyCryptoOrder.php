<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyCryptoOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CREATED = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PROCESSING = 'processing';

    // In your model
    protected $casts = [
        'amount_in_crypto' => 'float'
    ];

    const ALLOWANCE_AMOUNT = 0; // to be added to amount incase of crypto price fluctuation

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
