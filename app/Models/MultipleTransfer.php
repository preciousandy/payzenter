<?php

namespace App\Models;

use App\Traits\HashIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultipleTransfer extends Model
{
    use HasFactory;
    use HashIdTrait;

    protected $guarded = [];

    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';

    const TYPE_SWIFTPAY = 'SwiftPay';
    const TYPE_BANK = 'bank';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
