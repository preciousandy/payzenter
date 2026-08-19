<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSavings extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    const TYPE_STRICT = 'strict';
    const TYPE_FLEXIBLE = 'flexible';

    protected $casts = [
        'end_date' => 'datetime',
    ];

    public $appends = ['readable_end_date', 'hash_id'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_savings_members')->withPivot('role', 'status');
    }

    public function activities()
    {
        return $this->morphMany(SavingsActivity::class, 'modelable');
    }

    public function donations()
    {
        return $this->hasMany(GroupSavingsDonation::class);
    }

    public function getReadableEndDateAttribute()
    {
        return $this->end_date->diffForHumans();
    }
}
