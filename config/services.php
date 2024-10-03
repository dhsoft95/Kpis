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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'google_play' => [
        'api_token' => env('GOOGLE_PLAY_API_TOKEN'),
    ],

    'google_analytics' => [
        'property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'currency_api' => [
        'api_key' => env('CURRENCY_API_KEY'),
        'base_currency' => env('CURRENCY_API_BASE_CURRENCY', 'TZS'),
        'usd_determinant' => env('CURRENCY_API_USD_DETERMINANT', 0.04),
    ],

    'zendesk' => [
        'subdomain' => env('ZENDESK_SUBDOMAIN'),
        'username' => env('ZENDESK_USERNAME'),
        'token' => env('ZENDESK_TOKEN'),
        'fetch_minutes' => env('ZENDESK_FETCH_MINUTES', 2),
    ],
//    'zendesk' => [
//        'subdomain' => env('ZENDESK_SUBDOMAIN', 'simbamoneylimited'),
//        'username' => env('ZENDESK_USERNAME', 'your_username'),
//        'token' => env('ZENDESK_API_TOKEN', 'your_api_token'),
//    ],


];
