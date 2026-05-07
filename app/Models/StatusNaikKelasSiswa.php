<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusNaikKelasSiswa extends Model
{
    use HasFactory;

    protected $table = 'status_naik_kelas_siswa';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'kelas_asal',
        'kelas_tujuan',
        'original_kelas_id',
        'status_pembayaran',
        'persentase_nilai_tuntas',
        'jumlah_mapel_tuntas',
        'total_mapel',
        'status_kelulusan',
        'izin_khusus_ketua',
        'tanggal_eksekusi',
        'is_processed',
        'rolled_back_at',
        'rolled_back_by',
    ];

    protected $casts = [
        'persentase_nilai_tuntas' => 'decimal:2',
        'izin_khusus_ketua' => 'boolean',
        'is_processed' => 'boolean',
        'tanggal_eksekusi' => 'date',
        'rolled_back_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function originalKelas()
    {
        return $this->belongsTo(Kelas::class, 'original_kelas_id');
    }

    public function rolledBackBy()
    {
        return $this->belongsTo(User::class, 'rolled_back_by');
    }
}
