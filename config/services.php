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
        'appId' => env('FB_APP_ID'),
        'appSecret' => env('FB_APP_SECRET'),
        'accessToken' => env('APP_ENV') == "production" ? env('FB_ACCESS_TOKEN') : env('SANDBOX_FB_ACCESS_TOKEN'),
        'adAccountId' => env('APP_ENV') == "production" ? env('FB_AD_ACCOUNT_ID') : env('SANDBOX_FB_AD_ACCOUNT_ID'),
        'pageId' => env('FB_PAGE_ID'),
    ]
];
