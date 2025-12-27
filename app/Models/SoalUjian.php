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
        'pertanyaan',
        'tipe_soal',
        'pilihan_jawaban',
        'jawaban_benar',
        'bobot_nilai',
    ];

    protected $casts = [
        'pilihan_jawaban' => 'array',
        'bobot_nilai' => 'integer',
    ];

    // Relationships
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    // Helper: Cek jawaban benar atau salah (untuk pilihan ganda)
    public function checkAnswer($jawaban)
    {
        if ($this->tipe_soal === 'pilihan_ganda') {
            return strtoupper($jawaban) === strtoupper($this->jawaban_benar);
        }
        return null; // Essay perlu penilaian manual
    }
}