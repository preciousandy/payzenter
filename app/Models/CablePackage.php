<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CablePackage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cableProvider()
    {
        return $this->belongsTo(Cable::class, 'provider_code', 'code');
    }
}
