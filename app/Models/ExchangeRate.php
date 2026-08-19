<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    protected $guarded = [];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('currency.png');
    }

    public function scopeOrderByPriorityCurrencies($query)
    {
        return $query->orderByRaw("CASE 
        WHEN code = 'USD' THEN 1 
        WHEN code = 'EUR' THEN 2 
        WHEN code = 'GBP' THEN 3 
        ELSE 4 
        END")->orderBy('code');
    }

    public function scopeOrderByStatusOpenFirst($query)
    {
        return $query->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END");
    }
    public function scopeOrderBySellStatusOpenFirst($query)
    {
        return $query->orderByRaw("CASE WHEN sell_status = 'open' THEN 0 ELSE 1 END");
    }

    public function banks()
    {
        return $this->belongsToMany(DomiciliaryBank::class, 'exchange_rate_banks', 'exchange_rate_id', 'domiciliary_bank_id');
    }
}
