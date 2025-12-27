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

    // Helper: Auto-grading untuk pilihan ganda
    public function autoGrade()
    {
        $soal = $this->soalUjian;
        
        if ($soal && $soal->tipe_soal === 'pilihan_ganda') {
            if ($soal->checkAnswer($this->jawaban)) {
                $this->nilai_soal = $soal->bobot_nilai;
            } else {
                $this->nilai_soal = 0;
            }
            $this->save();
            return true;
        }
        
        return false; // Essay perlu grading manual
    }
}