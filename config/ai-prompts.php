<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Question Generator Prompts
    |--------------------------------------------------------------------------
    |
    | Prompts untuk AI Question Bank Generator
    | Support: Pilihan Ganda, Benar/Salah, Uraian, Isian Singkat
    | Jenjang: SD (1-6), SMP (7-9), SMA (10-12)
    |
    */

    'question_generator' => [

        // PILIHAN GANDA (MCQ)
        'pilihan_ganda' => [
            'system' => 'Anda adalah generator soal ujian profesional untuk siswa Indonesia. Anda ahli membuat soal pilihan ganda berkualitas tinggi sesuai kurikulum K13 dan Kurikulum Merdeka.',

            'prompt' => 'Tugas: Buat {count} soal pilihan ganda berkualitas tinggi.

Context:
- Topik/Materi: {topic}
- Mata Pelajaran: {subject}
- Tingkat Kesulitan: {difficulty}
- Jenjang Pendidikan: {jenjang} Kelas {kelas}
- Jumlah Soal: {count}

Pedoman Sesuai Jenjang:
{jenjang_guidelines}

Instruksi Pembuatan Soal:
1. Pertanyaan HARUS jelas dan sesuai tingkat kognitif {jenjang}
2. 5 pilihan jawaban (A, B, C, D, E) - hanya 1 yang benar
3. Distractor (pilihan salah) harus:
   - Masuk akal dan menantang (bukan obviously wrong)
   - Mencerminkan misconception umum siswa
   - Sesuai level pemahaman {jenjang}
4. HINDARI:
   - "Semua jawaban benar" atau "Tidak ada yang benar"
   - Pilihan yang terlalu mudah ditebak
   - Bahasa yang terlalu tinggi/rendah untuk {jenjang}
5. Tingkat Kesulitan:
   - Mudah: Fakta dasar, definisi, hafalan (C1-C2 Bloom)
   - Sedang: Aplikasi konsep, perhitungan sederhana (C3-C4 Bloom)
   - Sulit: Analisis, evaluasi, problem solving (C5-C6 Bloom)

Contoh Soal Berkualitas untuk {jenjang}:
{example_question}

Output JSON array (PENTING: strict JSON format, no markdown):
[
  {
    "pertanyaan": "Teks pertanyaan lengkap (gunakan bahasa sesuai {jenjang})",
    "tipe_soal": "pilihan_ganda",
    "pilihan_a": "Teks pilihan A",
    "pilihan_b": "Teks pilihan B",
    "pilihan_c": "Teks pilihan C",
    "pilihan_d": "Teks pilihan D",
    "pilihan_e": "Teks pilihan E",
    "kunci_jawaban": "A",
    "bobot": 10,
    "penjelasan": "Penjelasan singkat mengapa kunci jawaban benar"
  }
]

Generate {count} soal sekarang untuk topik "{topic}".',
        ],

        // BENAR / SALAH (TRUE/FALSE)
        'benar_salah' => [
            'system' => 'Anda adalah generator soal Benar/Salah profesional untuk siswa Indonesia.',

            'prompt' => 'Tugas: Buat {count} soal Benar/Salah berkualitas tinggi.

Context:
- Topik/Materi: {topic}
- Mata Pelajaran: {subject}
- Jenjang Pendidikan: {jenjang} Kelas {kelas}
- Jumlah Soal: {count}

Pedoman Sesuai Jenjang:
{jenjang_guidelines}

Instruksi Pembuatan Soal:
1. Pernyataan HARUS jelas dan definitif (benar ATAU salah, tidak ambigu)
2. Sesuai tingkat kognitif {jenjang}
3. Hindari:
   - Kata "selalu", "tidak pernah" yang membuat obvious
   - Double negative yang membingungkan
   - Pernyataan yang terlalu kompleks untuk {jenjang}
4. Berikan penjelasan singkat untuk key learning point

Contoh Soal untuk {jenjang}:
{example_question}

Output JSON array (PENTING: strict JSON, no markdown):
[
  {
    "pertanyaan": "Pernyataan yang jelas dan definitif",
    "tipe_soal": "benar_salah",
    "kunci_jawaban": "benar",
    "bobot": 5,
    "penjelasan": "Penjelasan mengapa pernyataan benar/salah"
  }
]

Generate {count} soal sekarang untuk topik "{topic}".',
        ],

        // URAIAN / ESSAY
        'uraian' => [
            'system' => 'Anda adalah generator soal uraian/essay profesional untuk siswa Indonesia. Anda ahli membuat pertanyaan yang mendorong critical thinking sesuai jenjang pendidikan.',

            'prompt' => 'Tugas: Buat {count} soal uraian/essay berkualitas tinggi.

Context:
- Topik/Materi: {topic}
- Mata Pelajaran: {subject}
- Tingkat Kesulitan: {difficulty}
- Jenjang Pendidikan: {jenjang} Kelas {kelas}
- Jumlah Soal: {count}

Pedoman Sesuai Jenjang:
{jenjang_guidelines}

Instruksi Pembuatan Soal:
1. Pertanyaan harus mendorong {difficulty_verb}:
   - Mudah: Menjelaskan, menyebutkan, mendeskripsikan (C2)
   - Sedang: Mengaplikasikan, membandingkan, menganalisis (C3-C4)
   - Sulit: Mengevaluasi, mencipta, memecahkan masalah kompleks (C5-C6)
2. Sesuai kemampuan menulis {jenjang}:
   - SD: Jawaban 3-5 kalimat
   - SMP: Jawaban 1 paragraf (5-8 kalimat)
   - SMA: Jawaban 2-3 paragraf dengan argumentasi
3. Sertakan rubrik penilaian yang jelas
4. Gunakan bahasa yang sesuai {jenjang}

Contoh Soal Essay untuk {jenjang}:
{example_question}

Output JSON array (PENTING: strict JSON, no markdown):
[
  {
    "pertanyaan": "Pertanyaan essay yang mendorong critical thinking",
    "tipe_soal": "uraian",
    "bobot": 15,
    "rubrik_penilaian": "Rubrik penilaian dalam format:\n- Aspek 1: (X poin)\n- Aspek 2: (Y poin)\n- Aspek 3: (Z poin)",
    "contoh_jawaban": "Contoh jawaban ideal untuk referensi guru (opsional)"
  }
]

Generate {count} soal sekarang untuk topik "{topic}".',
        ],

        // ISIAN SINGKAT
        'isian_singkat' => [
            'system' => 'Anda adalah generator soal isian singkat profesional untuk siswa Indonesia.',

            'prompt' => 'Tugas: Buat {count} soal isian singkat berkualitas tinggi.

Context:
- Topik/Materi: {topic}
- Mata Pelajaran: {subject}
- Jenjang Pendidikan: {jenjang} Kelas {kelas}
- Jumlah Soal: {count}

Pedoman Sesuai Jenjang:
{jenjang_guidelines}

Instruksi Pembuatan Soal:
1. Jawaban harus SPESIFIK dan SINGKAT (1-3 kata, atau 1 angka)
2. Pertanyaan harus mengarah ke 1 jawaban benar (tidak ambigu)
3. Sesuai level {jenjang}:
   - SD: Jawaban sederhana (nama, angka, istilah dasar)
   - SMP: Istilah teknis sederhana, rumus
   - SMA: Istilah kompleks, konsep spesifik
4. Hindari pertanyaan yang terlalu terbuka

Contoh Soal Isian untuk {jenjang}:
{example_question}

Output JSON array (PENTING: strict JSON, no markdown):
[
  {
    "pertanyaan": "Pertanyaan dengan blank _____ atau format isian",
    "tipe_soal": "isian_singkat",
    "kunci_jawaban": "jawaban singkat (1-3 kata)",
    "alternatif_jawaban": ["sinonim1", "sinonim2"],
    "bobot": 5,
    "penjelasan": "Penjelasan singkat"
  }
]

Generate {count} soal sekarang untuk topik "{topic}".',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Jenjang Education Guidelines
    |--------------------------------------------------------------------------
    |
    | Pedoman pembuatan soal sesuai jenjang pendidikan
    |
    */

    'jenjang_guidelines' => [
        'SD' => [
            'name' => 'Sekolah Dasar',
            'age_range' => '6-12 tahun',
            'guidelines' => '- Gunakan bahasa sederhana, kalimat pendek (max 15 kata)
- Hindari istilah teknis yang kompleks
- Fokus pada konsep konkret dan faktual
- Gunakan contoh dari kehidupan sehari-hari
- Untuk kelas 1-3: Pertanyaan sangat sederhana dengan gambar mental
- Untuk kelas 4-6: Mulai aplikasi konsep sederhana',

            'example_mcq' => 'Soal: Budi membeli 5 apel. Ia memberikan 2 apel kepada adiknya. Berapa apel yang tersisa?
A. 3 ✓ (benar - 5 - 2 = 3)
B. 2 (salah - hanya jumlah yang diberikan)
C. 5 (salah - jumlah awal)
D. 7 (salah - salah operasi, dijumlah)
E. 10 (salah - dikali)',

            'example_essay' => 'Soal: Jelaskan mengapa kita harus menjaga kebersihan lingkungan! Sebutkan 3 alasan! (Bobot: 10 poin)

Rubrik:
- Menyebutkan 3 alasan yang benar (6 poin, @2 poin/alasan)
- Kalimat jelas dan mudah dipahami (2 poin)
- Penulisan rapi (2 poin)',

            'example_isian' => 'Soal: Ibu kota Indonesia adalah _____
Jawaban: Jakarta',
        ],

        'SMP' => [
            'name' => 'Sekolah Menengah Pertama',
            'age_range' => '12-15 tahun',
            'guidelines' => '- Gunakan bahasa semi-formal, kalimat menengah (max 25 kata)
- Istilah teknis sederhana boleh digunakan
- Fokus pada aplikasi konsep dan analisis sederhana
- Mulai gunakan soal yang butuh reasoning 2-3 langkah
- Encourage critical thinking tingkat menengah',

            'example_mcq' => 'Soal: Jika persamaan 2x + 5 = 13, maka nilai x adalah...
A. 2 (salah - lupa bagi 2)
B. 4 ✓ (benar - (13-5)/2 = 4)
C. 8 (salah - dikali 2)
D. 9 (salah - hanya dikurangi 5)
E. 18 (salah - salah operasi)',

            'example_essay' => 'Soal: Jelaskan proses fotosintesis pada tumbuhan! Sebutkan:
1. Bahan-bahan yang dibutuhkan (3 poin)
2. Tempat terjadinya (2 poin)
3. Hasil fotosintesis (3 poin)
4. Manfaat bagi manusia (2 poin)
(Total: 10 poin)',

            'example_isian' => 'Soal: Rumus kimia air adalah _____
Jawaban: H₂O (atau H2O)',
        ],

        'SMA' => [
            'name' => 'Sekolah Menengah Atas',
            'age_range' => '15-18 tahun',
            'guidelines' => '- Gunakan bahasa formal dan akademis
- Istilah teknis dan konsep abstrak diperbolehkan
- Fokus pada analisis mendalam, evaluasi, dan sintesis
- Soal multi-step reasoning (3-5 langkah)
- Encourage argumentation dan critical analysis',

            'example_mcq' => 'Soal: Jika persamaan kuadrat x² - 5x + k = 0 memiliki akar kembar, maka nilai k adalah...
A. 2,5 (salah - setengah dari b)
B. 5 (salah - nilai b)
C. 6,25 ✓ (benar - diskriminan = 0, b²/4 = 25/4 = 6,25)
D. 10 (salah - dikali 2)
E. 25 (salah - hanya b²)',

            'example_essay' => 'Soal: Analisis dampak Revolusi Industri 4.0 terhadap pasar tenaga kerja di Indonesia!
Jawaban harus mencakup:
1. Definisi Revolusi Industri 4.0 (3 poin)
2. Minimal 3 dampak positif dengan contoh (6 poin)
3. Minimal 3 dampak negatif dengan contoh (6 poin)
4. Solusi untuk menghadapi tantangan (5 poin)
(Total: 20 poin, jawaban minimal 2-3 paragraf)',

            'example_isian' => 'Soal: Teori yang menyatakan bahwa cahaya memiliki sifat partikel DAN gelombang disebut teori _____
Jawaban: dualisme gelombang-partikel (atau dualitas)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Difficulty Verbs untuk Essay
    |--------------------------------------------------------------------------
    */

    'difficulty_verbs' => [
        'easy' => 'menjelaskan, menyebutkan, mendeskripsikan, mendefinisikan',
        'medium' => 'mengaplikasikan, membandingkan, menganalisis, mengklasifikasikan',
        'hard' => 'mengevaluasi, mencipta, memecahkan masalah kompleks, mengkritisi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Student Progress Insights Prompts
    |--------------------------------------------------------------------------
    |
    | Prompts untuk AI Student Analytics (Feature 2)
    |
    */

    'student_insights' => [
        'analyze_progress' => [
            'system' => 'Anda adalah sistem analisis akademik AI untuk sekolah. Anda ahli mengidentifikasi pola performa siswa dan memberikan rekomendasi intervensi yang actionable.',

            'prompt' => 'Anda adalah sistem analisis akademik AI untuk sekolah.

Data {student_count} siswa dari Kelas {kelas_name}:

{student_data}

Tugas Analisis:

1. **Risk Level Classification** (untuk setiap siswa):
   - HIGH: Nilai turun >10 poin ATAU avg <60 ATAU alpha >7 hari
   - MEDIUM: Nilai turun 5-10 poin ATAU avg 60-70 ATAU alpha 3-7 hari
   - LOW: Stabil atau improving

2. **Pattern Identification**:
   - declining (nilai turun konsisten)
   - struggling (nilai rendah tapi stabil)
   - attendance (alpha tinggi)
   - subject_specific (1-2 mapel drop signifikan)
   - improving (progress positif)

3. **Specific Recommendations** (2-4 rekomendasi per siswa HIGH/MEDIUM risk):
   - Contact parents (urgent jika HIGH risk)
   - Assign peer tutor (untuk mapel specific)
   - Review specific chapter/topic
   - Attendance monitoring
   - Counseling (jika multiple issues)

4. **Parent Notification Draft** (jika risk HIGH atau MEDIUM):
   - Tone: Profesional, supportive, tidak menghakimi
   - Structure: Greeting → Issue → Data → Call to action → Closing
   - Bahasa Indonesia formal tapi hangat

Output JSON array (PENTING: strict JSON, no markdown):
[
  {
    "siswa_id": int,
    "nama": "string",
    "risk_level": "high" | "medium" | "low",
    "risk_category": "declining" | "struggling" | "attendance" | "subject_specific" | "improving",
    "current_avg": float,
    "prev_avg": float,
    "decline_points": int,
    "alpha_days": int,
    "attendance_rate": int,
    "struggling_subjects": ["Mapel 1", "Mapel 2"],
    "analysis": "Analisis singkat 2-3 kalimat",
    "recommendations": ["Rec 1", "Rec 2", "Rec 3"],
    "parent_notification_draft": "Draft surat untuk wali siswa (atau null jika LOW risk)"
  }
]

Analisis sekarang.',
        ],
    ],
];
