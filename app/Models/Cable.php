<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function packages()
    {
        return $this->hasMany(CablePackage::class, 'provider_code', 'code');
    }
}
