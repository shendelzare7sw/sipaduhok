<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'judul_tugas',
        'deskripsi',
        'file_tugas',
        'tanggal_mulai',
        'tanggal_deadline',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_deadline' => 'date',
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

    public function guru()
    {
        return $this->belongsTo(TenagaPendidik::class, 'guru_id');
    }

    public function tugasSiswa()
    {
        return $this->hasMany(TugasSiswa::class);
    }

    // Helper: Cek apakah tugas sudah lewat deadline
    public function isOverdue()
    {
        return now()->gt($this->tanggal_deadline);
    }

    // Helper: Hitung berapa siswa yang sudah submit
    public function countSubmitted()
    {
        return $this->tugasSiswa()->where('status', '!=', 'belum_dikerjakan')->count();
    }
}