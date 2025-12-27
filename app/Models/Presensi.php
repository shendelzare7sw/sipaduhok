<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'mata_pelajaran_id',
        'tanggal',
        'status',
        'keterangan',
        'diinput_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    // Helper: Hitung total presensi siswa berdasarkan status
    public static function hitungPresensiSiswa($siswaId, $status, $tahunAjaranId = null)
    {
        $query = self::where('siswa_id', $siswaId)
            ->where('status', $status);

        if ($tahunAjaranId) {
            $query->whereHas('kelas', function($q) use ($tahunAjaranId) {
                $q->where('tahun_ajaran_id', $tahunAjaranId);
            });
        }

        return $query->count();
    }
}