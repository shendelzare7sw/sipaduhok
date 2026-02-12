<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JawabanSiswa extends Model
{
    use HasFactory;

    protected $table = 'jawaban_siswa';

    protected $fillable = [
        'ujian_siswa_id',
        'soal_ujian_id',
        'jawaban',
        'nilai_soal',
        'feedback',
    ];

    protected $casts = [
        'nilai_soal' => 'decimal:2',
    ];

    // Relationships
    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class);
    }

    public function soalUjian()
    {
        return $this->belongsTo(SoalUjian::class);
    }

    /**
     * Auto-grading untuk semua tipe soal yang bisa di-auto-grade.
     * Return: true jika berhasil di-grade, false jika perlu manual.
     */
    public function autoGrade()
    {
        $soal = $this->soalUjian;
        
        if (!$soal) return false;

        $result = $soal->checkAnswer($this->jawaban);

        if ($result !== null) {
            // Auto-gradable: pilgan, pilgan_kompleks, benar_salah, isian_singkat
            $this->nilai_soal = $soal->calculatePartialScore($this->jawaban);
            $this->save();
            return true;
        }
        
        return false; // Uraian/Essay perlu grading manual
    }

    /**
     * Cek apakah soal ini perlu koreksi manual
     */
    public function perluKoreksiManual(): bool
    {
        $soal = $this->soalUjian;
        if (!$soal) return false;

        return in_array($soal->tipe_soal, [
            SoalUjian::TIPE_URAIAN,
            SoalUjian::TIPE_ESSAY,
        ]);
    }

    /**
     * Cek apakah sudah dikoreksi (nilai_soal sudah diisi)
     */
    public function sudahDikoreksi(): bool
    {
        return $this->nilai_soal !== null;
    }
}