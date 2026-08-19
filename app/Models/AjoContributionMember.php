<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjoContributionMember extends Model
{
    use HasFactory;

    protected $casts = [
        'is_contributing' => 'boolean',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_MEMBER = 'member';

    protected $guarded = [];
}
