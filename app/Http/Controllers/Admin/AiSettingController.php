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
        $settings = AppSetting::whereIn('key', ['groq_api_key', 'gemini_api_key', 'ai_model', 'ai_vision_model', 'ai_provider', 'chatbot_enabled_roles'])->pluck('value', 'key');

        // Parse chatbot enabled roles (default: all staff enabled, siswa and orang_tua disabled)
        $chatbotEnabledRoles = isset($settings['chatbot_enabled_roles'])
            ? json_decode($settings['chatbot_enabled_roles'], true)
            : [
                'ketua_pkbm' => true,
                'wakil_kepala_sekolah' => true,
                'sekretaris' => true,
                'bendahara' => true,
                'wali_kelas' => true,
                'guru_pengajar' => true,
                'siswa' => false,
                'orang_tua' => false,
            ];

        return view('admin.ai-settings.index', [
            'groqApiKey' => $settings['groq_api_key'] ?? '',
            'geminiApiKey' => $settings['gemini_api_key'] ?? '',
            'model' => $settings['ai_model'] ?? 'llama-3.3-70b-versatile',
            'visionModel' => $settings['ai_vision_model'] ?? 'gemini-2.5-flash',
            'provider' => $settings['ai_provider'] ?? 'groq',
            'chatbotEnabledRoles' => $chatbotEnabledRoles,
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

        // Build chatbot enabled roles array from checkboxes
        $chatbotEnabledRoles = [
            'ketua_pkbm' => $request->has('chatbot_ketua_pkbm'),
            'wakil_kepala_sekolah' => $request->has('chatbot_wakil_kepala_sekolah'),
            'sekretaris' => $request->has('chatbot_sekretaris'),
            'bendahara' => $request->has('chatbot_bendahara'),
            'wali_kelas' => $request->has('chatbot_wali_kelas'),
            'guru_pengajar' => $request->has('chatbot_guru_pengajar'),
            'siswa' => $request->has('chatbot_siswa'),
            'orang_tua' => $request->has('chatbot_orang_tua'),
        ];

        $settings = [
            'groq_api_key' => $request->groq_api_key ?? '',
            'gemini_api_key' => $request->gemini_api_key ?? '',
            'ai_model' => $request->ai_model,
            'ai_vision_model' => $request->ai_vision_model,
            'ai_provider' => $request->ai_provider,
            'chatbot_enabled_roles' => json_encode($chatbotEnabledRoles),
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
                ])->timeout(15)->post('https://api.groq.com/openai/v1/chat/completions', [
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
                // Use v1 API for Gemini 2.0+ models (v1beta doesn't support them)
                $url = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";

                $response = Http::withOptions([
                    'verify' => false,
                ])->withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Test connection. Reply with OK.']
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 10,
                        'temperature' => 0.1
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    // Verify response has content
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return response()->json([
                            'success' => true,
                            'message' => '✅ Koneksi ke Google Gemini API berhasil! Model: ' . $model . ' | Response: ' . substr($data['candidates'][0]['content']['parts'][0]['text'], 0, 50)
                        ]);
                    }
                    return response()->json(['success' => true, 'message' => 'Koneksi berhasil tapi response tidak sesuai format. Model: ' . $model]);
                } else {
                    $errorBody = $response->json();
                    $errorMsg = $errorBody['error']['message'] ?? $response->body();
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Gemini API Error (' . $response->status() . '): ' . $errorMsg
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => '❌ Exception: ' . $e->getMessage()]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Provider tidak didukung untuk test ini.']);
    }
}
