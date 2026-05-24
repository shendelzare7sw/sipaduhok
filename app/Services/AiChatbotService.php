<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AppSetting;
use App\Services\Chatbot\KnowledgeBaseLoader;
use Exception;

class AiChatbotService
{
    private $groqApiKey;
    private $geminiApiKey;
    private $provider;
    private $defaultModel;
    private $visionModel;
    private KnowledgeBaseLoader $kb;

    public function __construct(KnowledgeBaseLoader $kb)
    {
        $this->kb = $kb;
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
                if (empty($this->geminiApiKey)) {
                    return [
                        'success' => false,
                        'error' => 'Model Groq tidak support membaca dokumen PDF. API Key Gemini belum dikonfigurasi, silakan hubungi Administrator.',
                        'model' => $selectedModel,
                        'switch_to_gemini' => false,
                    ];
                }
                
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

            if ($result['success'] && !empty($result['response'])) {
                $result['structured'] = $this->parseStructuredResponse($result['response'], $userRole);
            }

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
     * Build system prompt with knowledge base injected from docs/flow/{role}.md
     */
    private function buildSystemPrompt(string $userRole): string
    {
        $strict = isContextRestrictionEnabled();
        $knowledge = $this->kb->getForRole($userRole);
        $landingPages = $this->kb->getLandingPagesPrompt();

        $routeMap = $this->kb->getRouteMapForRole($userRole);
        $routeList = '';
        $routeCount = 0;
        foreach ($routeMap as $name => $url) {
            if ($routeCount >= 50) break;
            $routeList .= "{$name}={$url}\n";
            $routeCount++;
        }
        if (strlen($routeList) > 1800) {
            $routeList = substr($routeList, 0, 1800) . "...\n";
        }

        $scopeRule = $strict
            ? "MODE KETAT AKTIF: HANYA jawab pertanyaan seputar menu, fitur, dan cara penggunaan SIPADUHOK berdasarkan KNOWLEDGE BASE di bawah. Untuk pertanyaan di luar topik SIPADUHOK (cuaca, politik, hiburan, matematika umum, tokoh publik, dll), return JSON dengan text: 'Maaf, saya hanya dapat membantu seputar sistem SIPADUHOK. Silakan tanyakan tentang menu atau fitur yang ada di sistem.' tanpa button/related/callout."
            : "MODE TERBUKA AKTIF: Boleh menjawab pertanyaan umum di luar SIPADUHOK. Untuk pertanyaan umum, jawab langsung secara natural dalam Bahasa Indonesia dan jangan memaksakan KNOWLEDGE BASE, OWNERSHIP TABLE, button, atau related. Jika riwayat chat lama berisi penolakan karena batasan konteks, abaikan penolakan itu dan ikuti mode terbuka saat ini. Untuk pertanyaan yang memang terkait SIPADUHOK, tetap prioritaskan konteks SIPADUHOK jika relevan.";

        $roleLabel = $this->kb->roleLabel($userRole ?: 'guest');
        $ownershipPrompt = $this->kb->getOwnershipPromptForRole($userRole ?: 'guest');
        $ownershipTitle = $strict
            ? '🔴 LANGKAH PERTAMA WAJIB: CEK OWNERSHIP TABLE 🔴'
            : 'PANDUAN ROLE UNTUK PERTANYAAN SIPADUHOK';
        $ownershipIntro = $strict
            ? "Ini adalah daftar fitur yang DIKELOLA OLEH ROLE LAIN (bukan {$roleLabel}). Jika keyword pertanyaan user cocok dengan salah satu topik di bawah, WAJIB pakai POLA REKOMENDASI LINTAS-ROLE — JANGAN kasih langkah teknis menu."
            : "Gunakan tabel ini hanya jika pertanyaan user berkaitan dengan menu, fitur, data sekolah, hak akses, atau cara penggunaan SIPADUHOK. Untuk pertanyaan umum di luar SIPADUHOK, lewati bagian ini.";
        $knowledgeInstruction = $strict
            ? "LANGKAH KEDUA: Jika topik TIDAK ada di OWNERSHIP TABLE di atas, jawab berdasarkan KNOWLEDGE BASE role {$roleLabel} di bawah."
            : "Untuk pertanyaan yang memang terkait SIPADUHOK dan tidak ada di OWNERSHIP TABLE, jawab berdasarkan KNOWLEDGE BASE role {$roleLabel} di bawah. Untuk pertanyaan umum di luar SIPADUHOK, KNOWLEDGE BASE tidak wajib dipakai.";
        $coreRules = $strict
            ? implode("\n", [
                '1. SETIAP pertanyaan user adalah tentang sistem SIPADUHOK. DILARANG menjawab umum/generik (cth: "tergantung kebijakan sekolah", langkah Windows/Linux, teori jurnalistik, dll).',
                '2. Jawab HANYA berdasarkan KNOWLEDGE BASE dan OWNERSHIP TABLE di bawah.',
                '3. Jika tidak yakin: cek OWNERSHIP TABLE dulu, lalu KNOWLEDGE BASE. Jika tetap tidak ada, balas: "Maaf, saya belum punya informasi spesifik tentang itu di SIPADUHOK."',
            ])
            : implode("\n", [
                '1. Jika pertanyaan user terkait SIPADUHOK, menu, fitur, data sekolah, hak akses, atau cara penggunaan sistem: gunakan OWNERSHIP TABLE, KNOWLEDGE BASE, LANDING PAGES, dan ROUTE URL MAP di bawah.',
                '2. Jika pertanyaan user adalah pertanyaan umum di luar SIPADUHOK: jawab langsung berdasarkan pengetahuan umum secara ringkas, jelas, dan aman. Jangan menolak hanya karena topiknya di luar SIPADUHOK.',
                '3. Untuk pertanyaan umum di luar SIPADUHOK, set "button": null dan "related": null. Jangan mengarang route atau memaksa jawaban ke konteks SIPADUHOK.',
            ]);

        return <<<PROMPT
Anda adalah **Asisten SIPADUHOK** — chatbot resmi sistem informasi akademik PKBM House Of Knowledge. Pengguna login dengan role: **{$roleLabel}**.

⚠️ ATURAN UTAMA (TIDAK BOLEH DILANGGAR):
{$coreRules}

═══════════════════════════════════════════════════════════
{$ownershipTitle}
═══════════════════════════════════════════════════════════
{$ownershipIntro}

{$ownershipPrompt}

POLA REKOMENDASI LINTAS-ROLE — pakai persis format ini:
{
  "text": "Fitur [topik] dikelola oleh [role pemilik]. Sebagai {$roleLabel}, Anda tidak [aksi] langsung — silakan koordinasi dengan [role pemilik] untuk hal ini.",
  "callout": null,
  "button": null,   // KECUALI role = admin, boleh isi dengan admin_view_route untuk monitoring
  "related": null
}

Contoh OWNERSHIP-aware response (user = wakasek, tanya "cara approve rapor"):
{"text":"Approve/validasi rapor dilakukan oleh Ketua PKBM (validasi akhir setelah Wali Kelas generate). Sebagai Wakasek, Anda tidak melakukan approve rapor langsung — silakan koordinasi dengan Ketua PKBM.","callout":null,"button":null,"related":null}

Contoh OWNERSHIP-aware response (user = sekretaris, tanya "cara buat tagihan"):
{"text":"Pembuatan tagihan (SPP, bulk create, custom) dikelola oleh Bendahara atau Admin. Sebagai Sekretaris, Anda tidak membuat tagihan — silakan koordinasi dengan Bendahara/Admin.","callout":null,"button":null,"related":null}

═══════════════════════════════════════════════════════════
{$knowledgeInstruction}
═══════════════════════════════════════════════════════════

═══════════════════════════════════════════════════════════
ATURAN OUTPUT JSON (WAJIB):
═══════════════════════════════════════════════════════════
Selalu balas dengan JSON valid (tanpa code fence ```, tanpa teks pembungkus), schema:
{
  "text": "jawaban natural Bahasa Indonesia, max 3 paragraf pendek. Pakai \\n untuk newline. Hindari markdown heading (#).",
  "callout": "info penting singkat (max 200 char) atau null",
  "button": { "label": "Buka [Nama Menu]", "route": "nama.route.dari.map" } atau null,
  "related": [ { "label": "topik", "route": "nama.route" } ] atau null (max 3)
}

Aturan field "button" & "related": route HARUS dari ROUTE URL MAP di bawah. JANGAN mengarang nama route.

═══════════════════════════════════════════════════════════
ATURAN SCOPE:
═══════════════════════════════════════════════════════════
{$scopeRule}

═══════════════════════════════════════════════════════════
HALAMAN PUBLIK / LANDING PAGES — bisa diakses OLEH SEMUA ROLE (termasuk {$roleLabel}):
═══════════════════════════════════════════════════════════
Jika user bertanya tentang informasi umum sekolah (profil, struktur organisasi, visi misi, program, fasilitas, kontak, berita, PPDB, dll), arahkan ke halaman landing page di bawah, BUKAN ke menu admin setelah login. Untuk button/related, gunakan URL path langsung (mis. "/struktur-organisasi") sebagai field "route".

{$landingPages}

Contoh response untuk pertanyaan landing-page (berlaku semua role):
{"text":"Anda dapat melihat struktur organisasi sekolah di halaman publik /struktur-organisasi.","callout":null,"button":{"label":"Lihat Struktur Organisasi","route":"/struktur-organisasi"},"related":[{"label":"Profil Guru","route":"/profil-guru"},{"label":"Visi & Misi","route":"/visi-misi"}]}

═══════════════════════════════════════════════════════════
KNOWLEDGE BASE — dokumentasi menu/fitur internal yang DIKELOLA OLEH role {$roleLabel} (setelah login):
═══════════════════════════════════════════════════════════
{$knowledge}

═══════════════════════════════════════════════════════════
ROUTE URL MAP — daftar route name VALID untuk role {$roleLabel}:
═══════════════════════════════════════════════════════════
{$routeList}
PROMPT;
    }

    /**
     * Parse LLM raw output into structured response.
     * Resolves route names → URLs, filters routes not accessible to role.
     */
    private function parseStructuredResponse(string $raw, string $role): array
    {
        $fallback = [
            'text' => $raw,
            'callout' => null,
            'button' => null,
            'related' => null,
        ];

        $cleaned = trim($raw);
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/```\s*$/', '', $cleaned);
        $cleaned = trim($cleaned);

        if (!str_starts_with($cleaned, '{')) {
            if (preg_match('/\{[\s\S]+\}/', $cleaned, $m)) {
                $cleaned = $m[0];
            } else {
                return $fallback;
            }
        }

        $parsed = json_decode($cleaned, true);
        if (!is_array($parsed) || empty($parsed['text'])) {
            return $fallback;
        }

        $structured = [
            'text' => (string) $parsed['text'],
            'callout' => isset($parsed['callout']) && is_string($parsed['callout']) && trim($parsed['callout']) !== ''
                ? trim($parsed['callout'])
                : null,
            'button' => null,
            'related' => null,
        ];

        if (isset($parsed['button']) && is_array($parsed['button'])) {
            $url = $this->resolveButtonUrl($parsed['button']['route'] ?? null, $role);
            $label = $parsed['button']['label'] ?? null;
            if ($url && $label) {
                $structured['button'] = ['label' => trim($label), 'url' => $url];
            }
        }

        if (isset($parsed['related']) && is_array($parsed['related'])) {
            $related = [];
            foreach ($parsed['related'] as $item) {
                if (count($related) >= 3) break;
                if (!is_array($item)) continue;
                $url = $this->resolveButtonUrl($item['route'] ?? null, $role);
                $label = $item['label'] ?? null;
                if (!$url || !$label) continue;
                $related[] = ['label' => trim($label), 'url' => $url];
            }
            if (!empty($related)) {
                $structured['related'] = $related;
            }
        }

        return $structured;
    }

    /**
     * Resolve a button/related "route" field to a final URL.
     * Accepts:
     *  - Named route (e.g. "admin.kelas.index") — resolved via route() and filtered by role permission.
     *  - Direct URL path starting with "/" (landing pages only) — allowed for ALL roles.
     */
    private function resolveButtonUrl(?string $route, string $role): ?string
    {
        if (!$route) return null;
        $route = trim($route);
        if ($route === '') return null;

        if (str_starts_with($route, '/')) {
            return $this->kb->isLandingPageUrl($route) ? $route : null;
        }

        if (!$this->kb->isRouteAllowedForRole($route, $role)) return null;
        return $this->kb->resolveRouteUrl($route);
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
        if ($provider === 'groq' && !empty($this->groqApiKey)) {
            $chain = $this->buildGroqFallbackChain($selectedModel);
            $tried = [];
            $lastResult = null;

            foreach ($chain as $model) {
                if (in_array($model, $tried, true)) continue;
                $tried[] = $model;
                $result = $this->callGroqApi($messages, $model);
                if ($result['success']) {
                    if ($model !== $selectedModel) {
                        Log::info("Groq fallback succeeded with model: {$model} (original: {$selectedModel})");
                        $result['fallback_used'] = true;
                    }
                    return $result;
                }
                $lastResult = $result;
                if (!$this->isQuotaError($result['error'] ?? '')) {
                    break;
                }
                Log::warning("Groq model {$model} hit quota, trying next in fallback chain.");
            }

            if (!empty($this->geminiApiKey)) {
                Log::warning('All Groq models exhausted, falling back to Gemini.');
                $geminiResult = $this->callGeminiApi($messages, 'gemini-2.5-flash');
                if ($geminiResult['success']) {
                    $geminiResult['fallback_used'] = true;
                }
                return $geminiResult;
            }

            return $lastResult ?? [
                'success' => false,
                'error' => 'Semua model Groq sedang tidak tersedia dan Gemini API Key belum dikonfigurasi.',
                'model' => $selectedModel,
            ];
        }

        if ($provider === 'gemini' && !empty($this->geminiApiKey)) {
            return $this->callGeminiApi($messages, $selectedModel);
        }

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
     * Build ordered list of Groq models to try, starting with the selected one.
     */
    private function buildGroqFallbackChain(string $selectedModel): array
    {
        $defaultOrder = [
            'llama-3.3-70b-versatile',
            'qwen/qwen3-32b',
            'llama-3.1-8b-instant',
            'openai/gpt-oss-120b',
            'groq/compound',
            'allam-2-7b',
        ];

        $chain = [$selectedModel];
        foreach ($defaultOrder as $model) {
            if (!in_array($model, $chain, true)) {
                $chain[] = $model;
            }
        }
        return $chain;
    }

    private function isQuotaError(string $error): bool
    {
        $error = strtolower($error);
        return str_contains($error, 'rate limit')
            || str_contains($error, 'quota')
            || str_contains($error, '429')
            || str_contains($error, 'tpm')
            || str_contains($error, 'too many requests')
            || str_contains($error, 'capacity');
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
                    'temperature' => 0.4,
                    'max_tokens' => 900,
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
            $systemInstruction = null;
            $systemPrepended = false;
            foreach ($messages as $msg) {
                if ($msg['role'] === 'system') {
                    $systemInstruction = is_string($msg['content']) ? $msg['content'] : null;
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

            // Prepend system instruction to first user message (v1 API doesn't support systemInstruction field)
            if ($systemInstruction && !empty($contents)) {
                foreach ($contents as &$c) {
                    if ($c['role'] === 'user' && isset($c['parts'][0]['text']) && !$systemPrepended) {
                        $c['parts'][0]['text'] = "[INSTRUKSI SISTEM — IKUTI SECARA KETAT]\n" . $systemInstruction . "\n\n[/INSTRUKSI SISTEM]\n\nPESAN USER:\n" . $c['parts'][0]['text'];
                        $systemPrepended = true;
                        break;
                    }
                }
                unset($c);
            }

            // Determine Gemini model
            $geminiModel = str_contains($model, 'gemini') ? $model : 'gemini-2.5-flash';
            $url = "https://generativelanguage.googleapis.com/v1/models/{$geminiModel}:generateContent?key={$this->geminiApiKey}";

            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 1500,
                ],
            ];

            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(45)
                ->post($url, $payload);

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
