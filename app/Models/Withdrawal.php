<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class Withdrawal extends Model
{
    use HasFactory;
    use HasStatuses;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';

    protected $guarded = [];

    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
