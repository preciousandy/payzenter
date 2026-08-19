<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirtimeDataHistory extends Model
{
    use HasFactory;

    protected $guarded = [];

    const TYPE_AIRTIME = 'airtime';
    const TYPE_DATA = 'data';
}
