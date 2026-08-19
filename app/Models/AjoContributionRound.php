<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjoContributionRound extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_ON_GOING = 'on-going';
    const STATUS_AWAITING = 'awaiting';
    const STATUS_COMPLETED = 'completed';


    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function donations()
    {
        return $this->hasMany(AjoContributionDonation::class);
    }
}
