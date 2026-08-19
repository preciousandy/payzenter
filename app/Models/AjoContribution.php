<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AjoContribution extends Model
{
    use HasFactory, HashIdTrait;

    const TYPE_PERSONAL = 'personal';
    const TYPE_BUSINESS = 'business';

    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    const FREQUENCY_WEEKLY = 'weekly';
    const FREQUENCY_MONTHLY = 'monthly';
    const FREQUENCY_YEARLY = 'yearly';



    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'next_round_date' => 'date',
    ];

    public $appends = ['cover_photo_url', 'hash_id'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'ajo_contribution_members', 'ajo_contribution_id', 'user_id')->withPivot('is_contributing', 'role', 'order_number', 'if_auto_save', 'role', 'created_at');
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(AjoContributionRound::class);
    }

    public function activeRound()
    {
        return $this->rounds()->where('order', $this->current_round)->first();
    }

    public function getCoverPhotoUrlAttribute()
    {
        return $this?->cover_image ?  asset('/storage/' . $this->cover_image) : 'https://ui-avatars.com/api/?background=random&name=' . $this->name . '&rounded=true&bold=true';
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getActiveRoundDefaulters()
    {
        // Get the active round
        $activeRound = $this->activeRound();

        if (!$activeRound) {
            return collect(); // Return empty collection if no active round
        }

        // Get all members of the contribution
        $allMembers = $this->members()->where('is_contributing', true)->get();

        // Filter out members who have donated in the active round
        $defaulters = $allMembers->filter(function ($member) use ($activeRound) {
            // Check if the member has no donations for this active round
            $hasDonated = $activeRound->donations()->where('user_id', $member->id)->exists();
            return !$hasDonated;
        });

        return $defaulters;
    }
}
