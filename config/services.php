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

    'anthropic' => [
        'key'         => env('ANTHROPIC_API_KEY'),
        'model'       => env('ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
        'cache_hours' => (int) env('ANTHROPIC_CACHE_HOURS', 6),
    ],

    'kee_it' => [
        'reminder_hours_before'   => (int) env('TASK_REMINDER_HOURS_BEFORE', 24),
        'max_dashboard_urgent'    => (int) env('MAX_DASHBOARD_URGENT_TASKS', 5),
        'procrastination_penalty' => (int) env('PROCRASTINATION_PENALTY_PER_DAY', 5),
    ],

];
