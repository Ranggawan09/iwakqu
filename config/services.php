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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'mayar' => [
        'api_key' => env('MAYAR_API_KEY', ''),
        'is_production' => env('MAYAR_IS_PRODUCTION', false),
        // Sandbox: https://api.mayar.club/hl/v1
        // Production: https://api.mayar.id/hl/v1
    ],

    'shopeepay' => [
        'client_id' => env('SHOPEEPAY_CLIENT_ID', ''),
        'client_secret' => env('SHOPEEPAY_CLIENT_SECRET', ''),
        'merchant_id' => env('SHOPEEPAY_MERCHANT_ID', ''),
        'external_store_id' => env('SHOPEEPAY_STORE_ID', ''),
        'private_key' => env('SHOPEEPAY_PRIVATE_KEY', ''), // Raw private key string (with headers) OR path to private key PEM
        'shopeepay_public_key' => env('SHOPEEPAY_PUBLIC_KEY', ''), // Raw ShopeePay public key string OR path to PEM file
        'is_production' => env('SHOPEEPAY_IS_PRODUCTION', false),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

];
