<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AppSetting;
use Exception;

class AiChatbotService
{
    private $groqApiKey;
    private $geminiApiKey;
    private $provider;
    private $defaultModel;
    private $visionModel;

    public function __construct()
    {
        $this->loadConfig();
    }

    protected function loadConfig()
    {
        $settings = AppSetting::whereIn('key', [
            'groq_api_key',
            'gemini_api_key',
            'ai_model',
            'ai_vision_model',
            'ai_provider',
        ])->pluck('value', 'key');

        $this->provider = $settings['ai_provider'] ?? 'groq';
        $this->groqApiKey = $settings['groq_api_key'] ?? '';
        $this->geminiApiKey = $settings['gemini_api_key'] ?? '';
        $this->defaultModel = $settings['ai_model'] ?? 'llama-3.3-70b-versatile';
        $this->visionModel = $settings['ai_vision_model'] ?? 'meta-llama/llama-4-scout-17b-16e-instruct';

        // Auto-fix for decommissioned models
        if (in_array($this->defaultModel, ['llama3-70b-8192', 'llama-3.2-90b-text-preview'])) {
            $this->defaultModel = 'llama-3.3-70b-versatile';
        }

        if (in_array($this->visionModel, ['llama-3.2-11b-vision-preview', 'llama-3.2-90b-vision-preview'])) {
            $this->visionModel = 'meta-llama/llama-4-scout-17b-16e-instruct';
        }
    }

    /**
     * Get available AI models for dropdown
     *
     * @return array
     */
    public function getAvailableModels(): array
    {
        $models = [];

        // Add Groq models if API key configured
        if (!empty($this->groqApiKey)) {
            $models[] = [
                'id' => 'llama-3.3-70b-versatile',
                'name' => 'Llama 3.3 70B (Versatile)',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'llama-3.3-70b-versatile',
            ];

            $models[] = [
                'id' => 'qwen/qwen3-32b',
                'name' => 'Qwen 3 32B (High Rate Limit)',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'qwen/qwen3-32b',
            ];

            $models[] = [
                'id' => 'openai/gpt-oss-120b',
                'name' => 'GPT OSS 120B',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'openai/gpt-oss-120b',
            ];

            $models[] = [
                'id' => 'meta-llama/llama-4-scout-17b-16e-instruct',
                'name' => 'Llama 4 Scout 17B (Vision)',
                'provider' => 'groq',
                'supports_vision' => true,
                'supports_pdf' => false, 
                'default' => $this->defaultModel === 'meta-llama/llama-4-scout-17b-16e-instruct',
            ];

            $models[] = [
                'id' => 'allam-2-7b',
                'name' => 'Allam 2 7B',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'allam-2-7b',
            ];

            $models[] = [
                'id' => 'groq/compound',
                'name' => 'Groq Compound',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'groq/compound',
            ];
        }

        // Add Gemini models if API key configured
        if (!empty($this->geminiApiKey)) {
            $models[] = [
                'id' => 'gemini-2.5-flash',
                'name' => 'Gemini 2.5 Flash (PDF + Vision)',
                'provider' => 'gemini',
                'supports_vision' => true,
                'supports_pdf' => true, // Only Gemini supports PDF
                'default' => $this->defaultModel === 'gemini-2.5-flash',
            ];
        }

        // If no models available, return default
        if (empty($models)) {
            $models[] = [
                'id' => 'llama-3.3-70b-versatile',
                'name' => 'Llama 3.3 70B (Not Configured)',
                'provider' => 'groq',
                'supports_vision' => false,
                'default' => true,
            ];
        }

        return $models;
    }

    /**
     * Send message to AI chatbot
     *
     * @param string $userMessage
     * @param array $conversationHistory
     * @param string $userRole
     * @param string $selectedModel
     * @param array $attachedFiles Array of files [['path' => string, 'mime' => string, 'size' => int, 'name' => string], ...]
     * @return array ['success' => bool, 'response' => string, 'error' => string|null, 'model' => string]
     */
    public function sendMessage(
        string $userMessage,
        array $conversationHistory,
        string $userRole,
        string $selectedModel,
        array $attachedFiles = []
    ): array {
        try {
            // Validate API keys
            $provider = $this->detectProvider($selectedModel);
            $apiKey = $provider === 'groq' ? $this->groqApiKey : $this->geminiApiKey;

            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'error' => 'API key untuk ' . ucfirst($provider) . ' belum dikonfigurasi. Hubungi admin.',
                    'model' => $selectedModel,
                ];
            }

            // Validate PDF compatibility
            $hasPdf = !empty($attachedFiles) && collect($attachedFiles)->contains('mime', 'application/pdf');
            if ($hasPdf && $provider === 'groq') {
                return [
                    'success' => false,
                    'error' => 'Model Groq tidak support PDF. Silakan gunakan Gemini 2.5 Flash untuk membaca PDF.',
                    'model' => $selectedModel,
                    'switch_to_gemini' => true, // Signal to frontend
                ];
            }

            // Build messages array
            $messages = $this->buildMessagesArray($conversationHistory, $userMessage, $userRole, $attachedFiles, $provider);

            // Call AI with fallback
            $result = $this->callAiWithFallback($messages, $selectedModel, $provider);

            return $result;

        } catch (Exception $e) {
            Log::error('AI Chatbot Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'model' => $selectedModel,
            ];
        }
    }

    /**
     * Build system prompt based on user role
     *
     * @param string $userRole
     * @return string
     */
    private function buildSystemPrompt(string $userRole): string
    {
        // IMPORTANT: System/navigation questions are handled by rule-based KB on frontend.
        // This LLM prompt should ONLY handle general/educational questions.
        // DO NOT add any SIPADUHOK-specific menu/feature knowledge here.
        return "Anda adalah asisten AI di sistem informasi akademik SIPADUHOK. Anda membantu menjawab pertanyaan umum, edukasi, dan juga pertanyaan seputar sistem.\n\n"
             . "**ATURAN PENTING:**\n"
             . "1. Jawab dengan Bahasa Indonesia yang sopan dan mudah dipahami.\n"
             . "2. Jika user bertanya tentang navigasi sistem, menu, fitur, atau cara menggunakan SIPADUHOK — coba bantu dengan informasi umum yang wajar (misalnya: 'Untuk reset password, biasanya bisa melalui menu Pengaturan Akun atau hubungi Admin'). Jangan mengarang fitur spesifik.\n"
             . "3. Jika pertanyaan tidak jelas atau hanya berupa kata singkat ambigu, minta user menjelaskan lebih detail.\n"
             . "4. Berikan jawaban yang ringkas, informatif, dan to-the-point.\n"
             . "5. Jangan pernah menjawab 'Silakan ketik ulang pertanyaan Anda' — selalu berusaha memberikan jawaban yang berguna.";
    }

    /**
     * Build messages array for API
     *
     * @param array $history Conversation history [{role, content}, ...]
     * @param string $userMessage Current user message
     * @param string $userRole User role for system prompt
     * @param array $files Array of attached files
     * @param string $provider 'groq' or 'gemini'
     * @return array
     */
    private function buildMessagesArray(array $history, string $userMessage, string $userRole, array $files, string $provider): array
    {
        $messages = [];

        // Add system prompt
        $messages[] = [
            'role' => 'system',
            'content' => $this->buildSystemPrompt($userRole),
        ];

        // Add conversation history (trim to last 10 messages = 5 user + 5 assistant exchanges)
        $trimmedHistory = array_slice($history, -10);
        foreach ($trimmedHistory as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }

        // Add current user message
        $hasMultimodal = false;
        $contentParts = [];

        // Add text part first
        $contentParts[] = [
            'type' => 'text',
            'text' => $userMessage ?: 'Lihat file yang saya lampirkan dan jelaskan.',
        ];

        // Add image/file parts
        if (!empty($files)) {
            foreach ($files as $file) {
                if (in_array($file['mime'], ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])) {
                    // Image file - convert to base64 data URL
                    $base64Image = base64_encode(file_get_contents($file['path']));
                    $dataUrl = "data:{$file['mime']};base64,{$base64Image}";

                    $contentParts[] = [
                        'type' => 'image_url',
                        'image_url' => ['url' => $dataUrl],
                    ];

                    $hasMultimodal = true;

                } elseif ($file['mime'] === 'application/pdf') {
                    // PDF file - only Gemini supports it
                    if ($provider === 'gemini') {
                        // Convert PDF to base64 for Gemini
                        $base64Pdf = base64_encode(file_get_contents($file['path']));
                        $dataUrl = "data:application/pdf;base64,{$base64Pdf}";

                        $contentParts[] = [
                            'type' => 'pdf_url', // Custom type for PDF
                            'pdf_url' => ['url' => $dataUrl],
                            'mime' => 'application/pdf',
                            'base64' => $base64Pdf, // For Gemini inline_data format
                        ];

                        $hasMultimodal = true;
                    } else {
                        // Groq doesn't support PDF - just mention in text
                        $contentParts[0]['text'] .= "\n\n[File PDF terlampir: {$file['name']} - Model tidak support PDF]";
                    }
                }
            }
        }

        // Build final message
        if ($hasMultimodal) {
            // Multimodal message
            $messages[] = [
                'role' => 'user',
                'content' => $contentParts,
            ];
        } else {
            // Text-only message
            $messages[] = [
                'role' => 'user',
                'content' => $userMessage,
            ];
        }

        return $messages;
    }

    /**
     * Call AI with automatic fallback
     *
     * @param array $messages
     * @param string $selectedModel
     * @param string $provider
     * @return array
     */
    private function callAiWithFallback(array $messages, string $selectedModel, string $provider): array
    {
        // Try primary provider
        if ($provider === 'groq' && !empty($this->groqApiKey)) {
            $result = $this->callGroqApi($messages, $selectedModel);

            // Check for rate limit (429)
            if (!$result['success']) {
                $errorMsg = strtolower($result['error'] ?? '');
                $isQuotaError = strpos($errorMsg, 'rate limit') !== false ||
                                strpos($errorMsg, 'quota') !== false ||
                                strpos($errorMsg, '429') !== false;

                if ($isQuotaError) {
                    Log::warning("Groq quota exceeded for model {$selectedModel}. Attempting internal Groq fallback.");
                    
                    // Fallback to another Groq model before Gemini
                    $backupGroqModel = ($selectedModel === 'qwen/qwen3-32b') ? 'llama-3.3-70b-versatile' : 'qwen/qwen3-32b';
                    $backupResult = $this->callGroqApi($messages, $backupGroqModel);
                    
                    if ($backupResult['success']) {
                        return $backupResult;
                    }
                    
                    // If backup Groq model ALSO fails, fallback to Gemini
                    if (!empty($this->geminiApiKey)) {
                        Log::warning('Both Groq models rate-limited, falling back to Gemini.');
                        return $this->callGeminiApi($messages, $this->defaultModel);
                    }
                }
            }

            return $result;
        }

        if ($provider === 'gemini' && !empty($this->geminiApiKey)) {
            return $this->callGeminiApi($messages, $selectedModel);
        }

        // Fallback to Gemini if Groq not available entirely
        if (!empty($this->geminiApiKey)) {
            return $this->callGeminiApi($messages, $this->defaultModel);
        }

        return [
            'success' => false,
            'error' => 'Tidak ada API key yang tersedia (Groq atau Gemini).',
            'model' => $selectedModel,
        ];
    }

    /**
     * Call Groq API
     *
     * @param array $messages
     * @param string $model
     * @return array
     */
    private function callGroqApi(array $messages, string $model): array
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->groqApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->timeout(120) // Increase timeout for longer responses
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 8000, // Increased from 1000 to 8000 for longer responses
                ]);

            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = $response->body();

                // Handle rate limiting
                if ($statusCode === 429) {
                    return [
                        'success' => false,
                        'error' => 'Rate limit exceeded - Groq quota habis',
                        'status_code' => 429,
                        'model' => $model,
                    ];
                }

                return [
                    'success' => false,
                    'error' => "API Error {$statusCode}: " . substr($errorBody, 0, 200),
                    'status_code' => $statusCode,
                    'model' => $model,
                ];
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            return [
                'success' => true,
                'response' => $content,
                'model' => $model,
                'provider' => 'groq',
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq Connection Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Connection timeout - periksa koneksi internet',
                'model' => $model,
            ];
        } catch (Exception $e) {
            Log::error('Groq API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
                'model' => $model,
            ];
        }
    }

    /**
     * Call Gemini API
     *
     * @param array $messages
     * @param string $model
     * @return array
     */
    private function callGeminiApi(array $messages, string $model): array
    {
        try {
            // Convert OpenAI-style messages to Gemini format
            $contents = [];
            foreach ($messages as $msg) {
                if ($msg['role'] === 'system') {
                    // Prepend system message to first user message
                    continue;
                }

                if ($msg['role'] === 'user' || $msg['role'] === 'model') {
                    $role = $msg['role'] === 'user' ? 'user' : 'model';

                    if (is_array($msg['content'])) {
                        // Multimodal content
                        $parts = [];
                        foreach ($msg['content'] as $item) {
                            if ($item['type'] === 'text') {
                                $parts[] = ['text' => $item['text']];

                            } elseif ($item['type'] === 'image_url') {
                                // Extract base64 from data URL
                                $dataUrl = $item['image_url']['url'];
                                if (preg_match('/data:(.+);base64,(.+)/', $dataUrl, $matches)) {
                                    $mimeType = $matches[1];
                                    $base64Data = $matches[2];
                                    $parts[] = [
                                        'inline_data' => [
                                            'mime_type' => $mimeType,
                                            'data' => $base64Data,
                                        ],
                                    ];
                                }

                            } elseif ($item['type'] === 'pdf_url') {
                                // PDF file - use inline_data format
                                $parts[] = [
                                    'inline_data' => [
                                        'mime_type' => 'application/pdf',
                                        'data' => $item['base64'],
                                    ],
                                ];
                            }
                        }
                        $contents[] = ['role' => $role, 'parts' => $parts];
                    } else {
                        // Text content
                        $contents[] = [
                            'role' => $role,
                            'parts' => [['text' => $msg['content']]],
                        ];
                    }
                } elseif ($msg['role'] === 'assistant') {
                    // Gemini uses 'model' instead of 'assistant'
                    $contents[] = [
                        'role' => 'model',
                        'parts' => [['text' => $msg['content']]],
                    ];
                }
            }

            // Determine Gemini model
            $geminiModel = str_contains($model, 'gemini') ? $model : 'gemini-2.5-flash';
            // Use v1 API for Gemini 2.0+ models
            $url = "https://generativelanguage.googleapis.com/v1/models/{$geminiModel}:generateContent?key={$this->geminiApiKey}";

            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(120) // Increase timeout for PDF processing
                ->post($url, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 8000, // Increased from 1000 to 8000 (max for free tier)
                    ],
                ]);

            if ($response->failed()) {
                throw new Exception("Gemini API Error: " . $response->body());
            }

            $json = $response->json();
            $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respons dari AI.';

            return [
                'success' => true,
                'response' => $content,
                'model' => $geminiModel,
                'provider' => 'gemini',
            ];

        } catch (Exception $e) {
            Log::error('Gemini API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Error Gemini: ' . $e->getMessage(),
                'model' => $model,
            ];
        }
    }

    /**
     * Detect provider from model ID
     *
     * @param string $modelId
     * @return string 'groq' or 'gemini'
     */
    private function detectProvider(string $modelId): string
    {
        if (str_contains($modelId, 'gemini')) {
            return 'gemini';
        }

        if (str_contains($modelId, 'llama') || str_contains($modelId, 'qwen')) {
            return 'groq';
        }

        // Default
        return $this->provider;
    }

    /**
     * Process file attachment
     *
     * @param array $file ['path' => string, 'mime' => string, 'size' => int, 'name' => string]
     * @return array
     */
    public function processFileAttachment(array $file): array
    {
        // Validate file exists
        if (!file_exists($file['path'])) {
            return ['success' => false, 'error' => 'File tidak ditemukan.'];
        }

        // Validate MIME type
        $validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'application/pdf'];
        if (!in_array($file['mime'], $validTypes)) {
            return ['success' => false, 'error' => 'Format file tidak didukung. Gunakan JPG, PNG, atau PDF.'];
        }

        // Validate file size (max 4MB)
        if ($file['size'] > 4 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File terlalu besar. Maksimal 4MB.'];
        }

        return ['success' => true];
    }
}
