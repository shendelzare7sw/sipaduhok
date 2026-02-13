<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;

class AiSettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::whereIn('key', ['ai_api_key', 'ai_model', 'ai_vision_model', 'ai_provider'])->pluck('value', 'key');
        
        return view('admin.ai-settings.index', [
            'apiKey' => $settings['ai_api_key'] ?? '',
            'model' => $settings['ai_model'] ?? 'llama3-70b-8192',
            'visionModel' => $settings['ai_vision_model'] ?? 'meta-llama/llama-4-scout-17b-16e-instruct',
            'provider' => $settings['ai_provider'] ?? 'groq',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'ai_api_key' => 'required|string',
            'ai_model' => 'required|string',
            'ai_vision_model' => 'required|string',
            'ai_provider' => 'required|string|in:groq,openai,gemini',
        ]);

        $settings = [
            'ai_api_key' => $request->ai_api_key,
            'ai_model' => $request->ai_model,
            'ai_vision_model' => $request->ai_vision_model,
            'ai_provider' => $request->ai_provider,
        ];

        foreach ($settings as $key => $value) {
            AppSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan AI berhasil disimpan.');
    }

    public function testConnection(Request $request) 
    {
        $apiKey = $request->input('api_key');
        $model = $request->input('model');
        $provider = $request->input('provider');

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API Key wajib diisi.']);
        }

        if ($provider === 'groq') {
            try {
                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Test connection. Reply with "OK".']
                    ],
                    'max_tokens' => 5
                ]);

                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Koneksi ke Groq API berhasil!']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Gagal: ' . $response->body()]);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Provider tidak didukung untuk test ini.']);
    }
}
