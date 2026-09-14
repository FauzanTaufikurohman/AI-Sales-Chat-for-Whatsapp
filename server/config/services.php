<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    'fonnte' => [
        'account_token' => env('FONNTE_ACCOUNT_TOKEN'),
        'webhook_url' => env(
            'FONNTE_WEBHOOK_URL',
            rtrim(env('APP_URL', 'http://localhost'), '/').'/api/webhooks/whatsapp'
        ),
        'webhook_secret' => env('FONNTE_WEBHOOK_SECRET'),
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'required_information' => array_values(array_filter(array_map(
            'trim',
            explode(',', env('AI_REQUIRED_INFORMATION', 'nama,kebutuhan,budget,lokasi,waktu_penggunaan'))
        ))),
    ],
];
