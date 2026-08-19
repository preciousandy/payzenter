<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holdings extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];

    protected $casts = [
        "end_date" => "date"
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    const ASSET_TYPE_FIAT = 'fiat';
    const ASSET_TYPE_METAL = 'metal';


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
