<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    use HasFactory;


    protected $guarded = [];

    public $appends = ['logo'];

    public function getLogoAttribute()
    {
        return asset('/trend.png');
    }
}
