<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReferral extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_CLAIMED = 'claimed';


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
}
