<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaPendidik extends Model
{
    use HasFactory;

    protected $table = 'tenaga_pendidik';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'pendidikan_terakhir',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'guru_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'guru_id');
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'guru_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'guru_id');
    }

    /**
     * Get penugasan mengajar guru ini.
     */
    public function guruKelas()
    {
        return $this->hasMany(\App\Models\GuruPengajarKelas::class, 'tenaga_pendidik_id');
    }
}