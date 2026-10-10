<?php

return [

    'midtrans' => [
        'sandbox_server_key' => env('MIDTRANS_SANDBOX_SERVER_KEY'),
        'production_server_key' => env('MIDTRANS_PRODUCTION_SERVER_KEY'),
        'snap_url' => [
            'sandbox' => env('MIDTRANS_SANDBOX_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/v1'),
            'production' => env('MIDTRANS_PRODUCTION_SNAP_URL', 'https://app.midtrans.com/snap/v1'),
        ],
        'api_url' => [
            'sandbox' => env('MIDTRANS_SANDBOX_API_URL', 'https://api.sandbox.midtrans.com/v2'),
            'production' => env('MIDTRANS_PRODUCTION_API_URL', 'https://api.midtrans.com/v2'),
        ],
        'expiry_minutes' => (int) env('MIDTRANS_EXPIRY_MINUTES', 720),
        // Kode kanal yang ditawarkan kepada wali siswa. Setiap kanal juga harus
        // aktif di dashboard Midtrans agar Snap dapat menampilkannya.
        'channels' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('MIDTRANS_CHANNELS', 'QRIS,GOPAY,SHOPEEPAY,VA')),
        ))),
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
