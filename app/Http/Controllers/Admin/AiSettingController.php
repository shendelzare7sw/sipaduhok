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
        $settings = AppSetting::whereIn('key', ['groq_api_key', 'gemini_api_key', 'ai_model', 'ai_vision_model', 'ai_provider'])->pluck('value', 'key');

        return view('admin.ai-settings.index', [
            'groqApiKey' => $settings['groq_api_key'] ?? '',
            'geminiApiKey' => $settings['gemini_api_key'] ?? '',
            'model' => $settings['ai_model'] ?? 'llama-3.3-70b-versatile',
            'visionModel' => $settings['ai_vision_model'] ?? 'meta-llama/llama-4-scout-17b-16e-instruct',
            'provider' => $settings['ai_provider'] ?? 'groq',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'groq_api_key' => 'nullable|string',
            'gemini_api_key' => 'nullable|string',
            'ai_model' => 'required|string',
            'ai_vision_model' => 'required|string',
            'ai_provider' => 'required|string|in:groq,gemini',
        ]);

        // Validasi: Pastikan API key sesuai provider yang dipilih terisi
        if ($request->ai_provider === 'groq' && empty($request->groq_api_key)) {
            return redirect()->back()->withErrors(['groq_api_key' => 'Groq API Key wajib diisi untuk provider Groq Cloud.']);
        }

        if ($request->ai_provider === 'gemini' && empty($request->gemini_api_key)) {
            return redirect()->back()->withErrors(['gemini_api_key' => 'Gemini API Key wajib diisi untuk provider Google Gemini.']);
        }

        $settings = [
            'groq_api_key' => $request->groq_api_key ?? '',
            'gemini_api_key' => $request->gemini_api_key ?? '',
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
                    return response()->json(['success' => true, 'message' => 'Koneksi ke Groq API berhasil! Model: ' . $model]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Gagal terhubung ke Groq API. Periksa API Key atau model yang dipilih.']);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        if ($provider === 'gemini') {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Test connection. Reply with OK.']
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 5
                    ]
                ]);

                if ($response->successful()) {
                    return response()->json(['success' => true, 'message' => 'Koneksi ke Google Gemini API berhasil! Model: ' . $model]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Gagal terhubung ke Gemini API. Periksa API Key atau model yang dipilih.']);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Provider tidak didukung untuk test ini.']);
    }
}
