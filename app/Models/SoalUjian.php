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
        return strtoupper(trim($jawaban)) === strtoupper(trim($this->jawaban_benar));
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

        $pilihanData = $this->pilihan_jawaban;
        $jawabanBenar = $pilihanData['jawaban_benar'] ?? [];

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

        // Fallback to jawaban_benar column
        if ($this->jawaban_benar) {
            return strtolower(trim($this->jawaban_benar)) === $jawaban;
        }

        return false;
    }

    /**
     * Calculate partial score for benar_salah type
     */
    public function calculatePartialScore($jawaban)
    {
        if ($this->tipe_soal !== self::TIPE_BENAR_SALAH) {
            return $this->checkAnswer($jawaban) ? $this->bobot_nilai : 0;
        }

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