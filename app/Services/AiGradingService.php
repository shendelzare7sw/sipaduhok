<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AppSetting;

class AiGradingService
{
    protected $apiKey;
    protected $model;
    protected $visionModel;
    protected $provider;

    public function __construct()
    {
        $this->loadConfig();
    }

    protected function loadConfig()
    {
        $settings = AppSetting::whereIn('key', ['groq_api_key', 'gemini_api_key', 'ai_model', 'ai_vision_model', 'ai_provider'])->pluck('value', 'key');

        $this->provider = $settings['ai_provider'] ?? 'groq';

        // Load API key sesuai provider yang aktif
        if ($this->provider === 'groq') {
            $this->apiKey = $settings['groq_api_key'] ?? null;
        } elseif ($this->provider === 'gemini') {
            $this->apiKey = $settings['gemini_api_key'] ?? null;
        }

        $this->model = $settings['ai_model'] ?? 'llama-3.3-70b-versatile';

        // Auto-fix for decommissioned models
        if (in_array($this->model, ['llama3-70b-8192', 'llama-3.2-90b-text-preview'])) {
            $this->model = 'llama-3.3-70b-versatile';
        }

        // Default to Llama 4 Scout (Vision capable)
        $this->visionModel = $settings['ai_vision_model'] ?? 'meta-llama/llama-4-scout-17b-16e-instruct';

        // Auto-fix for decommissioned vision models (11b & 90b previews)
        if (in_array($this->visionModel, ['llama-3.2-11b-vision-preview', 'llama-3.2-90b-vision-preview'])) {
            $this->visionModel = 'meta-llama/llama-4-scout-17b-16e-instruct';
        }
    }

    /**
     * Evaluate student answer
     * ...
     */
    public function evaluate($question, $studentAnswer, $correctAnswer, $maxScore = 100)
    {
        // ... (lines 45-72 same)
        if (!$this->apiKey) {
             return [
                'score' => 0,
                'feedback' => 'Error: API Key AI belum dikonfigurasi di pengaturan admin.',
                'error' => true
            ];
        }

        try {
            if ($this->provider === 'groq') {
                return $this->evaluateWithGroq($question, $studentAnswer, $correctAnswer, $maxScore);
            } elseif ($this->provider === 'gemini') {
                return $this->evaluateWithGemini($question, $studentAnswer, $correctAnswer, $maxScore);
            }
            
            // Future providers
            return [
                'score' => 0,
                'feedback' => 'Provider AI tidak didukung saat ini.',
                'error' => true
            ];

        } catch (\Exception $e) {
            Log::error('AI Grading Error: ' . $e->getMessage());
            return [
                'score' => 0,
                'feedback' => 'Terjadi kesalahan saat menghubungi AI: ' . $e->getMessage(),
                'error' => true
            ];
        }
    }

    protected function evaluateWithGemini($question, $studentAnswer, $correctAnswer, $maxScore)
    {
        $prompt = "Anda adalah asisten guru. Tugas: Menilai jawaban soal uraian.
        
        Soal: \"{$question}\"
        Kunci/Konteks: \"{$correctAnswer}\"
        Jawaban Siswa: \"{$studentAnswer}\"
        
        Instruksi:
        1. Bandingkan jawaban siswa dengan kunci.
        2. Beri nilai (0-{$maxScore}).
        3. Beri feedback (max 3 kalimat, Bahasa Indonesia).
        4. Output WAJIB JSON: {\"score\": int, \"feedback\": string} tanpa markdown ```json";

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
        
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.3,
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception("Gemini API Error: " . $response->body());
        }

        $json = $response->json();
        $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $result = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback strategy if JSON is broken (regex)
            preg_match('/"score"\s*:\s*(\d+)/', $content, $scoreMatches);
            preg_match('/"feedback"\s*:\s*"(.*?)"/', $content, $feedbackMatches);

            $result = [
                'score' => $scoreMatches[1] ?? 0,
                'feedback' => $feedbackMatches[1] ?? 'Feedback tidak terbaca.'
            ];
        }

        return [
            'score' => isset($result['score']) ? min($maxScore, max(0, intval($result['score']))) : 0,
            'feedback' => $result['feedback'] ?? 'Tidak ada feedback dari AI.',
            'error' => false
        ];
    }
    
    protected function evaluateWithGroq($question, $studentAnswer, $correctAnswer, $maxScore)
    {
        $prompt = "Anda adalah asisten guru yang objektif. Tugas Anda adalah menilai jawaban siswa soal uraian.
        
        Soal: \"{$question}\"
        Kunci Jawaban / Konteks: \"{$correctAnswer}\"
        Jawaban Siswa: \"{$studentAnswer}\"
        
        Instruksi:
        1. Bandingkan jawaban siswa dengan kunci jawaban.
        2. Berikan nilai (score) antara 0 sampai {$maxScore}.
        3. Berikan feedback singkat (maksimal 3 kalimat) dalam Bahasa Indonesia.
        4. Output WAJIB berupa JSON valid dengan format: {\"score\": int, \"feedback\": string}. Jangan ada teks lain.";

        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Anda adalah sistem penilaian otomatis yang outputnya selalu JSON.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.2, // Low temperature for consistent grading
            'max_tokens' => 300,
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->failed()) {
            throw new \Exception("Groq API Error: " . $response->body());
        }

        $json = $response->json();
        $content = $json['choices'][0]['message']['content'] ?? '{}';
        $result = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback strategy if JSON is broken (regex)
            preg_match('/"score"\s*:\s*(\d+)/', $content, $scoreMatches);
            preg_match('/"feedback"\s*:\s*"(.*?)"/', $content, $feedbackMatches);
            
            $result = [
                'score' => $scoreMatches[1] ?? 0,
                'feedback' => $feedbackMatches[1] ?? 'Feedback tidak terbaca.'
            ];
        }

        return [
            'score' => isset($result['score']) ? min($maxScore, max(0, intval($result['score']))) : 0,
            'feedback' => $result['feedback'] ?? 'Tidak ada feedback dari AI.',
            'error' => false
        ];
    }

    /**
     * Evaluate student answer with Image (Multimodal)
     */
    public function evaluateImage($question, $imagePath, $contextOrKey, $maxScore = 100)
    {
        if (!$this->apiKey) {
             return [
                'score' => 0,
                'feedback' => 'Error: API Key AI belum dikonfigurasi.',
                'error' => true
            ];
        }

        try {
            // Validate Image
            if (!file_exists($imagePath)) {
                return ['score' => 0, 'feedback' => 'File gambar tidak ditemukan.', 'error' => true];
            }

            $mimeType = mime_content_type($imagePath);
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
                return ['score' => 0, 'feedback' => 'Format file tidak didukung AI (hanya JPG/PNG).', 'error' => true];
            }

            // Validate file size (max 4MB for vision APIs)
            $fileSize = filesize($imagePath);
            if ($fileSize > 4 * 1024 * 1024) {
                return ['score' => 0, 'feedback' => 'File gambar terlalu besar (maksimal 4MB untuk AI Vision).', 'error' => true];
            }

            if ($this->provider === 'groq') {
                // Use Vision Model
                // ... Groq implementation loaded from previous step but we need to split it if we want cleaner code
                // For now, let's just use if/else block inside evaluateImage for simplicity or refactor.
            }

            // Refactored EvaluateImage to support providers
            if ($this->provider === 'gemini') {
                return $this->evaluateImageWithGemini($question, $imagePath, $contextOrKey, $maxScore);
            }

            // Default / Groq Implementation (Existing)
            // ... (keep existing Groq logic here or move to separate method, let's keep it inline for now but wrapped)
            
            // Groq Logic (Moved to be explicit)
            $base64Image = base64_encode(file_get_contents($imagePath));
            $dataUrl = "data:{$mimeType};base64,{$base64Image}";

            $prompt = "Anda adalah asisten guru. Tugas Anda menilai jawaban siswa berupa GAMBAR.
            
            Soal/Tugas: \"{$question}\"
            Konteks/Kunci: \"{$contextOrKey}\"
            
            Instruksi:
            1. Analisis gambar jawaban siswa dengan teliti.
            2. Bandingkan dengan soal dan konteks.
            3. Berikan nilai (0-{$maxScore}) dan feedback konstruktif (max 3 kalimat Bahasa Indonesia).
            4. Jika gambar tidak terbaca/irrelavan, beri nilai 0.
            5. Output WAJIB JSON valid: {\"score\": int, \"feedback\": string}";

            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->visionModel,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => $dataUrl]]
                        ]
                    ]
                ],
                'temperature' => 0.2,
                'max_tokens' => 300,
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->failed()) {
                throw new \Exception("Groq API Error: " . $response->body());
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? '{}';
            $result = json_decode($content, true);

            return [
                'score' => isset($result['score']) ? min($maxScore, max(0, intval($result['score']))) : 0,
                'feedback' => $result['feedback'] ?? 'Tidak ada feedback.',
                'error' => false
            ];

        } catch (\Exception $e) {
            Log::error('AI Vision Error: ' . $e->getMessage());
            return [
                'score' => 0,
                'feedback' => 'Gagal analisis gambar: ' . $e->getMessage(),
                'error' => true
            ];
        }
    }

    protected function evaluateImageWithGemini($question, $imagePath, $contextOrKey, $maxScore)
    {
        $mimeType = mime_content_type($imagePath);
        $base64Image = base64_encode(file_get_contents($imagePath));

        $prompt = "Anda asisten guru. Nilai gambar jawaban siswa ini.
        Soal: {$question}
        Kunci: {$contextOrKey}
        
        Output JSON: {\"score\": int (0-{$maxScore}), \"feedback\": string (max 3 kalimat)}";

        // Use configured text model for Gemini (Flash/Pro supports vision natively)
        // Or explicitly use vision model setting if different, but usually gemini-1.5-flash is both.
        // Let's use $this->model because Gemini models are multimodal by default.
        $model = str_contains($this->model, 'gemini') ? $this->model : 'gemini-1.5-flash';

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}";

        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->failed()) {
            throw new \Exception("Gemini Vision Error: " . $response->body());
        }

        $json = $response->json();
        $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $result = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Fallback strategy if JSON is broken (regex)
            preg_match('/"score"\s*:\s*(\d+)/', $content, $scoreMatches);
            preg_match('/"feedback"\s*:\s*"(.*?)"/', $content, $feedbackMatches);

            $result = [
                'score' => $scoreMatches[1] ?? 0,
                'feedback' => $feedbackMatches[1] ?? 'Feedback tidak terbaca.'
            ];
        }

        return [
            'score' => isset($result['score']) ? min($maxScore, max(0, intval($result['score']))) : 0,
            'feedback' => $result['feedback'] ?? 'Tidak ada feedback.',
            'error' => false
        ];
    }

}
