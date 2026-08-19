<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_SEEN = 'seen';


    const CATEGORY_COMPLAINT = 'complaint';
    const CATEGORY_ENQUIRY = 'enquiry';
    const CATEGORY_SUGGESTION = 'suggestion';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
