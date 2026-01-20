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
        'pertemuan_id',
        'guru_id',
        'jenis_tugas',
        'urutan',
        'judul_tugas',
        'judul_bab',
        'nama_materi',
        'deskripsi',
        'file_tugas',
        'tanggal_mulai',
        'tanggal_deadline',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_deadline' => 'date',
    ];

    // Jenis Tugas constants
    const JENIS_TUGAS = 'tugas';
    const JENIS_LATIHAN = 'latihan';

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

    public function tugasSiswa()
    {
        return $this->hasMany(TugasSiswa::class);
    }

    // Scopes
    public function scopeTugasOnly($query)
    {
        return $query->where('jenis_tugas', self::JENIS_TUGAS);
    }

    public function scopeLatihanOnly($query)
    {
        return $query->where('jenis_tugas', self::JENIS_LATIHAN);
    }

    public function scopeActive($query)
    {
        return $query->where('tanggal_deadline', '>=', now());
    }

    // Helper: Cek apakah tugas sudah lewat deadline
    public function isOverdue()
    {
        return now()->gt($this->tanggal_deadline);
    }

    // Helper: Cek apakah aktif (dalam masa pengerjaan)
    public function isActive()
    {
        $now = now();
        return $now->gte($this->tanggal_mulai) && $now->lte($this->tanggal_deadline);
    }

    // Helper: Hitung berapa siswa yang sudah submit
    public function countSubmitted()
    {
        return $this->tugasSiswa()->where('status', '!=', 'belum_dikerjakan')->count();
    }

    // Helper: Get label dengan nomor urut (Tugas 1, Latihan 2, etc)
    public function getLabel()
    {
        $prefix = $this->jenis_tugas === self::JENIS_TUGAS ? 'Tugas' : 'Latihan';
        return $prefix . ' ' . ($this->urutan ?? $this->id);
    }

    // Helper: Auto-generate urutan saat create
    public static function generateUrutan($kelasId, $mapelId, $jenisTugas)
    {
        $lastUrutan = self::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('jenis_tugas', $jenisTugas)
            ->max('urutan');

        return ($lastUrutan ?? 0) + 1;
    }

    // Boot method untuk auto-generate urutan
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tugas) {
            if (empty($tugas->urutan)) {
                $tugas->urutan = self::generateUrutan(
                    $tugas->kelas_id,
                    $tugas->mata_pelajaran_id,
                    $tugas->jenis_tugas ?? self::JENIS_TUGAS
                );
            }
        });
    }
}