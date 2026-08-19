<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FxFormField extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'type',
        'required',
        'form_type',
        'currency_codes'
    ];

    protected $casts = [
        'required' => 'boolean',
        'currency_codes' => 'array'
    ];

    public function scopeForFormType($query, $formType)
    {
        return $query->where('form_type', $formType);
    }

    public function scopeForCurrency($query, $currencyCode)
    {
        return $query->whereJsonContains('currency_codes', $currencyCode);
    }

    public function getAvailableCurrencies()
    {
        return ExchangeRate::distinct()->pluck('code')->toArray();
    }
}
