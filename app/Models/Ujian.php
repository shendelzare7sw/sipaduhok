<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $table = 'ujian';

    // Tipe Ujian Constants
    const TIPE_ULANGAN_HARIAN = 'ulangan_harian';
    const TIPE_UTS = 'uts';
    const TIPE_UAS = 'uas';
    const TIPE_KUIS = 'kuis';
    const TIPE_PTS_GANJIL = 'pts_ganjil';
    const TIPE_PAS_GANJIL = 'pas_ganjil';
    const TIPE_PTS_GENAP = 'pts_genap';
    const TIPE_PAS_GENAP = 'pas_genap';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'judul_ujian',
        'deskripsi',
        'tipe_ujian',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_menit',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'durasi_menit' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get labels for tipe ujian
     */
    public static function getTipeLabels(): array
    {
        return [
            self::TIPE_ULANGAN_HARIAN => 'Ulangan Harian',
            self::TIPE_UTS => 'UTS',
            self::TIPE_UAS => 'UAS',
            self::TIPE_KUIS => 'Kuis',
            self::TIPE_PTS_GANJIL => 'PTS Ganjil',
            self::TIPE_PAS_GANJIL => 'PAS Ganjil',
            self::TIPE_PTS_GENAP => 'PTS Genap',
            self::TIPE_PAS_GENAP => 'PAS Genap',
        ];
    }

    /**
     * Get label for current tipe
     */
    public function getTipeLabelAttribute(): string
    {
        return self::getTipeLabels()[$this->tipe_ujian] ?? $this->tipe_ujian;
    }

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
