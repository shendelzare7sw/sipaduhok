<?php

return [

    'paywuz' => [
        'base_url' => env('PAYWUZ_BASE_URL', 'https://api.paywuz.id/v1'),
        'checkout_url' => env('PAYWUZ_CHECKOUT_URL', 'https://paywuz.id/pay'),
        'sandbox_api_key' => env('PAYWUZ_SANDBOX_API_KEY'),
        'production_api_key' => env('PAYWUZ_PRODUCTION_API_KEY'),
        'expiry_minutes' => (int) env('PAYWUZ_EXPIRY_MINUTES', 720),
        'webhook_tolerance_seconds' => (int) env('PAYWUZ_WEBHOOK_TOLERANCE_SECONDS', 900),
    ],

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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'turnstile' => [
        'site_key' => env('TURNSTILE_SITE_KEY'),
        'secret_key' => env('TURNSTILE_SECRET_KEY'),
    ],

    'pdftotext' => [
        // spatie/pdf-to-text hanya mengenali beberapa path umum Linux/macOS
        // (+ satu path Xpdf khusus Windows). Kalau pdftotext (poppler) terpasang
        // di lokasi lain (mis. Windows/Laragon via Git for Windows), isi path
        // binary-nya lewat PDFTOTEXT_BIN_PATH di .env. Kosongkan untuk pakai
        // auto-detect bawaan paket (biasanya cukup di Linux/VPS).
        'bin_path' => env('PDFTOTEXT_BIN_PATH'),
    ],

];
