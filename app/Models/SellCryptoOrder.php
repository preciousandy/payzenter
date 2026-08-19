<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellCryptoOrder extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CREATED = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PROCESSING = 'processing';

    protected $casts = [
        'amount_in_crypto' => 'float'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
