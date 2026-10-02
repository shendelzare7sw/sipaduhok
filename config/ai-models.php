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
| Terakhir diverifikasi langsung ke API: 2 Oktober 2026.
| Perubahan Oktober 2026: Groq memindahkan Llama 3.3 70B & Llama 3.1 8B ke paket
| Enterprise (API key biasa mendapat 404 model_not_found), mematikan
| qwen/qwen3.6-27b dan groq/compound. Model aktif untuk key biasa: gpt-oss-120b,
| gpt-oss-20b, dan qwen/qwen3.8-27b (multimodal).
| Gemini: 2.5-pro & 2.5-flash-lite ditolak untuk key baru; 3.5-flash/3.8-flash
| sering 503 (overload) di endpoint v1, jadi tidak dicantumkan.
|
| Kuota paket gratis Groq kecil (gpt-oss 8.000 token/menit, qwen 7.000 token input/menit),
| sehingga chatbot mengirim knowledge base versi ringkas ke Groq (lihat
| KnowledgeBaseLoader::getRelevantForRole).
|
*/

return [

    /*
    | Model teks default per provider.
    */
    'default_text' => [
        'groq' => 'openai/gpt-oss-120b',
        'gemini' => 'gemini-2.5-flash',
    ],

    /*
    | Model VISION (bisa membaca gambar). Dipakai untuk analisis AI pada
    | jawaban tugas berupa foto/PDF hasil scan, dan lampiran gambar chatbot.
    |
    | Catatan: di Groq, `qwen/qwen3.8-27b` adalah SATU-SATUNYA model yang
    | mendukung input gambar saat ini (maks 20MB & 3 gambar per permintaan,
    | mendukung JSON mode + multi-turn).
    */
    'default_vision' => [
        'groq' => 'qwen/qwen3.8-27b',
        'gemini' => 'gemini-2.5-flash',
    ],

    /*
    | Model yang boleh dipilih guru/admin di menu Pengaturan AI.
    | 'vision' => true berarti model bisa membaca gambar.
    */
    'available' => [
        'groq' => [
            'openai/gpt-oss-120b' => ['label' => 'GPT OSS 120B (Direkomendasikan - Terbaik)', 'vision' => false],
            'openai/gpt-oss-20b' => ['label' => 'GPT OSS 20B (Cepat - Ringan)', 'vision' => false],
            'qwen/qwen3.8-27b' => ['label' => 'Qwen 3.8 27B (Multimodal - Teks + Gambar)', 'vision' => true],
        ],
        'gemini' => [
            'gemini-2.5-flash' => ['label' => 'Gemini 2.5 Flash (Multimodal)', 'vision' => true],
            'gemini-3.5-flash-lite' => ['label' => 'Gemini 3.5 Flash Lite (Cepat - Hemat)', 'vision' => true],
        ],
    ],

    /*
    | Model yang SUDAH DIMATIKAN penyedianya, beserta penggantinya.
    | Dipakai untuk memperbaiki otomatis setting lama yang tersimpan di
    | database, supaya guru tidak perlu mengubah apa pun secara manual.
    */
    'retired' => [
        // Groq - teks (Llama 3.x kini khusus Enterprise sejak Oktober 2026)
        'llama-3.3-70b-versatile' => 'openai/gpt-oss-120b',
        'llama-3.1-8b-instant' => 'openai/gpt-oss-20b',
        'groq/compound' => 'openai/gpt-oss-120b',
        'groq/compound-mini' => 'openai/gpt-oss-20b',
        'qwen/qwen3-32b' => 'openai/gpt-oss-120b',
        'qwen-2.5-32b' => 'openai/gpt-oss-120b',
        'qwen-2.5-coder-32b' => 'openai/gpt-oss-120b',
        'llama3-70b-8192' => 'openai/gpt-oss-120b',
        'llama-3.1-70b-versatile' => 'openai/gpt-oss-120b',
        'llama-3.2-90b-text-preview' => 'openai/gpt-oss-120b',
        'mixtral-8x7b-32768' => 'openai/gpt-oss-120b',
        'gemma2-9b-it' => 'openai/gpt-oss-20b',
        'gemma-7b-it' => 'openai/gpt-oss-20b',

        // Groq - vision
        'qwen/qwen3.6-27b' => 'qwen/qwen3.8-27b',
        'meta-llama/llama-4-scout-17b-16e-instruct' => 'qwen/qwen3.8-27b',
        'meta-llama/llama-4-maverick-17b-128e-instruct' => 'qwen/qwen3.8-27b',
        'llama-3.2-11b-vision-preview' => 'qwen/qwen3.8-27b',
        'llama-3.2-90b-vision-preview' => 'qwen/qwen3.8-27b',

        // Gemini
        'gemini-1.5-flash' => 'gemini-2.5-flash',
        'gemini-1.5-pro' => 'gemini-2.5-flash',
        'gemini-2.0-flash-exp' => 'gemini-2.5-flash',
        // Oktober 2026: masih terdaftar di /models tetapi ditolak "no longer available
        // to new users"; model Pro juga tidak masuk kuota paket gratis (429).
        'gemini-2.5-pro' => 'gemini-2.5-flash',
        'gemini-2.5-flash-lite' => 'gemini-3.5-flash-lite',
    ],

    /*
    | Parameter khusus per model Groq (disisipkan otomatis oleh helper ai_groq_payload()).
    | - gpt-oss: model bernalar; penalaran dikirim di field terpisah, BUKAN di content.
    |   include_reasoning=false agar respons ringkas. Token penalaran tetap
    |   dihitung ke max_tokens, jadi helper menambah anggaran `extra_tokens`.
    | - qwen3.8: dua mode; untuk JSON/penilaian WAJIB reasoning_format hidden/parsed,
    |   reasoning_effort none = mode instruct (cepat, tanpa <think> di content).
    */
    'groq_params' => [
        'openai/gpt-oss-120b' => ['params' => ['reasoning_effort' => 'low', 'include_reasoning' => false], 'extra_tokens' => 1024],
        'openai/gpt-oss-20b' => ['params' => ['reasoning_effort' => 'low', 'include_reasoning' => false], 'extra_tokens' => 1024],
        'qwen/qwen3.8-27b' => ['params' => ['reasoning_effort' => 'none', 'reasoning_format' => 'hidden'], 'extra_tokens' => 0],
    ],

    /*
    | Batas jumlah gambar per permintaan untuk model vision Groq.
    */
    'groq_max_images' => 3,
];
