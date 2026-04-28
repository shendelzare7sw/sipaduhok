<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$key = \App\Models\AppSetting::where('key', 'groq_api_key')->value('value');
$response = Illuminate\Support\Facades\Http::withOptions(['verify' => false])
    ->withHeaders(['Authorization' => 'Bearer ' . $key])
    ->post('https://api.groq.com/openai/v1/chat/completions', [
        'model' => 'qwen/qwen3-32b',
        'messages' => [['role' => 'user', 'content' => 'hello']],
        'max_tokens' => 1500
    ]);
print_r($response->body());
