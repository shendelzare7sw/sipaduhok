<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'pertemuan_id',
        'guru_id',
        'judul_ujian',
        'deskripsi',
        'tipe_ujian',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_menit',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'durasi_menit' => 'integer',
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function guru()
    {
        return $this->belongsTo(TenagaPendidik::class, 'guru_id');
    }

    public function soalUjian()
    {
        return $this->hasMany(SoalUjian::class);
    }

    public function ujianSiswa()
    {
        return $this->hasMany(UjianSiswa::class);
    }

    // Helper: Cek apakah ujian sedang berlangsung
    public function isOngoing()
    {
        $now = now();
        return $now->gte($this->tanggal_mulai) && $now->lte($this->tanggal_selesai);
    }

    // Helper: Hitung total bobot nilai
    public function totalBobot()
    {
        return $this->soalUjian()->sum('bobot_nilai');
    }
}