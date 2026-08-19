<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantKyb extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'pep_declaration' => 'boolean',
        'is_settlement_suspended' => 'boolean',
        'settlement_verified_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

