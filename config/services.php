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

    'frontend' => [
        'root' => env('PET_VITRINE_ROOT', 'https://petvitrine.org'),
        'login' => env('PET_VITRINE_LOGIN', 'https://petvitrine.org/login'),
        'reset_password' => env('PET_VITRINE_RESET_PASSWORD', 'https://petvitrine.org/reset-password'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'facebook' => [
        'graph_api_root_url' => env('FB_GRAPH_API_ROOT_URL'),
        'app_id' => env('FB_APP_ID'),
        'app_secret' => env('FB_APP_SECRET'),
        'business_id' => env('FB_BUSINESS_ID'),
        'page_id' => env('FB_PAGE_ID'),
        'access_token' => env('APP_ENV') == "production" ? env('FB_ACCESS_TOKEN') : env('SANDBOX_FB_ACCESS_TOKEN'),
        'ad_account_id' => env('APP_ENV') == "production" ? env('FB_AD_ACCOUNT_ID') : env('SANDBOX_FB_AD_ACCOUNT_ID'),
    ],

    'petfinder' => [
        'api_key' => env('PETFINDER_API_KEY'),
        'api_secret' => env('PETFINDER_API_SECRET')
    ],

    'recaptcha' => [
        'url' => env('RECAPTCHA_URL'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY')
    ],

    'paypal' => [
        'account' => env('APP_ENV') == 'production' ? env('PAYPAL_LIVE_ACCOUNT') : env('PAYPAL_SANDBOX_ACCOUNT'),
        'url' => env('APP_ENV') == 'production' ? env('PAYPAL_LIVE_ROOT_URL') : env('PAYPAL_SANDBOX_ROOT_URL'),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'return_url' => env('PAYPAL_RETURN_URL')
    ]
];
