<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class AiQuestionGeneratorService
{
    private $apiKey;
    private $provider;
    private $model;
    private $geminiApiKey;
    private $useGroq;

    public function __construct()
    {
        $this->loadConfig();
    }

    protected function loadConfig()
    {
        $settings = \App\Models\AppSetting::whereIn('key', [
            'groq_api_key',      // NEW: Groq-specific API key
            'gemini_api_key',    // NEW: Gemini-specific API key
            'ai_model',
            'ai_provider',
        ])->pluck('value', 'key');

        $this->provider = $settings['ai_provider'] ?? 'groq';
        $this->model = $settings['ai_model'] ?? 'llama-3.3-70b-versatile';

        // Auto-fix for decommissioned Groq models
        if (in_array($this->model, ['llama3-70b-8192', 'llama-3.2-90b-text-preview', 'llama-3.1-70b-versatile'])) {
            Log::warning("Decommissioned model detected: {$this->model}. Fallback to llama-3.3-70b-versatile");
            $this->model = 'llama-3.3-70b-versatile';
        }

        // Auto-fix for unavailable Mixtral/Gemma models (not free in Groq)
        // Note: qwen/qwen3-32b IS available on Groq free tier (60 RPM), so we allow it
        $blockedModels = ['mixtral', 'gemma', 'qwen-2.5', 'qwen2'];
        foreach ($blockedModels as $blocked) {
            if (str_contains($this->model, $blocked)) {
                Log::warning("Unavailable model detected: {$this->model}. Fallback to llama-3.3-70b-versatile");
                $this->model = 'llama-3.3-70b-versatile';
                break;
            }
        }

        // Auto-fix for deprecated Gemini models
        if (in_array($this->model, ['gemini-1.5-flash', 'gemini-1.5-pro', 'gemini-2.0-flash-exp'])) {
            $this->model = 'gemini-2.5-flash';
        }

        // Load API key based on active provider (dual API key system)
        if ($this->provider === 'groq') {
            $this->apiKey = $settings['groq_api_key'] ?? '';
            $this->useGroq = !empty($this->apiKey);
        } elseif ($this->provider === 'gemini') {
            $this->apiKey = $settings['gemini_api_key'] ?? '';
            $this->useGroq = false;
        } else {
            // Fallback
            $this->apiKey = '';
            $this->useGroq = false;
        }

        // Store Gemini key separately for fallback purposes
        $this->geminiApiKey = $settings['gemini_api_key'] ?? '';
    }

    /**
     * Main method: Generate questions based on type
     *
     * @param string $topic Topic/materi
     * @param string $questionType pilihan_ganda|benar_salah|uraian|isian_singkat
     * @param string $difficulty easy|medium|hard
     * @param int $count Number of questions (1-10)
     * @param string $subjectName Nama mata pelajaran
     * @param int $kelasNumber Nomor kelas (1-12)
     * @param string|null $customInstructions Optional custom instructions
     * @param bool $generateNarasi Whether to generate narasi/context passage
     * @return array Generated questions
     */
    public function generateQuestions(
        string $topic,
        string $questionType,
        string $difficulty,
        int $count,
        string $subjectName,
        int $kelasNumber,
        ?string $customInstructions = null,
        bool $generateNarasi = false
    ): array {
        try {
            // Validate inputs
            if (empty(trim($topic))) {
                throw new \Exception('Topik tidak boleh kosong');
            }

            if ($count < 1 || $count > 10) {
                throw new \Exception('Jumlah soal harus antara 1-10');
            }

            // Detect jenjang from kelas number
            $jenjang = $this->detectJenjang($kelasNumber);

            // Create cache key
            $cacheKey = $this->generateCacheKey($topic, $questionType, $difficulty, $count, $subjectName, $jenjang, $customInstructions, $generateNarasi);

            // Check cache first (6 hours expiry)
            if (Cache::has($cacheKey)) {
                Log::info('Serving cached questions for: ' . $topic);
                return Cache::get($cacheKey);
            }

            // Route to appropriate generator method
            $result = null;
            switch ($questionType) {
                case 'pilihan_ganda':
                    $result = $this->generateMCQ($topic, $difficulty, $count, $subjectName, $jenjang, $kelasNumber, $customInstructions, $generateNarasi);
                    break;

                case 'pilihan_ganda_kompleks':
                    // Complex MCQ uses same generator but with multi-answer flag
                    $result = $this->generateComplexMCQ($topic, $difficulty, $count, $subjectName, $jenjang, $kelasNumber, $customInstructions, $generateNarasi);
                    break;

                case 'benar_salah':
                    $result = $this->generateTrueFalse($topic, $count, $subjectName, $jenjang, $kelasNumber, $customInstructions, $generateNarasi);
                    break;

                case 'uraian':
                    $result = $this->generateEssay($topic, $difficulty, $count, $subjectName, $jenjang, $kelasNumber, $customInstructions, $generateNarasi);
                    break;

                case 'isian_singkat':
                    $result = $this->generateFillInBlank($topic, $count, $subjectName, $jenjang, $kelasNumber, $customInstructions, $generateNarasi);
                    break;

                default:
                    throw new \Exception("Tipe soal tidak didukung: {$questionType}");
            }

            // Cache successful results for 6 hours
            if ($result && $result['success']) {
                Cache::put($cacheKey, $result, now()->addHours(6));
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('AI Question Generation Error: ' . $e->getMessage(), [
                'topic' => $topic,
                'type' => $questionType,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Gagal generate soal: ' . $e->getMessage(),
                'questions' => [],
            ];
        }
    }

    /**
     * Generate cache key for questions
     */
    private function generateCacheKey(
        string $topic,
        string $type,
        string $difficulty,
        int $count,
        string $subject,
        string $jenjang,
        ?string $customInstructions,
        bool $generateNarasi = false
    ): string {
        $parts = [
            'ai_questions',
            md5(strtolower(trim($topic))),
            $type,
            $difficulty,
            $count,
            md5(strtolower(trim($subject))),
            $jenjang,
            $customInstructions ? md5(trim($customInstructions)) : 'none',
            $generateNarasi ? 'with_narasi' : 'no_narasi'
        ];

        return implode('_', $parts);
    }

    /**
     * Detect jenjang pendidikan from kelas number
     */
    private function detectJenjang(int $kelasNumber): string
    {
        if ($kelasNumber >= 1 && $kelasNumber <= 6) {
            return 'SD';
        } elseif ($kelasNumber >= 7 && $kelasNumber <= 9) {
            return 'SMP';
        } elseif ($kelasNumber >= 10 && $kelasNumber <= 12) {
            return 'SMA';
        }

        // Default to SMP if unclear
        return 'SMP';
    }

    /**
     * Generate Multiple Choice Questions (Pilihan Ganda)
     */
    private function generateMCQ(
        string $topic,
        string $difficulty,
        int $count,
        string $subject,
        string $jenjang,
        int $kelas,
        ?string $customInstructions = null,
        bool $generateNarasi = false
    ): array {
        $prompts = config('ai-prompts.question_generator.pilihan_ganda');
        $jenjangData = config("ai-prompts.jenjang_guidelines.{$jenjang}");

        // Build prompt
        $systemPrompt = $prompts['system'];
        $userPrompt = str_replace(
            ['{count}', '{topic}', '{subject}', '{difficulty}', '{jenjang}', '{kelas}', '{jenjang_guidelines}', '{example_question}'],
            [$count, $topic, $subject, $difficulty, $jenjang, $kelas, $jenjangData['guidelines'], $jenjangData['example_mcq']],
            $prompts['prompt']
        );

        // Add custom instructions if provided
        if (!empty($customInstructions)) {
            $userPrompt .= "\n\nInstruksi Tambahan dari Guru:\n" . $customInstructions;
        }

        // Add narasi generation instructions if requested
        if ($generateNarasi) {
            $userPrompt .= "\n\n**INSTRUKSI PENTING - GENERATE NARASI:**
Generate teks bacaan/narasi/konteks yang relevan dengan topik, lalu buat soal berdasarkan narasi tersebut.

Format narasi:
- Panjang: 150-300 kata (untuk $jenjang)
- Konten: Sesuai topik '$topic' dan mata pelajaran '$subject'
- Style: Informatif, menarik, sesuai usia siswa $jenjang
- Contoh: Untuk 'Siklus Air' → narasi tentang perjalanan tetes air dari laut ke langit

PENTING: Semua soal HARUS merujuk pada narasi. Siswa harus membaca narasi untuk menjawab.

Output JSON HARUS include field 'narasi' PLUS semua field yang diminta di template atas:
{
  \"narasi\": \"[Teks bacaan lengkap 150-300 kata di sini...]\",
  \"pertanyaan\": \"Berdasarkan teks di atas, ...\",
  \"tipe_soal\": \"pilihan_ganda\",
  \"pilihan_a\": \"...\",
  \"pilihan_b\": \"...\",
  \"pilihan_c\": \"...\",
  \"pilihan_d\": \"...\",
  \"pilihan_e\": \"...\",
  \"kunci_jawaban\": \"A\",
  \"bobot\": 10,
  \"penjelasan\": \"...\"
}

JANGAN HILANGKAN FIELD APAPUN - tambahkan 'narasi', jangan replace field lainnya!";
        }

        // Add final consistency reminder
        $userPrompt .= "\n\n**CRITICAL: Generate EXACTLY {$count} soal lengkap!**";
        $userPrompt .= "\nSetiap soal HARUS punya: pertanyaan, tipe_soal, pilihan_a/b/c/d/e, kunci_jawaban, bobot";
        if ($generateNarasi) {
            $userPrompt .= "\nSEMUA {$count} soal HARUS include field 'narasi'";
        }

        // Determine best model based on subject
        $model = $this->selectModelForSubject($subject);

        // Call AI with fallback
        $maxTokens = $generateNarasi ? 2000 : 1500; // Higher if narasi enabled
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.8, $maxTokens);

        if (!$response['success']) {
            return $response;
        }

        // Parse and validate questions
        $questions = $this->parseQuestions($response['content'], 'pilihan_ganda');

        return [
            'success' => true,
            'questions' => $questions,
            'metadata' => [
                'topic' => $topic,
                'difficulty' => $difficulty,
                'jenjang' => $jenjang,
                'model_used' => $response['model'],
                'provider' => $response['provider'] ?? 'groq',
            ],
        ];
    }

    /**
     * Generate Complex MCQ (Pilihan Ganda Kompleks) - multi-answer format
     */
    private function generateComplexMCQ(
        string $topic,
        string $difficulty,
        int $count,
        string $subject,
        string $jenjang,
        int $kelas,
        ?string $customInstructions = null,
        bool $generateNarasi = false
    ): array {
        $jenjangData = config("ai-prompts.jenjang_guidelines.{$jenjang}");

        $systemPrompt = 'Anda adalah generator soal Pilihan Ganda Kompleks profesional untuk siswa Indonesia. Soal Pilihan Ganda Kompleks (PGK) memiliki LEBIH DARI SATU jawaban benar dari 5 pilihan yang tersedia.';

        $userPrompt = "Tugas: Buat {$count} soal Pilihan Ganda Kompleks (multi-answer) berkualitas tinggi.\n\n"
            . "Context:\n"
            . "- Topik/Materi: {$topic}\n"
            . "- Mata Pelajaran: {$subject}\n"
            . "- Tingkat Kesulitan: {$difficulty}\n"
            . "- Jenjang Pendidikan: {$jenjang} Kelas {$kelas}\n\n"
            . "Pedoman Sesuai Jenjang:\n{$jenjangData['guidelines']}\n\n"
            . "Instruksi KHUSUS Soal PGK:\n"
            . "1. Setiap soal HARUS memiliki 2-4 jawaban benar dari 5 pilihan (A-E)\n"
            . "2. Pastikan pilihan salah (distractor) masuk akal dan menantang\n"
            . "3. kunci_jawaban berisi STRING kunci benar yang dipisahkan koma, contoh: \"A,C,E\"\n\n"
            . "Output JSON array (PENTING: strict JSON, no markdown):\n"
            . '[{"pertanyaan":"teks soal","tipe_soal":"pilihan_ganda_kompleks","pilihan_a":"teks A","pilihan_b":"teks B","pilihan_c":"teks C","pilihan_d":"teks D","pilihan_e":"teks E","kunci_jawaban":"A,C","bobot":10,"penjelasan":"penjelasan singkat"}]' . "\n\n"
            . "Generate {$count} soal untuk topik \"{$topic}\".";

        if (!empty($customInstructions)) {
            $userPrompt .= "\n\nInstruksi Tambahan: " . $customInstructions;
        }

        $model = $this->selectModelForSubject($subject);
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.8, 1500);

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'pilihan_ganda_kompleks');

        return [
            'success' => true,
            'questions' => $questions,
            'metadata' => [
                'topic' => $topic,
                'jenjang' => $jenjang,
                'model_used' => $response['model'],
                'provider' => $response['provider'] ?? 'groq',
            ],
        ];
    }

    /**
     * Generate True/False Questions (Benar/Salah)
     */
    private function generateTrueFalse(
        string $topic,
        int $count,
        string $subject,
        string $jenjang,
        int $kelas,
        ?string $customInstructions = null,
        bool $generateNarasi = false
    ): array {
        $prompts = config('ai-prompts.question_generator.benar_salah');
        $jenjangData = config("ai-prompts.jenjang_guidelines.{$jenjang}");

        $systemPrompt = $prompts['system'];
        $userPrompt = str_replace(
            ['{count}', '{topic}', '{subject}', '{jenjang}', '{kelas}', '{jenjang_guidelines}', '{example_question}'],
            [$count, $topic, $subject, $jenjang, $kelas, $jenjangData['guidelines'], $jenjangData['example_isian']],
            $prompts['prompt']
        );

        if (!empty($customInstructions)) {
            $userPrompt .= "\n\nInstruksi Tambahan dari Guru:\n" . $customInstructions;
        }

        // Add narasi generation instructions if requested
        if ($generateNarasi) {
            $userPrompt .= "\n\n**INSTRUKSI PENTING - GENERATE NARASI:**
Generate teks bacaan/narasi/konteks yang relevan dengan topik, lalu buat soal berdasarkan narasi tersebut.

Format narasi:
- Panjang: 150-300 kata (untuk $jenjang)
- Konten: Sesuai topik '$topic' dan mata pelajaran '$subject'
- Style: Informatif, menarik, sesuai usia siswa $jenjang

PENTING: Semua soal HARUS merujuk pada narasi. Siswa harus membaca narasi untuk menjawab.

Output JSON HARUS include field 'narasi' PLUS semua field yang diminta di template atas:
{
  \"narasi\": \"[Teks bacaan lengkap 150-300 kata di sini...]\",
  \"pertanyaan\": \"Berdasarkan teks di atas, pernyataan berikut yang BENAR adalah...\",
  \"tipe_soal\": \"benar_salah\",
  \"kunci_jawaban\": \"benar\",
  \"bobot\": 5,
  \"penjelasan\": \"...\"
}

JANGAN HILANGKAN FIELD APAPUN - tambahkan 'narasi', jangan replace field lainnya!";
        }

        // Add final consistency reminder
        $userPrompt .= "\n\n**CRITICAL: Generate EXACTLY {$count} soal lengkap!**";
        $userPrompt .= "\nSetiap soal HARUS punya: pertanyaan, tipe_soal, kunci_jawaban, bobot";
        if ($generateNarasi) {
            $userPrompt .= "\nSEMUA {$count} soal HARUS include field 'narasi'";
        }

        $model = $this->selectModelForSubject($subject);
        $maxTokens = $generateNarasi ? 1200 : 800; // Higher if narasi enabled
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.7, $maxTokens);

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'benar_salah');

        return [
            'success' => true,
            'questions' => $questions,
            'metadata' => [
                'topic' => $topic,
                'jenjang' => $jenjang,
                'model_used' => $response['model'],
                'provider' => $response['provider'] ?? 'groq',
            ],
        ];
    }

    /**
     * Generate Essay Questions (Uraian)
     */
    private function generateEssay(
        string $topic,
        string $difficulty,
        int $count,
        string $subject,
        string $jenjang,
        int $kelas,
        ?string $customInstructions = null,
        bool $generateNarasi = false
    ): array {
        $prompts = config('ai-prompts.question_generator.uraian');
        $jenjangData = config("ai-prompts.jenjang_guidelines.{$jenjang}");
        $difficultyVerbs = config("ai-prompts.difficulty_verbs.{$difficulty}");

        $systemPrompt = $prompts['system'];
        $userPrompt = str_replace(
            ['{count}', '{topic}', '{subject}', '{difficulty}', '{difficulty_verb}', '{jenjang}', '{kelas}', '{jenjang_guidelines}', '{example_question}'],
            [$count, $topic, $subject, $difficulty, $difficultyVerbs, $jenjang, $kelas, $jenjangData['guidelines'], $jenjangData['example_essay']],
            $prompts['prompt']
        );

        if (!empty($customInstructions)) {
            $userPrompt .= "\n\nInstruksi Tambahan dari Guru:\n" . $customInstructions;
        }

        // Add narasi generation instructions if requested
        if ($generateNarasi) {
            $userPrompt .= "\n\n**INSTRUKSI PENTING - GENERATE NARASI:**
Generate teks bacaan/narasi/konteks yang relevan dengan topik, lalu buat soal berdasarkan narasi tersebut.

Format narasi:
- Panjang: 150-300 kata (untuk $jenjang)
- Konten: Sesuai topik '$topic' dan mata pelajaran '$subject'
- Style: Informatif, menarik, sesuai usia siswa $jenjang
- Cocok untuk soal uraian/essay yang membutuhkan analisis mendalam

PENTING: Soal uraian HARUS merujuk pada narasi untuk analisis/penjelasan.

Output JSON HARUS include field 'narasi' PLUS semua field yang diminta di template atas:
{
  \"narasi\": \"[Teks bacaan lengkap 150-300 kata di sini...]\",
  \"pertanyaan\": \"Berdasarkan teks di atas, jelaskan...\",
  \"tipe_soal\": \"uraian\",
  \"bobot\": 15,
  \"rubrik_penilaian\": \"Rubrik penilaian:\n- Aspek 1: (X poin)\n- Aspek 2: (Y poin)\",
  \"contoh_jawaban\": \"Contoh jawaban ideal (opsional)\"
}

JANGAN HILANGKAN FIELD APAPUN - tambahkan 'narasi', jangan replace field lainnya!";
        }

        // Add final consistency reminder for all uraian questions
        $userPrompt .= "\n\n**CRITICAL: Generate EXACTLY {$count} soal lengkap dengan semua field required!**";
        $userPrompt .= "\nSetiap soal HARUS punya: pertanyaan, tipe_soal, bobot, rubrik_penilaian";
        if ($generateNarasi) {
            $userPrompt .= "\nSEMUA {$count} soal HARUS include field 'narasi' (150-300 kata)";
        }
        $userPrompt .= "\nValidasi JSON output Anda sebelum return - pastikan array berisi TEPAT {$count} object, tidak boleh kurang!";
        $userPrompt .= "\n\nPENTING: Soal uraian memiliki field rubrik_penilaian yang panjang. Pastikan semua {$count} soal ter-generate secara lengkap sebelum response berakhir.";

        $model = $this->selectModelForSubject($subject);
        // Use higher token counts for essay questions (each has rubrik_penilaian which is verbose)
        // Rule of thumb: ~600 tokens per essay question
        $maxTokens = $generateNarasi ? 4000 : min(600 * $count + 500, 3500);
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.7, $maxTokens);

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'uraian');

        // If we got fewer questions than requested, retry once with explicit count instruction
        if (count($questions) < $count) {
            $missing = $count - count($questions);
            Log::warning("Essay generator returned {count($questions)}/{$count} questions. Retrying for {$missing} missing questions.");

            $retryPrompt = $userPrompt . "\n\n[RETRY]: Sebelumnya hanya {" . count($questions) . "} soal yang di-generate. Kali ini WAJIB generate semua {$count} soal. Jangan berhenti sebelum semua {$count} soal selesai.";
            $retryResponse = $this->callAiWithFallback($model, $systemPrompt, $retryPrompt, 0.5, $maxTokens);

            if ($retryResponse['success']) {
                $retryQuestions = $this->parseQuestions($retryResponse['content'], 'uraian');
                // Use the retry result if it has more or equal questions
                if (count($retryQuestions) >= count($questions)) {
                    $questions = $retryQuestions;
                }
            }
        }

        return [
            'success' => true,
            'questions' => $questions,
            'metadata' => [
                'topic' => $topic,
                'difficulty' => $difficulty,
                'jenjang' => $jenjang,
                'model_used' => $response['model'],
                'provider' => $response['provider'] ?? 'groq',
                'requested_count' => $count,
                'actual_count' => count($questions),
            ],
        ];
    }

    /**
     * Generate Fill-in-the-Blank Questions (Isian Singkat)
     */
    private function generateFillInBlank(
        string $topic,
        int $count,
        string $subject,
        string $jenjang,
        int $kelas,
        ?string $customInstructions = null,
        bool $generateNarasi = false
    ): array {
        $prompts = config('ai-prompts.question_generator.isian_singkat');
        $jenjangData = config("ai-prompts.jenjang_guidelines.{$jenjang}");

        $systemPrompt = $prompts['system'];
        $userPrompt = str_replace(
            ['{count}', '{topic}', '{subject}', '{jenjang}', '{kelas}', '{jenjang_guidelines}', '{example_question}'],
            [$count, $topic, $subject, $jenjang, $kelas, $jenjangData['guidelines'], $jenjangData['example_isian']],
            $prompts['prompt']
        );

        if (!empty($customInstructions)) {
            $userPrompt .= "\n\nInstruksi Tambahan dari Guru:\n" . $customInstructions;
        }

        // Add narasi generation instructions if requested
        if ($generateNarasi) {
            $userPrompt .= "\n\n**INSTRUKSI PENTING - GENERATE NARASI:**
Generate teks bacaan/narasi/konteks yang relevan dengan topik, lalu buat soal berdasarkan narasi tersebut.

Format narasi:
- Panjang: 150-300 kata (untuk $jenjang)
- Konten: Sesuai topik '$topic' dan mata pelajaran '$subject'
- Style: Informatif, menarik, sesuai usia siswa $jenjang

PENTING: Soal isian singkat HARUS merujuk pada narasi. Jawaban harus ditemukan dalam teks.

Output JSON HARUS include field 'narasi' PLUS semua field yang diminta di template atas:
{
  \"narasi\": \"[Teks bacaan lengkap 150-300 kata di sini...]\",
  \"pertanyaan\": \"Berdasarkan teks, _____ adalah...\",
  \"tipe_soal\": \"isian_singkat\",
  \"kunci_jawaban\": \"jawaban singkat\",
  \"alternatif_jawaban\": [\"sinonim1\", \"sinonim2\"],
  \"bobot\": 5,
  \"penjelasan\": \"...\"
}

JANGAN HILANGKAN FIELD APAPUN - tambahkan 'narasi', jangan replace field lainnya!";
        }

        // Add final consistency reminder
        $userPrompt .= "\n\n**CRITICAL: Generate EXACTLY {$count} soal lengkap!**";
        $userPrompt .= "\nSetiap soal HARUS punya: pertanyaan, tipe_soal, kunci_jawaban, bobot";
        if ($generateNarasi) {
            $userPrompt .= "\nSEMUA {$count} soal HARUS include field 'narasi'";
        }

        $model = $this->selectModelForSubject($subject);
        // Isian singkat needs moderate tokens - no complex rubric
        $maxTokens = $generateNarasi ? 1500 : 1000;
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.7, $maxTokens, false); // false = no json_object mode

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'isian_singkat');

        return [
            'success' => true,
            'questions' => $questions,
            'metadata' => [
                'topic' => $topic,
                'jenjang' => $jenjang,
                'model_used' => $response['model'],
                'provider' => $response['provider'] ?? 'groq',
            ],
        ];
    }

    /**
     * Select best AI model based on subject
     * Llama for math/science, Qwen for Indonesian-specific subjects
     */
    private function selectModelForSubject(string $subject): string
    {
        // Use the dynamically configured model from the admin settings
        return $this->model;
    }

    /**
     * Call AI with automatic fallback from Groq to Gemini
     * @param bool $useJsonObjectMode Whether to use Groq's json_object response_format (not compatible with nested arrays)
     */
    private function callAiWithFallback(
        string $model,
        string $systemPrompt,
        string $userPrompt,
        float $temperature,
        int $maxTokens,
        bool $useJsonObjectMode = true
    ): array {
        // Try Groq first if API key is available
        if ($this->useGroq) {
            $groqResponse = $this->callGroqApi($model, $systemPrompt, $userPrompt, $temperature, $maxTokens);

            // Check if Groq quota exceeded (HTTP 429) or rate limit
            if (!$groqResponse['success']) {
                $errorMsg = strtolower($groqResponse['error'] ?? '');
                $isQuotaError = strpos($errorMsg, 'rate limit') !== false ||
                                strpos($errorMsg, 'quota') !== false ||
                                strpos($errorMsg, '429') !== false;

                if ($isQuotaError) {
                    // Determine alternative Groq model
                    $altModel = str_contains($model, 'qwen') ? 'llama-3.3-70b-versatile' : 'qwen/qwen3-32b';
                    Log::warning("Groq quota exceeded for {$model}, trying alternative model {$altModel}");
                    
                    // Try alternative model
                    $altResponse = $this->callGroqApi($altModel, $systemPrompt, $userPrompt, $temperature, $maxTokens, $useJsonObjectMode);
                    
                    if ($altResponse['success']) {
                        return $altResponse;
                    }
                    
                    // If alternative also fails, fallback to Gemini
                    if (!empty($this->geminiApiKey)) {
                        Log::warning('Alternative Groq model also failed, falling back to Gemini');
                        return $this->callGeminiApi($systemPrompt, $userPrompt, $temperature);
                    }
                }
            }

            return $groqResponse;
        }

        // Fallback to Gemini if Groq not available
        if (!empty($this->geminiApiKey)) {
            return $this->callGeminiApi($systemPrompt, $userPrompt, $temperature);
        }

        return [
            'success' => false,
            'error' => 'Tidak ada API key yang tersedia (Groq atau Gemini)',
        ];
    }

    /**
     * Call Groq API with JSON mode
     */
    private function callGroqApi(
        string $model,
        string $systemPrompt,
        string $userPrompt,
        float $temperature,
        int $maxTokens,
        bool $useJsonObjectMode = true
    ): array {
        try {
            $response = Http::withOptions([
                'verify' => false,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)->post('https://api.groq.com/openai/v1/chat/completions', array_filter([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $temperature,
                'max_tokens' => $maxTokens,
                // Only use json_object mode when flag is true AND we're not using nested arrays
                'response_format' => $useJsonObjectMode ? ['type' => 'json_object'] : null,
            ]));

            if (!$response->successful()) {
                $statusCode = $response->status();
                $errorBody = $response->body();

                Log::error('Groq API Error', [
                    'status' => $statusCode,
                    'body' => $errorBody,
                    'model' => $model
                ]);

                // Check for specific error types
                if ($statusCode === 429) {
                    return [
                        'success' => false,
                        'error' => 'Rate limit exceeded - Groq quota habis',
                        'status_code' => 429,
                    ];
                }

                return [
                    'success' => false,
                    'error' => "API Error {$statusCode}: " . substr($errorBody, 0, 200),
                    'status_code' => $statusCode,
                ];
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                throw new \Exception('Empty response from Groq API');
            }

            return [
                'success' => true,
                'content' => $content,
                'model' => $model,
                'provider' => 'groq',
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Groq Connection Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Connection timeout - periksa koneksi internet',
            ];
        } catch (\Exception $e) {
            Log::error('Groq API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Call Gemini API as fallback
     */
    private function callGeminiApi(
        string $systemPrompt,
        string $userPrompt,
        float $temperature
    ): array {
        try {
            $combinedPrompt = $systemPrompt . "\n\n" . $userPrompt;
            $combinedPrompt .= "\n\nIMPORTANT: Output harus berupa JSON array yang valid. Jangan tambahkan teks lain di luar JSON.";

            $response = Http::withOptions([
                'verify' => false,
            ])->timeout(90)->post(
                "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$this->geminiApiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $combinedPrompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'maxOutputTokens' => 2048,
                    ]
                ]
            );

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => 'Gemini API Error: ' . $response->status(),
                ];
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($content)) {
                throw new \Exception('Empty response from Gemini API');
            }

            return [
                'success' => true,
                'content' => $content,
                'model' => 'gemini-2.5-flash',
                'provider' => 'gemini',
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini Connection Error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Connection timeout - periksa koneksi internet',
            ];
        } catch (\Exception $e) {
            Log::error('Gemini API Exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Gemini Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Parse and validate AI-generated questions
     */
    private function parseQuestions(string $jsonContent, string $expectedType): array
    {
        try {
            // Remove markdown code blocks if present
            $jsonContent = preg_replace('/```json\s*/', '', $jsonContent);
            $jsonContent = preg_replace('/```\s*$/', '', $jsonContent);
            $jsonContent = trim($jsonContent);

            $data = json_decode($jsonContent, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Parse Error', [
                    'error' => json_last_error_msg(),
                    'content' => substr($jsonContent, 0, 500) // First 500 chars
                ]);
                throw new \Exception('Invalid JSON from AI: ' . json_last_error_msg());
            }

            // Validate structure - handle both array and object formats
            if (!is_array($data)) {
                throw new \Exception('AI response is not an array');
            }

            // If AI returned object with 'questions' key, extract it
            if (isset($data['questions']) && is_array($data['questions'])) {
                $data = $data['questions'];
            }

            // If AI returned object with 'data' key, extract it
            if (isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            }

            // If AI returned object with 'soal' key (Indonesian), extract it
            if (isset($data['soal']) && is_array($data['soal'])) {
                $data = $data['soal'];
            }

            // Validate each question
            $validatedQuestions = [];
            foreach ($data as $question) {
                if (is_array($question) && $this->validateQuestion($question, $expectedType)) {
                    $validatedQuestions[] = $question;
                } else {
                    Log::warning('Invalid question structure', ['question' => $question]);
                }
            }

            if (empty($validatedQuestions)) {
                Log::error('No valid questions after validation', [
                    'total_items' => count($data),
                    'expected_type' => $expectedType,
                    'sample_data' => array_slice($data, 0, 2) // Log first 2 items for debugging
                ]);
                throw new \Exception('No valid questions generated. Check logs for details.');
            }

            return $validatedQuestions;

        } catch (\Exception $e) {
            Log::error('Question Parse Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Validate question structure based on type
     */
    private function validateQuestion(array $question, string $type): bool
    {
        // Common required fields
        if (empty($question['pertanyaan']) || empty($question['tipe_soal'])) {
            Log::warning('Missing common fields', [
                'type' => $type,
                'has_pertanyaan' => isset($question['pertanyaan']),
                'has_tipe_soal' => isset($question['tipe_soal']),
                'question_keys' => array_keys($question)
            ]);
            return false;
        }

        // Note: 'narasi' field is OPTIONAL - only present when generateNarasi=true
        // No validation needed for narasi since it's optional

        // Type-specific validation
        switch ($type) {
            case 'pilihan_ganda':
                $valid = isset($question['pilihan_a'], $question['pilihan_b'], $question['pilihan_c'],
                             $question['pilihan_d'], $question['pilihan_e'], $question['kunci_jawaban']);
                if (!$valid) {
                    Log::warning('MCQ validation failed', [
                        'has_pilihan_a' => isset($question['pilihan_a']),
                        'has_pilihan_b' => isset($question['pilihan_b']),
                        'has_pilihan_c' => isset($question['pilihan_c']),
                        'has_pilihan_d' => isset($question['pilihan_d']),
                        'has_pilihan_e' => isset($question['pilihan_e']),
                        'has_kunci_jawaban' => isset($question['kunci_jawaban']),
                        'question_keys' => array_keys($question)
                    ]);
                }
                return $valid;

            case 'benar_salah':
                $valid = isset($question['kunci_jawaban']) &&
                       in_array(strtolower($question['kunci_jawaban']), ['benar', 'salah', 'true', 'false']);
                if (!$valid) {
                    Log::warning('True/False validation failed', [
                        'has_kunci_jawaban' => isset($question['kunci_jawaban']),
                        'kunci_value' => $question['kunci_jawaban'] ?? 'NULL',
                        'question_keys' => array_keys($question)
                    ]);
                }
                return $valid;

            case 'uraian':
                // For essay, only require 'pertanyaan' (already checked above).
                if (!isset($question['rubrik_penilaian']) && !isset($question['bobot'])) {
                    Log::warning('Essay question missing both rubrik_penilaian and bobot - accepted anyway', [
                        'question_keys' => array_keys($question),
                    ]);
                }
                return true; // Accept all essay questions that have 'pertanyaan'

            case 'pilihan_ganda_kompleks':
                // Multi-answer MCQ: needs pilihan A-E and kunci_jawaban (comma-separated)
                return isset($question['pilihan_a'], $question['pilihan_b'], $question['pilihan_c'],
                             $question['pilihan_d'], $question['pilihan_e'], $question['kunci_jawaban']);

            case 'isian_singkat':
                $valid = isset($question['kunci_jawaban']);
                if (!$valid) {
                    Log::warning('Fill-in-blank validation failed', [
                        'has_kunci_jawaban' => isset($question['kunci_jawaban']),
                        'question_keys' => array_keys($question)
                    ]);
                }
                return $valid;

            default:
                return false;
        }
    }

    /**
     * Track API usage for quota management
     */
    private function trackApiUsage(string $model, int $tokensUsed): void
    {
        try {
            \DB::table('api_usage_logs')->insert([
                'provider' => $this->provider,
                'model' => $model,
                'tokens_used' => $tokensUsed,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist yet
            Log::warning('API usage tracking failed', [
                'error' => $e->getMessage(),
                'model' => $model ?? 'unknown',
            ]);
        }
    }
}
