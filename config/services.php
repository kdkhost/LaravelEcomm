<?php

declare(strict_types=1);

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

    'replicate' => [
        'token' => env('REPLICATE_API_TOKEN'),
        'model' => env('REPLICATE_VIRTUAL_TRY_ON_MODEL', 'black-forest-labs/flux-kontext-pro'),
        'base_url' => env('REPLICATE_BASE_URL', 'https://api.replicate.com/v1'),
        'timeout' => (int) env('REPLICATE_TIMEOUT', 120),
        'wait_seconds' => (int) env('REPLICATE_WAIT_SECONDS', 60),
        'poll_seconds' => (int) env('REPLICATE_POLL_SECONDS', 3),
        'max_poll_seconds' => (int) env('REPLICATE_MAX_POLL_SECONDS', 90),
        'max_input_mb' => (int) env('REPLICATE_MAX_INPUT_MB', 8),
        'output_aspect_ratio' => env('REPLICATE_OUTPUT_ASPECT_RATIO', '3:4'),
        'output_format' => env('REPLICATE_OUTPUT_FORMAT', 'jpg'),
        'safety_tolerance' => (int) env('REPLICATE_SAFETY_TOLERANCE', 2),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
