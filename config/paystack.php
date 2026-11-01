<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paystack Config
    |--------------------------------------------------------------------------
    |
    | Configuration options for Paystack Payment Gateway API.
    |
    */

    'secret_key' => env('PAYSTACK_SECRET_KEY', 'sk_test_d2185697e8fd06af68ba6d93696d9f35d9174e33'),

    'public_key' => env('PAYSTACK_PUBLIC_KEY', 'pk_test_e7ec903d4b7c3cef750ced8c5ac706ba4ff1b2f6'),

    'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),

    'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL'),

];
