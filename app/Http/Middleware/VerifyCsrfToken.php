<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/webhook/flutterwave',
        '/webhook/crypto',
        '/webhook/transfer',
        '/webhook/fund-walllet',
        '/webhook/palmpay',
        '/webhook/paystack',
        '/webhook/udara360',
        '/webhook/udara',
    ];
}
