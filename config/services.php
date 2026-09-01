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

    // ─── FMCSMS Gateway (fortmed.org) ────────────────────────────────
    'fmcsms' => [
        'api_key'     => env('FMCSMS_API_KEY'),
        'api_url'     => env('FMCSMS_API_URL', 'https://fortmed.org/web/FMCSMS/api/messages.php'),
        'sender_name' => env('FMCSMS_SENDER_NAME', 'REPROCARE'),
        'from_number' => env('FMCSMS_FROM_NUMBER'),
        'mock'        => env('FMCSMS_MOCK', false),
    ],

    // ─── Google Gemini AI ─────────────────────────────────────────────
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model'   => env('GEMINI_MODEL', 'gemini-1.5-flash'),
        'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models',
    ],

];
