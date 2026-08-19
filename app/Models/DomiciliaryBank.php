<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomiciliaryBank extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function currencies()
    {
        return $this->belongsToMany(ExchangeRate::class, 'exchange_rate_banks', 'domiciliary_bank_id', 'exchange_rate_id');
    }
}
