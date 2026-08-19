<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetalRate extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $fillable = ['metal', 'price'];
}
