<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'jenjang',
        'deskripsi',
    ];

    // Relationships
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function guruPengajar()
    {
        return $this->hasMany(GuruPengajarKelas::class);
    }
}