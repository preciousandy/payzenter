<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Configure Google Analytics 4 (GA4) measurement_id
    'ga4' => [
        'measurementId' => env('GA_MEASUREMENT_ID'),
    ],

    'udara360' => [
        'base_url' => env('UDARA360_BASE_URL', 'https://openapi.udara360.io'),
        'client_id' => env('UDARA360_CLIENT_ID', ''),
        'client_secret' => env('UDARA360_CLIENT_SECRET', ''),
        'parent_account' => env('UDARA360_PARENT_ACCOUNT', ''),
        'product_code' => env('UDARA360_PRODUCT_CODE', '301'),
        'bank_name' => env('UDARA360_BANK_NAME', 'Providus Bank'),
        'bank_code' => env('UDARA360_BANK_CODE', '000023'),
        'webhook_secret' => env('UDARA360_WEBHOOK_SECRET', ''),
    ],
];
