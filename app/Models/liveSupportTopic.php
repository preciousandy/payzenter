<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class liveSupportTopic extends Model
{
    use HasFactory, HashIdTrait;

    protected $fillable = ['name'];

    public function messages()
    {
        return $this->hasMany(liveSupportMessage::class, 'topic_id');
    }
}
