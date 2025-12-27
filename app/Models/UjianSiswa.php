<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianSiswa extends Model
{
    use HasFactory;

    protected $table = 'ujian_siswa';

    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'waktu_mulai',
        'waktu_selesai',
        'nilai',
        'status',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    // Relationships
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    // Helper: Hitung sisa waktu ujian
    public function remainingTime()
    {
        if ($this->waktu_mulai && $this->ujian) {
            $deadline = $this->waktu_mulai->addMinutes($this->ujian->durasi_menit);
            $remaining = now()->diffInMinutes($deadline, false);
            return max(0, $remaining);
        }
        return 0;
    }

    // Helper: Cek apakah waktu ujian habis
    public function isTimeUp()
    {
        return $this->remainingTime() <= 0;
    }
}