<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyFxOrder extends Model
{
    use HasFactory;

    protected $guarded  = [];

    protected $casts = [
        'data' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CREATED = 'created';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_EXPIRED = 'expired';



    const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CREATED => 'Created',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_EXPIRED => 'Expired',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
