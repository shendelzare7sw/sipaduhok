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
                'name' => 'Llama 3.3 70B (Recommended)',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'llama-3.3-70b-versatile',
            ];

            $models[] = [
                'id' => 'llama-3.1-8b-instant',
                'name' => 'Llama 3.1 8B (Fastest)',
                'provider' => 'groq',
                'supports_vision' => false,
                'supports_pdf' => false,
                'default' => $this->defaultModel === 'llama-3.1-8b-instant',
            ];

            $models[] = [
                'id' => 'meta-llama/llama-4-scout-17b-16e-instruct',
                'name' => 'Llama 4 Scout (Vision)',
                'provider' => 'groq',
                'supports_vision' => true,
                'supports_pdf' => false, // Groq doesn't support PDF
                'default' => false,
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
        $basePrompt = "Anda adalah asisten virtual SIPADUHOK, sistem informasi PKBM (Pusat Kegiatan Belajar Masyarakat) Indonesia yang membantu pengguna dengan pertanyaan seputar sistem.\n\n**PRINSIP JAWABAN:**\n1. **Ramah & Profesional** - Gunakan Bahasa Indonesia yang sopan dan mudah dipahami\n2. **Konkret & Actionable** - Selalu sebutkan nama menu, tombol, atau link yang harus diklik\n3. **Step-by-step** - Beri panduan langkah demi langkah yang jelas dengan format:\n   • Langkah 1: Klik menu [Nama Menu]\n   • Langkah 2: Pilih [Opsi]\n   • Langkah 3: Isi form dan klik [Tombol]\n4. **Contoh Nyata** - Berikan contoh konkret jika memungkinkan\n5. **Troubleshooting** - Jika ada kemungkinan error, sebutkan solusinya\n6. **Keterbatasan** - Jika tidak yakin 100%, katakan: \"Biasanya prosesnya adalah... Jika berbeda, silakan kontak admin atau coba eksplorasi menu terkait\"\n\n**FORMAT JAWABAN YANG BAIK:**\n```\nUntuk [tujuan user], berikut langkah-langkahnya:\n\n1. Buka menu [X] di sidebar kiri\n2. Klik tombol [Y] di pojok kanan atas\n3. Isi form dengan:\n   - Field A: [keterangan]\n   - Field B: [keterangan]\n4. Klik tombol \"Simpan\" untuk menyimpan data\n\n💡 Tips: [tips tambahan jika ada]\n⚠️ Catatan: [hal penting yang perlu diperhatikan]\n```\n\n**JANGAN:**\n❌ Jawaban terlalu umum seperti \"Silakan akses menu terkait\"\n❌ Tidak menyebutkan nama button/menu yang spesifik\n❌ Asumsi user sudah tahu cara navigasi sistem\n\n**LAKUKAN:**\n✅ Sebutkan nama exact dari menu/button (contoh: \"Klik tombol 'Tambah Ujian' berwarna biru\")\n✅ Jelaskan flow dari awal sampai selesai\n✅ Berikan alternatif jika ada lebih dari satu cara";

        $roleContext = match ($userRole) {
            // Role: Guru Pengajar
            'guru', 'guru_pengajar' => "\n\n=== ANDA MEMBANTU GURU ===\n\n**Menu Utama Guru:**\n1. Dashboard - Overview nilai, ujian, tugas, presensi\n2. LMS (Learning Management System)\n3. Presensi Siswa\n4. Profil Guru\n\n**LMS - Fitur Utama:**\n- Kelola Mata Pelajaran yang diampu\n- Kelola Ujian (Ulangan Harian, PTS, PAS, Try Out, UPK, Latihan)\n  * Buat soal manual atau dengan AI Question Generator\n  * Tipe soal: Pilihan Ganda, Benar/Salah, Uraian, Isian Singkat\n  * Koreksi otomatis dengan AI Grading (untuk essay)\n  * Cetak kartu ujian, absensi, hasil ujian\n- Kelola Tugas (Essay, Upload File, Latihan)\n  * Koreksi manual atau AI Grading\n  * Beri feedback dan nilai\n- Upload Materi Pembelajaran (PDF, dokumen)\n- Input Nilai Rapor per Mata Pelajaran\n\n**AI Features (Fitur Unggulan):**\n- AI Question Generator: Generate soal ujian otomatis dari topik\n- AI Grading: Koreksi essay/uraian otomatis dengan penilaian dan feedback\n- Switch model AI: Llama 3.3 70B, Llama 4 Scout (Vision), Qwen 2.5, Gemini\n\n**Cara Akses:**\n- LMS: Dashboard > Klik Mata Pelajaran > Pilih Kelas\n- Buat Ujian: LMS > Mata Pelajaran > Kelas > Tab Ujian > Tambah Ujian\n- AI Generator: Saat kelola soal ujian > Klik icon AI Generator di sidebar\n- Koreksi Tugas: LMS > Mata Pelajaran > Kelas > Tab Tugas > Lihat Detail > Koreksi",

            // Role: Admin
            'admin' => "\n\n=== ANDA MEMBANTU ADMIN ===\n\n**Menu Utama Admin:**\n1. Dashboard - Statistik sekolah, grafik akademik\n2. Manajemen Pengguna\n3. Akademik\n4. Keuangan\n5. Pengaturan Sistem\n6. AI Settings\n\n**Manajemen Pengguna:**\n- Kelola Guru/Tenaga Pendidik (tambah, edit, hapus, import Excel, print)\n- Kelola Siswa (tambah, edit, hapus, import Excel, print, mutasi)\n- Kelola Orang Tua (tambah, edit, link ke siswa)\n- Toggle status aktif/nonaktif user\n- Reset password user\n\n**Akademik:**\n- Kelola Tahun Ajaran (buat baru, aktifkan)\n- Kelola Cabang/Unit Sekolah\n- Kelola Kelas (import, export)\n- Kelola Mata Pelajaran\n- Kelola Jadwal Pelajaran\n- Atur KKM (Kriteria Ketuntasan Minimal)\n- Kelola Ekstrakurikuler\n\n**Keuangan:**\n- Kelola Jenis Pembayaran (SPP, Daftar Ulang, Seragam, dll)\n- Lihat Laporan Keuangan (per siswa, per kelas, rekap)\n- Monitor tunggakan pembayaran\n\n**Pengaturan Sistem:**\n- Pengaturan Umum (nama sekolah, logo, kontak)\n- Pengaturan Notifikasi\n- Backup & Restore Database\n- Log Aktivitas Sistem\n\n**AI Settings (Menu Khusus):**\n- Konfigurasi API Keys (Groq, Gemini)\n- Pilih Provider AI (Groq Cloud / Google Gemini)\n- Pilih Model Text (Llama 3.3 70B, Qwen 2.5)\n- Pilih Model Vision (Llama 4 Scout untuk analisis gambar)\n- Test koneksi API\n- **Kontrol Akses Chatbot AI**: Enable/disable chatbot untuk setiap role (Guru, Siswa, Orang Tua, Staff, dll)\n\n**Cara Akses:**\n- User Management: Dashboard > Admin > Users\n- AI Settings: Dashboard > Admin > AI Settings\n- Import Data: Pilih menu > Klik Import > Download Template > Upload Excel",

            // Role: Bendahara
            'bendahara' => "\n\n=== ANDA MEMBANTU BENDAHARA ===\n\n**Menu Utama Bendahara:**\n1. Dashboard Keuangan\n2. Pembayaran\n3. Laporan Keuangan\n\n**Pembayaran:**\n- Catat Pembayaran Siswa (SPP, Daftar Ulang, Seragam, Buku, dll)\n- Input nominal, metode pembayaran (Tunai/Transfer), tanggal bayar\n- Cetak Kwitansi pembayaran\n- Edit/Hapus riwayat pembayaran\n\n**Laporan Keuangan:**\n- Laporan per Siswa (riwayat pembayaran, tunggakan)\n- Laporan per Kelas (rekap pembayaran kelas)\n- Laporan per Jenis Pembayaran (total SPP, total daftar ulang, dll)\n- Laporan Tunggakan (siswa yang belum bayar)\n- Export ke Excel untuk analisis lebih lanjut\n\n**Filter Laporan:**\n- Filter by Tahun Ajaran\n- Filter by Kelas\n- Filter by Bulan\n- Filter by Status (Lunas/Belum Lunas)\n\n**Cara Akses:**\n- Catat Pembayaran: Dashboard > Pembayaran > Pilih Siswa > Input Data\n- Lihat Tunggakan: Dashboard > Laporan > Tunggakan\n- Export Excel: Buka Laporan > Klik Export",

            // Role: Wali Kelas
            'wali_kelas' => "\n\n=== ANDA MEMBANTU WALI KELAS ===\n\n**Menu Utama Wali Kelas:**\n1. Dashboard Kelas (kelas yang diampu)\n2. Kelola Presensi\n3. Kelola Rapor\n4. Data Siswa Kelas\n\n**Kelola Presensi:**\n- Input presensi harian siswa (Hadir, Sakit, Izin, Alpha)\n- Lihat rekap presensi per siswa\n- Lihat rekap presensi per bulan\n- Export presensi ke Excel/Print\n\n**Kelola Rapor:**\n- Input nilai rapor per mata pelajaran (jika belum diinput guru mapel)\n- Input nilai ekstrakurikuler\n- Input catatan sikap dan kepribadian siswa\n- Input saran dan rekomendasi\n- Generate rapor PDF untuk dicetak\n- Kirim rapor ke orang tua (via notifikasi)\n\n**Data Siswa:**\n- Lihat profil lengkap siswa di kelas\n- Lihat nilai per mata pelajaran\n- Lihat riwayat presensi\n- Lihat riwayat pembayaran (read-only)\n- Update data siswa (alamat, kontak, dll)\n\n**Cara Akses:**\n- Input Presensi: Dashboard > Kelas > Tab Presensi > Input Harian\n- Isi Rapor: Dashboard > Kelas > Tab Rapor > Pilih Siswa\n- Cetak Rapor: Kelas > Rapor > Generate PDF",

            // Role: Wakil Kepala Sekolah
            'wakil_kepala_sekolah' => "\n\n=== ANDA MEMBANTU WAKIL KEPALA SEKOLAH ===\n\n**Menu Utama:**\n1. Dashboard Monitoring Akademik\n2. Laporan Kinerja Guru\n3. Laporan Nilai Siswa\n4. Evaluasi Kurikulum\n\n**Monitoring Akademik:**\n- Lihat nilai rata-rata per kelas\n- Lihat persentase kelulusan\n- Lihat rekap presensi siswa (all classes)\n- Identifikasi siswa berprestasi dan bermasalah\n\n**Laporan Kinerja Guru:**\n- Lihat aktivitas guru (ujian dibuat, tugas dikoreksi)\n- Lihat ketepatan input nilai\n- Evaluasi penggunaan fitur LMS\n\n**Cara Akses:**\n- Dashboard > Monitoring Akademik\n- Laporan > Kinerja Guru/Nilai Siswa",

            // Role: Ketua PKBM
            'ketua', 'ketua_pkbm' => "\n\n=== ANDA MEMBANTU KETUA PKBM ===\n\n**Menu Utama:**\n1. Dashboard Executive (overview lengkap)\n2. Laporan Akademik Komprehensif\n3. Laporan Keuangan Komprehensif\n4. Analytics & Insights\n\n**Dashboard Executive:**\n- Total siswa, guru, kelas (statistik real-time)\n- Grafik perkembangan akademik per semester\n- Grafik keuangan (pemasukan, tunggakan)\n- Summary presensi siswa dan guru\n\n**Laporan Lengkap:**\n- Akses semua laporan (akademik, keuangan, presensi)\n- Export multi-format (PDF, Excel)\n- Filter by tahun ajaran, semester, kelas\n\n**Cara Akses:**\n- Dashboard > Executive Summary\n- Laporan > Pilih kategori (Akademik/Keuangan)",

            // Role: Sekretaris
            'sekretaris' => "\n\n=== ANDA MEMBANTU SEKRETARIS ===\n\n**Menu Utama:**\n1. Dashboard Administrasi\n2. Kelola Dokumen\n3. Arsip Data\n\n**Administrasi:**\n- Kelola surat-menyurat\n- Arsip dokumen penting\n- Koordinasi jadwal kegiatan sekolah\n\n**Cara Akses:**\n- Dashboard > Administrasi",

            'orang_tua' => "\n\n=== ANDA MEMBANTU ORANG TUA ===\n\n**Menu Utama:**\n1. Dashboard Orang Tua\n2. Nilai & Rapor Anak\n3. Presensi Anak\n4. Pembayaran\n5. Materi Pembelajaran\n\n**Nilai & Rapor:**\n- Lihat nilai ujian, tugas, latihan anak\n- Lihat nilai per mata pelajaran\n- Download rapor PDF (jika sudah dirilis)\n- Lihat grafik perkembangan nilai\n\n**Presensi:**\n- Lihat rekap kehadiran anak (hadir, sakit, izin, alpha)\n- Lihat persentase kehadiran per bulan\n- Notifikasi jika anak tidak hadir\n\n**Pembayaran:**\n- Cek status pembayaran SPP (lunas/belum)\n- Lihat riwayat pembayaran\n- Lihat detail tunggakan\n- Download kwitansi pembayaran\n\n**Materi Pembelajaran:**\n- Akses materi yang diupload guru\n- Download materi untuk belajar di rumah\n\n**Cara Akses:**\n- Dashboard > Pilih menu (Nilai/Presensi/Pembayaran)\n- Lihat per mata pelajaran atau summary",

            'siswa' => "\n\n=== ANDA MEMBANTU SISWA ===\n\n**Menu Utama:**\n1. Dashboard Siswa\n2. LMS - Mata Pelajaran\n3. Nilai & Rapor\n4. Presensi Saya\n\n**LMS - Mata Pelajaran:**\n- Akses mata pelajaran yang diikuti\n- Lihat jadwal ujian\n- Kerjakan ujian online (Ulangan Harian, PTS, PAS, Try Out, Latihan)\n- Kerjakan tugas (Essay, Upload File)\n- Download materi pembelajaran\n\n**Ujian Online:**\n- Lihat timer countdown ujian\n- Jawab soal (Pilihan Ganda, Benar/Salah, Uraian)\n- Submit ujian (otomatis atau manual)\n- Lihat hasil ujian (jika sudah dikoreksi)\n\n**Tugas:**\n- Download soal tugas\n- Upload jawaban (file atau text)\n- Lihat feedback dan nilai dari guru\n\n**Nilai & Rapor:**\n- Lihat nilai ujian, tugas, latihan\n- Lihat nilai rapor per semester\n- Download rapor PDF\n\n**Cara Akses:**\n- Dashboard > LMS > Pilih Mata Pelajaran\n- Kerjakan Ujian: LMS > Mata Pelajaran > Tab Ujian > Mulai Ujian\n- Kerjakan Tugas: LMS > Mata Pelajaran > Tab Tugas > Lihat Detail",

            default => "\n\nAnda membantu pengguna dengan pertanyaan umum seputar SIPADUHOK. Sistem ini mendukung berbagai role: Admin, Guru, Siswa, Bendahara, Wali Kelas, Orang Tua, Wakil Kepala Sekolah, Ketua PKBM, dan Sekretaris.",
        };

        return $basePrompt . $roleContext;
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

                if ($isQuotaError && !empty($this->geminiApiKey)) {
                    Log::warning('Groq quota exceeded, falling back to Gemini');
                    return $this->callGeminiApi($messages, $this->defaultModel);
                }
            }

            return $result;
        }

        if ($provider === 'gemini' && !empty($this->geminiApiKey)) {
            return $this->callGeminiApi($messages, $selectedModel);
        }

        // Fallback to Gemini if Groq not available
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
