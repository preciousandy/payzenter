<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['code', 'content'];

    const TERMS_AND_CONDITIONS = 'terms-and-conditions';
    const PRIVACY_POLICY = 'privacy-policy';
    const ABOUT_US = 'about-us';
}
