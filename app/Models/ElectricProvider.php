<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricProvider extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return  $this->logo ? asset('storage/' . $this->logo) : randomImage($this->name);
    }
}
