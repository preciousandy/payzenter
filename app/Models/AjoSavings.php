<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjoSavings extends Model
{
    use HasFactory, HashIdTrait;

    protected $guarded = [];

    protected $casts = [
        'end_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    const TYPE_DAILY = 'Daily';
    const TYPE_WEEKLY = 'Weekly';
    const TYPE_MONTHLY = 'Monthly';
    const TYPE_YEARLY = 'Yearly';

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    public $appends = ['days_left', 'duration', 'pay_back_amount'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', Self::STATUS_ACTIVE);
    }


    public function scopeClosed($query)
    {
        return $query->where('status', Self::STATUS_CLOSED);
    }

    public function getDaysLeftAttribute()
    {
        return now()->diffInDays($this->end_date);
    }

    public function getDurationAttribute()
    {
        return $this->created_at->diffInDays($this->end_date);
    }

    public function transactions()
    {
        return $this->hasMany(AjoSavingsTransaction::class);
    }

    public function getPayBackAmountAttribute()
    {
        return $this->transactions()->where('status', UserTransaction::STATUS_FAILED)->sum('amount');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
}
