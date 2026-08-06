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

        // Ganti otomatis model yang sudah dimatikan penyedianya (daftar di
        // config/ai-models.php), supaya setting lama di database tidak bikin
        // fitur mati diam-diam dengan pesan "Gagal terhubung".
        $this->model = ai_model_aktif($this->model, $this->provider);

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
        $maxTokens = $generateNarasi ? max(2000, $count * 300 + 500) : min($count * 300 + 500, 3000);
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.8, $maxTokens);

        if (!$response['success']) {
            return $response;
        }

        // Parse and validate questions
        $questions = $this->parseQuestions($response['content'], 'pilihan_ganda');

        if (count($questions) < $count) {
            $missing = $count - count($questions);
            Log::warning('MCQ generator returned ' . count($questions) . "/{$count} questions. Retrying.");
            $retryPrompt = $userPrompt . "\n\n[RETRY]: Sebelumnya hanya " . count($questions) . " soal yang di-generate. Kali ini WAJIB generate semua {$count} soal. Jangan berhenti di tengah jalan.";
            $retryResponse = $this->callAiWithFallback($model, $systemPrompt, $retryPrompt, 0.5, $maxTokens);
            if ($retryResponse['success']) {
                $retryQuestions = $this->parseQuestions($retryResponse['content'], 'pilihan_ganda');
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
        $maxTokens = $generateNarasi ? max(2000, $count * 350 + 500) : min($count * 350 + 500, 3000);
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.8, $maxTokens);

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'pilihan_ganda_kompleks');

        if (count($questions) < $count) {
            $missing = $count - count($questions);
            Log::warning('Complex MCQ generator returned ' . count($questions) . "/{$count} questions. Retrying.");
            $retryPrompt = $userPrompt . "\n\n[RETRY]: Sebelumnya hanya " . count($questions) . " soal yang di-generate. Kali ini WAJIB generate semua {$count} soal.";
            $retryResponse = $this->callAiWithFallback($model, $systemPrompt, $retryPrompt, 0.5, $maxTokens);
            if ($retryResponse['success']) {
                $retryQuestions = $this->parseQuestions($retryResponse['content'], 'pilihan_ganda_kompleks');
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
        $maxTokens = $generateNarasi ? max(2000, $count * 250 + 500) : min($count * 250 + 500, 3000);
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.7, $maxTokens);

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'benar_salah');

        if (count($questions) < $count) {
            $missing = $count - count($questions);
            Log::warning('True/False generator returned ' . count($questions) . "/{$count} questions. Retrying.");
            $retryPrompt = $userPrompt . "\n\n[RETRY]: Sebelumnya hanya " . count($questions) . " soal yang di-generate. Kali ini WAJIB generate semua {$count} soal.";
            $retryResponse = $this->callAiWithFallback($model, $systemPrompt, $retryPrompt, 0.5, $maxTokens);
            if ($retryResponse['success']) {
                $retryQuestions = $this->parseQuestions($retryResponse['content'], 'benar_salah');
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
            Log::warning('Essay generator returned ' . count($questions) . "/{$count} questions. Retrying for {$missing} missing questions.");

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
        $maxTokens = $generateNarasi ? max(2000, $count * 250 + 500) : min($count * 250 + 500, 3000);
        $response = $this->callAiWithFallback($model, $systemPrompt, $userPrompt, 0.7, $maxTokens, false); // false = no json_object mode

        if (!$response['success']) {
            return $response;
        }

        $questions = $this->parseQuestions($response['content'], 'isian_singkat');

        if (count($questions) < $count) {
            $missing = $count - count($questions);
            Log::warning('Fill In Blank generator returned ' . count($questions) . "/{$count} questions. Retrying.");
            $retryPrompt = $userPrompt . "\n\n[RETRY]: Sebelumnya hanya " . count($questions) . " soal yang di-generate. Kali ini WAJIB generate semua {$count} soal.";
            $retryResponse = $this->callAiWithFallback($model, $systemPrompt, $retryPrompt, 0.5, $maxTokens, false);
            if ($retryResponse['success']) {
                $retryQuestions = $this->parseQuestions($retryResponse['content'], 'isian_singkat');
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
                    $altModel = ai_model_cadangan($model);
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

            // AI kadang membalas SATU objek soal langsung (tanpa dibungkus array
            // maupun key pembungkus) - apalagi saat mode json_object Groq aktif.
            // Tanpa penanganan ini, foreach di bawah malah mengulang per-FIELD
            // ("pertanyaan", "tipe_soal", ...) sehingga semua ditolak dan guru
            // hanya melihat "No valid questions generated".
            if (isset($data['pertanyaan'])) {
                $data = [$data];
            }

            // Objek berkunci angka ({"1": {...}, "2": {...}}) -> jadikan list biasa.
            $data = array_values($data);

            // Tiap soal kadang masih dibungkus lagi, mis. [{"soal_1": {...}},
            // {"soal_2": {...}}]. Buka pembungkusnya supaya isinya terbaca.
            $data = array_map(fn ($item) => $this->bukaPembungkusSoal($item), $data);

            // Validate each question
            $validatedQuestions = [];
            foreach ($data as $question) {
                if (!is_array($question)) {
                    Log::warning('Invalid question structure', ['question' => $question]);
                    continue;
                }

                // Rapikan dulu (huruf kunci jadi kapital, opsi kosong dilengkapi)
                // baru divalidasi — supaya soal tidak ditolak hanya gara-gara AI
                // menulis "a" alih-alih "A" atau melewatkan satu opsi.
                $question = $this->normalizeQuestion($question, $expectedType);

                if ($this->validateQuestion($question, $expectedType)) {
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
     * Rapikan satu soal hasil AI sebelum divalidasi/dikirim ke form.
     *
     * AI sering tidak konsisten: kunci ditulis huruf kecil ("a"), ditulis
     * lengkap ("A. Ekonomi pasar"), dikirim sebagai array, atau opsi D/E
     * dilewatkan. Semua itu dirapikan di sini agar soal tidak ditolak dan
     * kunci jawabannya tidak hilang saat disimpan.
     */
    private function normalizeQuestion(array $question, string $type): array
    {
        $kunci = $question['kunci_jawaban'] ?? null;

        // Kunci berupa array → gabung (PGK) / ambil elemen pertama (lainnya).
        if (is_array($kunci)) {
            $kunci = $type === 'pilihan_ganda_kompleks'
                ? implode(',', array_map('strval', $kunci))
                : (string) (reset($kunci) ?: '');
        } elseif (is_bool($kunci)) {
            $kunci = $kunci ? 'benar' : 'salah';
        } elseif ($kunci !== null) {
            $kunci = (string) $kunci;
        }

        if ($type === 'pilihan_ganda' || $type === 'pilihan_ganda_kompleks') {
            // Opsi A–E: kalau AI melewatkan satu, isi string kosong supaya
            // strukturnya tetap utuh (opsi kosong disaring lagi saat disimpan).
            foreach (['a', 'b', 'c', 'd', 'e'] as $huruf) {
                $key = 'pilihan_' . $huruf;
                if (!isset($question[$key]) || $question[$key] === null) {
                    $question[$key] = '';
                } elseif (is_array($question[$key])) {
                    $question[$key] = implode(' ', array_map('strval', $question[$key]));
                } else {
                    $question[$key] = (string) $question[$key];
                }
            }

            // Ambil huruf kuncinya saja lalu KAPITALKAN. Ini yang bikin kunci
            // jawaban sempat hilang: form mencocokkan value "A"–"E", sedangkan
            // AI kerap mengirim "a" / "a. teks jawaban".
            if ($kunci !== null && $kunci !== '') {
                $hurufKunci = [];
                foreach (preg_split('/[,;]+/', $kunci) as $bagian) {
                    if (preg_match('/[A-Ea-e]/', trim($bagian), $m)) {
                        $hurufKunci[] = strtoupper($m[0]);
                    }
                }
                if (!empty($hurufKunci)) {
                    $hurufKunci = array_values(array_unique($hurufKunci));
                    $kunci = $type === 'pilihan_ganda_kompleks'
                        ? implode(',', $hurufKunci)
                        : $hurufKunci[0];
                }
            }
        } elseif ($type === 'benar_salah' && $kunci !== null) {
            // Samakan ragam penulisan: true/1/B/Benar → "benar".
            $k = strtolower(trim($kunci));
            if (in_array($k, ['true', '1', 'b', 'benar'], true)) {
                $kunci = 'benar';
            } elseif (in_array($k, ['false', '0', 's', 'salah'], true)) {
                $kunci = 'salah';
            }
        }

        if ($kunci !== null) {
            $question['kunci_jawaban'] = $kunci;
        }

        // Bobot harus angka; AI kadang mengirim "10 poin".
        if (isset($question['bobot']) && !is_int($question['bobot'])) {
            $bobot = (int) filter_var((string) $question['bobot'], FILTER_SANITIZE_NUMBER_INT);
            $question['bobot'] = $bobot > 0 ? $bobot : 10;
        }

        return $question;
    }

    /**
     * Buka pembungkus di sekitar satu soal.
     *
     * AI kerap membungkus tiap soal dengan key sendiri, mis.
     *   {"soal_1": {"pertanyaan": ...}}  atau  {"question": {...}}
     * Kalau tidak dibuka, isinya tidak terbaca dan soal ditolak validasi.
     * Menelusuri maksimal 3 lapis agar tidak terjebak struktur aneh.
     */
    private function bukaPembungkusSoal($item, int $kedalaman = 0)
    {
        if (!is_array($item) || isset($item['pertanyaan']) || $kedalaman >= 3) {
            return $item;
        }

        // Pembungkus = objek berisi tepat satu anak yang juga objek.
        if (count($item) === 1) {
            $anak = reset($item);
            if (is_array($anak)) {
                return $this->bukaPembungkusSoal($anak, $kedalaman + 1);
            }
        }

        return $item;
    }

    /**
     * Berapa opsi jawaban (A-E) yang benar-benar terisi.
     */
    private function hitungOpsiTerisi(array $question): int
    {
        $jumlah = 0;
        foreach (['a', 'b', 'c', 'd', 'e'] as $huruf) {
            $nilai = $question['pilihan_' . $huruf] ?? '';
            if (is_scalar($nilai) && trim((string) $nilai) !== '') {
                $jumlah++;
            }
        }
        return $jumlah;
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
                // Cukup 3 opsi terisi + kunci yang menunjuk salah satu opsi.
                // Dulu WAJIB kelima opsi A-E ada; kalau AI melewatkan opsi E
                // (sering terjadi pada soal sulit) SEMUA soal ditolak dan guru
                // hanya melihat "No valid questions generated".
                $terisi = $this->hitungOpsiTerisi($question);
                $kunci = strtoupper(trim((string) ($question['kunci_jawaban'] ?? '')));
                $valid = $terisi >= 3
                    && $kunci !== ''
                    && isset($question['pilihan_' . strtolower($kunci)])
                    && trim((string) $question['pilihan_' . strtolower($kunci)]) !== '';

                if (!$valid) {
                    Log::warning('MCQ validation failed', [
                        'opsi_terisi' => $terisi,
                        'kunci_jawaban' => $question['kunci_jawaban'] ?? 'NULL',
                        'question_keys' => array_keys($question),
                    ]);
                }
                return $valid;

            case 'benar_salah':
                $kunci = $question['kunci_jawaban'] ?? null;
                // normalizeQuestion() sudah menyeragamkan jadi "benar"/"salah";
                // sisa nilai lain tetap diterima kalau masih dikenali.
                $valid = is_scalar($kunci)
                    && in_array(strtolower(trim((string) $kunci)), ['benar', 'salah', 'true', 'false'], true);
                if (!$valid) {
                    Log::warning('True/False validation failed', [
                        'kunci_value' => is_scalar($kunci) ? $kunci : gettype($kunci),
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
                // Multi-answer MCQ: minimal 3 opsi terisi + minimal 1 huruf kunci.
                $terisi = $this->hitungOpsiTerisi($question);
                $kunciList = array_filter(array_map(
                    'trim',
                    explode(',', (string) ($question['kunci_jawaban'] ?? ''))
                ));
                $valid = $terisi >= 3 && count($kunciList) >= 1;
                if (!$valid) {
                    Log::warning('Complex MCQ validation failed', [
                        'opsi_terisi' => $terisi,
                        'kunci_jawaban' => $question['kunci_jawaban'] ?? 'NULL',
                    ]);
                }
                return $valid;

            case 'isian_singkat':
                $valid = isset($question['kunci_jawaban'])
                    && is_scalar($question['kunci_jawaban'])
                    && trim((string) $question['kunci_jawaban']) !== '';
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
