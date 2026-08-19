<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForeignExchangeSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'form_fields' => 'array',
        'sell_fx_form_fields' => 'array'
    ];
}
