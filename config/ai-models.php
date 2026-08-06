<?php

/*
|--------------------------------------------------------------------------
| Daftar Model AI — SUMBER TUNGGAL
|--------------------------------------------------------------------------
|
| Groq & Google rutin mematikan (decommission) model lama. Kalau nama model
| masih tersebar hardcode di banyak service/blade/JS, satu model mati bikin
| beberapa fitur ikut mati diam-diam dan sulit dilacak — persis yang terjadi
| pada `meta-llama/llama-4-scout-17b-16e-instruct` (analisis gambar/PDF tugas)
| dan `qwen/qwen3-32b` (pilihan di menu Pengaturan AI).
|
| Semua nama model sekarang dibaca dari sini.
|
| Cara memverifikasi ulang daftar ini kapan pun (butuh API key tersimpan):
|   Groq   : GET https://api.groq.com/openai/v1/models
|   Gemini : GET https://generativelanguage.googleapis.com/v1beta/models
|
| Terakhir diverifikasi langsung ke API: 6 Agustus 2026.
|
*/

return [

    /*
    | Model teks default per provider.
    */
    'default_text' => [
        'groq' => 'llama-3.3-70b-versatile',
        'gemini' => 'gemini-2.5-flash',
    ],

    /*
    | Model VISION (bisa membaca gambar). Dipakai untuk analisis AI pada
    | jawaban tugas berupa foto/PDF hasil scan, dan lampiran gambar chatbot.
    |
    | Catatan: di Groq, `qwen/qwen3.6-27b` adalah SATU-SATUNYA model yang
    | mendukung input gambar saat ini (maks 20MB & 5 gambar per permintaan,
    | mendukung JSON mode + multi-turn). Jadi ya — Qwen mendukung multimodal.
    */
    'default_vision' => [
        'groq' => 'qwen/qwen3.6-27b',
        'gemini' => 'gemini-2.5-flash',
    ],

    /*
    | Model yang boleh dipilih guru/admin di menu Pengaturan AI.
    | 'vision' => true berarti model bisa membaca gambar.
    */
    'available' => [
        'groq' => [
            'llama-3.3-70b-versatile' => ['label' => 'Llama 3.3 70B (Direkomendasikan - Terbaik)', 'vision' => false],
            'llama-3.1-8b-instant' => ['label' => 'Llama 3.1 8B (Tercepat - Ringan)', 'vision' => false],
            'openai/gpt-oss-120b' => ['label' => 'GPT OSS 120B (Model Berat)', 'vision' => false],
            'openai/gpt-oss-20b' => ['label' => 'GPT OSS 20B (Cepat)', 'vision' => false],
            'qwen/qwen3.6-27b' => ['label' => 'Qwen 3.6 27B (Multimodal - Teks + Gambar)', 'vision' => true],
            'groq/compound' => ['label' => 'Groq Compound (Dengan Web Search)', 'vision' => false],
        ],
        'gemini' => [
            'gemini-2.5-flash' => ['label' => 'Gemini 2.5 Flash (Multimodal)', 'vision' => true],
            'gemini-2.5-pro' => ['label' => 'Gemini 2.5 Pro (Paling Akurat)', 'vision' => true],
            'gemini-2.5-flash-lite' => ['label' => 'Gemini 2.5 Flash Lite (Hemat)', 'vision' => true],
        ],
    ],

    /*
    | Model yang SUDAH DIMATIKAN penyedianya, beserta penggantinya.
    | Dipakai untuk memperbaiki otomatis setting lama yang tersimpan di
    | database, supaya guru tidak perlu mengubah apa pun secara manual.
    */
    'retired' => [
        // Groq - teks
        'qwen/qwen3-32b' => 'llama-3.3-70b-versatile',
        'qwen-2.5-32b' => 'llama-3.3-70b-versatile',
        'qwen-2.5-coder-32b' => 'llama-3.3-70b-versatile',
        'llama3-70b-8192' => 'llama-3.3-70b-versatile',
        'llama-3.1-70b-versatile' => 'llama-3.3-70b-versatile',
        'llama-3.2-90b-text-preview' => 'llama-3.3-70b-versatile',
        'mixtral-8x7b-32768' => 'llama-3.3-70b-versatile',
        'gemma2-9b-it' => 'llama-3.3-70b-versatile',
        'gemma-7b-it' => 'llama-3.3-70b-versatile',

        // Groq - vision
        'meta-llama/llama-4-scout-17b-16e-instruct' => 'qwen/qwen3.6-27b',
        'meta-llama/llama-4-maverick-17b-128e-instruct' => 'qwen/qwen3.6-27b',
        'llama-3.2-11b-vision-preview' => 'qwen/qwen3.6-27b',
        'llama-3.2-90b-vision-preview' => 'qwen/qwen3.6-27b',

        // Gemini
        'gemini-1.5-flash' => 'gemini-2.5-flash',
        'gemini-1.5-pro' => 'gemini-2.5-pro',
        'gemini-2.0-flash-exp' => 'gemini-2.5-flash',
    ],
];
