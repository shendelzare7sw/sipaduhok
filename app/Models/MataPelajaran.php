<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajaran';

    public const AGAMA_FILTERS = [
        'Islam',
        'Kristen',
        'Katolik',
        'Hindu',
        'Buddha',
        'Konghucu',
    ];

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'jenjang',
        'kelompok',
        'filter_agama',
        'deskripsi',
    ];

    public function isFilteredByAgama(): bool
    {
        return ! empty($this->filter_agama);
    }

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