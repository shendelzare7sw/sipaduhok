<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalUjian extends Model
{
    use HasFactory;

    protected $table = 'soal_ujian';

    protected $fillable = [
        'ujian_id',
        'narasi',
        'urutan',
        'image_path',
        'pertanyaan',
        'tipe_soal',
        'jumlah_pilihan',
        'pilihan_jawaban',
        'jawaban_benar',
        'kunci_jawaban',
        'bobot_nilai',
    ];

    protected $casts = [
        'pilihan_jawaban' => 'array',
        'bobot_nilai' => 'integer',
    ];

    /**
     * Sembunyikan kunci jawaban dari serialisasi array/JSON (mis. @json, toArray, response()->json).
     * Tidak memengaruhi akses properti PHP, jadi grading & UI guru (property access) tetap jalan.
     * Untuk audiens yang berhak (guru), pakai ->makeVisible([...]) secara eksplisit.
     */
    protected $hidden = [
        'kunci_jawaban',
        'jawaban_benar',
    ];

    // Tipe Soal Constants
    const TIPE_PILGAN = 'pilihan_ganda';
    const TIPE_PILGAN_KOMPLEKS = 'pilihan_ganda_kompleks';
    const TIPE_BENAR_SALAH = 'benar_salah';
    const TIPE_ISIAN = 'isian_singkat';
    const TIPE_URAIAN = 'uraian';
    const TIPE_ESSAY = 'essay';

    // Relationships
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    /**
     * Versi pilihan_jawaban yang AMAN dikirim ke siswa.
     * Membuang semua info kunci jawaban yang secara struktur ikut tersimpan di pilihan_jawaban:
     *  - key 'jawaban_benar' (pilihan ganda kompleks & isian singkat)
     *  - flag 'benar' pada tiap item 'pernyataan' (benar/salah)
     * Sumber tunggal opsi aman-siswa: view/JS jangan pakai pilihan_jawaban mentah.
     */
    public function pilihanJawabanForSiswa(): array
    {
        $data = $this->pilihan_jawaban;
        if (!is_array($data)) {
            $data = json_decode($data ?? '[]', true) ?: [];
        }

        // Pilihan ganda kompleks & isian singkat menyimpan jawaban di key ini.
        unset($data['jawaban_benar']);

        // Benar/salah: sisakan hanya teks pernyataan, buang flag 'benar'.
        if (isset($data['pernyataan']) && is_array($data['pernyataan'])) {
            $data['pernyataan'] = array_map(function ($item) {
                if (is_array($item)) {
                    unset($item['benar']);
                }
                return $item;
            }, $data['pernyataan']);
        }

        return $data;
    }

    /**
     * Check jawaban untuk semua tipe soal
     * Return: true (benar), false (salah), null (perlu koreksi manual)
     */
    public function checkAnswer($jawaban)
    {
        switch ($this->tipe_soal) {
            case self::TIPE_PILGAN:
                return $this->checkPilihanGanda($jawaban);

            case self::TIPE_PILGAN_KOMPLEKS:
                return $this->checkPilihanGandaKompleks($jawaban);

            case self::TIPE_BENAR_SALAH:
                return $this->checkBenarSalah($jawaban);

            case self::TIPE_ISIAN:
                return $this->checkIsianSingkat($jawaban);

            case self::TIPE_URAIAN:
            case self::TIPE_ESSAY:
                return null; // Perlu koreksi manual

            default:
                return null;
        }
    }

    /**
     * Pilihan Ganda biasa (1 jawaban benar)
     */
    protected function checkPilihanGanda($jawaban)
    {
        return strtoupper(trim($jawaban)) === strtoupper(trim($this->kunci_jawaban));
    }

    /**
     * Pilihan Ganda Kompleks (multiple jawaban benar)
     * Format pilihan_jawaban: {"options": ["A", "B", "C", "D"], "jawaban_benar": ["A", "C"]}
     */
    protected function checkPilihanGandaKompleks($jawaban)
    {
        if (!is_array($jawaban)) {
            $jawaban = json_decode($jawaban, true) ?? [];
        }

        $jawabanBenar = $this->kunci_jawaban;
        if (!is_array($jawabanBenar)) {
            $jawabanBenar = json_decode($jawabanBenar, true) ?? [];
        }

        // Normalize to uppercase
        $jawaban = array_map('strtoupper', array_map('trim', $jawaban));
        $jawabanBenar = array_map('strtoupper', array_map('trim', $jawabanBenar));

        sort($jawaban);
        sort($jawabanBenar);

        return $jawaban === $jawabanBenar;
    }

    /**
     * Benar/Salah (tabel pernyataan)
     * Format pilihan_jawaban: {"pernyataan": [{"text": "...", "benar": true}, ...]}
     * Format jawaban: [true, false, true, true, false]
     */
    protected function checkBenarSalah($jawaban)
    {
        if (!is_array($jawaban)) {
            $jawaban = json_decode($jawaban, true) ?? [];
        }

        $pilihanData = $this->pilihan_jawaban;
        $pernyataan = $pilihanData['pernyataan'] ?? [];

        if (count($jawaban) !== count($pernyataan)) {
            return false;
        }

        $benar = 0;
        $total = count($pernyataan);

        foreach ($pernyataan as $index => $item) {
            $jawabanBenar = $item['benar'] ?? false;
            $jawabanSiswa = $jawaban[$index] ?? false;

            // Convert string "true"/"false" to boolean
            if (is_string($jawabanSiswa)) {
                $jawabanSiswa = filter_var($jawabanSiswa, FILTER_VALIDATE_BOOLEAN);
            }

            if ($jawabanSiswa === $jawabanBenar) {
                $benar++;
            }
        }

        // Return score as percentage (can also return true/false if perfect)
        return $benar === $total;
    }

    /**
     * Isian Singkat (case-insensitive matching)
     * Format pilihan_jawaban: {"jawaban_benar": ["sel", "Sel", "SEL"]}
     */
    protected function checkIsianSingkat($jawaban)
    {
        $jawaban = strtolower(trim($jawaban));

        // Check from pilihan_jawaban first
        $pilihanData = $this->pilihan_jawaban;
        if (isset($pilihanData['jawaban_benar']) && is_array($pilihanData['jawaban_benar'])) {
            foreach ($pilihanData['jawaban_benar'] as $benar) {
                if (strtolower(trim($benar)) === $jawaban) {
                    return true;
                }
            }
        }

        // Fallback to kunci_jawaban column
        if ($this->kunci_jawaban) {
            return strtolower(trim($this->kunci_jawaban)) === $jawaban;
        }

        return false;
    }

    /**
     * Calculate partial score for benar_salah type
     */
    public function calculatePartialScore($jawaban)
    {
        // 1. Partial Scoring untuk Pilihan Ganda Kompleks
        if ($this->tipe_soal === self::TIPE_PILGAN_KOMPLEKS) {
            if (!is_array($jawaban)) {
                $jawaban = json_decode($jawaban, true) ?? [];
            }
            $pilihanData = $this->pilihan_jawaban;
            $kunciBenar = $this->kunci_jawaban;
            if (!is_array($kunciBenar)) {
                $kunciBenar = json_decode($kunciBenar, true) ?? [];
            }
            
            // Collect valid options based on keys A, B, C, D, E that have non-empty text
            $availableOptions = [];
            foreach (['A', 'B', 'C', 'D', 'E'] as $letter) {
                if (isset($pilihanData[$letter]) && $pilihanData[$letter] !== '') {
                    $availableOptions[] = $letter;
                }
            }

            if (empty($kunciBenar) || empty($availableOptions)) return 0;

            // Normalize choices to uppercase and trim
            $jawaban = array_map('strtoupper', array_map('trim', $jawaban));
            $kunciBenar = array_map('strtoupper', array_map('trim', $kunciBenar));

            $jumlahKunciBenar = count($kunciBenar);
            $jumlahKunciSalah = count($availableOptions) - $jumlahKunciBenar;

            $benarDipilih = 0;
            $salahDipilih = 0;

            foreach ($jawaban as $j) {
                if (in_array($j, $kunciBenar)) {
                    $benarDipilih++;
                } elseif (in_array($j, $availableOptions)) {
                    $salahDipilih++;
                }
            }

            // Calculate points: (benar dipilih / total kunci benar) * bobot - (salah dipilih / total opsi salah) * bobot
            $poinPerBenar = $jumlahKunciBenar > 0 ? ($this->bobot_nilai / $jumlahKunciBenar) : 0;
            $poinPerSalah = $jumlahKunciSalah > 0 ? ($this->bobot_nilai / $jumlahKunciSalah) : 0;

            $skor = ($benarDipilih * $poinPerBenar) - ($salahDipilih * $poinPerSalah);
            
            return max(0, $skor);
        }

        // 2. Partial Scoring untuk Benar / Salah
        if ($this->tipe_soal === self::TIPE_BENAR_SALAH) {
            if (!is_array($jawaban)) {
                $jawaban = json_decode($jawaban, true) ?? [];
            }

            $pilihanData = $this->pilihan_jawaban;
            $pernyataan = $pilihanData['pernyataan'] ?? [];

            if (empty($pernyataan))
                return 0;

            $benar = 0;
            foreach ($pernyataan as $index => $item) {
                $jawabanBenar = $item['benar'] ?? false;
                $jawabanSiswa = $jawaban[$index] ?? false;

                if (is_string($jawabanSiswa)) {
                    $jawabanSiswa = filter_var($jawabanSiswa, FILTER_VALIDATE_BOOLEAN);
                }

                if ($jawabanSiswa === $jawabanBenar) {
                    $benar++;
                }
            }

            return ($benar / count($pernyataan)) * $this->bobot_nilai;
        }

        // 3. Fallback: Full score or 0 for other types
        return $this->checkAnswer($jawaban) ? $this->bobot_nilai : 0;
    }

    /**
     * Get label for tipe soal
     */
    public static function getTipeSoalLabel($tipe)
    {
        return match ($tipe) {
            self::TIPE_PILGAN => 'Pilihan Ganda',
            self::TIPE_PILGAN_KOMPLEKS => 'Pilihan Ganda Kompleks',
            self::TIPE_BENAR_SALAH => 'Benar/Salah',
            self::TIPE_ISIAN => 'Isian Singkat',
            self::TIPE_URAIAN => 'Uraian',
            self::TIPE_ESSAY => 'Essay',
            default => 'Unknown',
        };
    }
}