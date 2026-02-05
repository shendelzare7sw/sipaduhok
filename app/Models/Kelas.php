<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'cabang_id',
        'tahun_ajaran_id',
        'wali_kelas_id',
        'nama_kelas',
        'jenjang',
        'kode_kelas',
        'kuota_siswa',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(TenagaPendidik::class, 'wali_kelas_id');
    }

    public function jadwalPelajaran()
    {
        return $this->belongsToMany(JadwalPelajaran::class, 'jadwal_kelas');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
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

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Get guru yang mengajar di kelas ini.
     */
    public function guruPengajar()
    {
        return $this->hasMany(\App\Models\GuruPengajarKelas::class, 'kelas_id');
    }

    /**
     * Get wali kelas assignments (many-to-many).
     */
    public function waliKelasAssignments()
    {
        return $this->hasMany(\App\Models\WaliKelasAssignment::class, 'kelas_id');
    }

    /**
     * Get all wali kelas untuk kelas ini (via pivot).
     */
    public function waliKelasMultiple()
    {
        return $this->belongsToMany(TenagaPendidik::class, 'wali_kelas_assignments', 'kelas_id', 'tenaga_pendidik_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }
}