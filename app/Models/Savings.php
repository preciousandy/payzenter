<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CLOSED = 'closed';

    const TYPE_LOCKED = 'locked';
    const TYPE_UNLOCKED = 'unlocked';

    const FREQUENCY_DAILY = 'daily';
    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';

    protected $casts = [
        'end_date' => 'datetime',
        'auto_save_last_deduction' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activities()
    {
        return $this->morphMany(SavingsActivity::class, 'modelable');
    }

    // active savings
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // auto save savings
    public function scopeAutoSave($query)
    {
        return $query->where('if_auto_save', true);
    }

    // closed savings
    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }
}
