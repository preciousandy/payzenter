<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    use HasFactory, HashIdTrait;


    protected $guarded = [];

    const TYPE_SWIFTPAY = 'swiftpay';
    const TYPE_BANK = 'bank';


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
