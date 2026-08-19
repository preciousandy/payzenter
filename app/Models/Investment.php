<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];

    protected $casts = [
        'end_date' => 'date'
    ];

    const ASSET_TYPE_CRYPTO = 'crypto';
    const ASSET_TYPE_STOCK = 'stock';


    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
