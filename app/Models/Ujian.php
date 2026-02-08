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
    const TIPE_LATIHAN = 'latihan';
    const TIPE_PTS_GANJIL = 'pts_ganjil';
    const TIPE_PAS_GANJIL = 'pas_ganjil';
    const TIPE_PTS_GENAP = 'pts_genap';
    const TIPE_PAS_GENAP = 'pas_genap';
    const TIPE_TO_1 = 'to_1';
    const TIPE_TO_2 = 'to_2';
    const TIPE_TO_3 = 'to_3';
    const TIPE_UPK = 'upk';
    const TIPE_UJIAN_PRAKTEK = 'ujian_praktek';

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
        'tampilkan_nilai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'durasi_menit' => 'integer',
        'is_active' => 'boolean',
        'tampilkan_nilai' => 'boolean',
    ];

    /**
     * Get labels for tipe ujian
     */
    public static function getTipeLabels(): array
    {
        return [
            self::TIPE_ULANGAN_HARIAN => 'Ulangan Harian',
            self::TIPE_LATIHAN => 'Latihan',
            self::TIPE_PTS_GANJIL => 'PTS Ganjil',
            self::TIPE_PAS_GANJIL => 'PAS Ganjil',
            self::TIPE_PTS_GENAP => 'PTS Genap',
            self::TIPE_PAS_GENAP => 'PAS Genap',
            self::TIPE_TO_1 => 'Try Out 1',
            self::TIPE_TO_2 => 'Try Out 2',
            self::TIPE_TO_3 => 'Try Out 3',
            self::TIPE_UPK => 'UPK',
            self::TIPE_UJIAN_PRAKTEK => 'Ujian Praktek',
            // Legacy
            self::TIPE_UTS => 'UTS',
            self::TIPE_UAS => 'UAS',
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

    /**
     * Cek apakah tipe ujian ini membutuhkan validasi akses (PTS/PAS/UTS/UAS/TO/UPK).
     * Ulangan Harian dan Latihan TIDAK membutuhkan validasi.
     */
    public function requiresValidation(): bool
    {
        return in_array($this->tipe_ujian, [
            self::TIPE_UTS, self::TIPE_UAS,
            self::TIPE_PTS_GANJIL, self::TIPE_PAS_GANJIL,
            self::TIPE_PTS_GENAP, self::TIPE_PAS_GENAP,
            self::TIPE_TO_1, self::TIPE_TO_2, self::TIPE_TO_3,
            self::TIPE_UPK, self::TIPE_UJIAN_PRAKTEK,
        ]);
    }

    /**
     * Cek apakah kelas adalah tingkat akhir per jenjang.
     * SD: kelas 6, SMP: kelas 9, SMA: kelas 12
     */
    public static function isTingkatAkhir($kelas): bool
    {
        if (!$kelas) return false;

        $jenjang = strtoupper($kelas->jenjang ?? '');
        $namaKelas = $kelas->nama_kelas ?? '';

        // Extract angka dari nama_kelas (misal "9A" → 9, "12-B" → 12)
        preg_match('/(\d+)/', $namaKelas, $matches);
        $tingkat = (int) ($matches[1] ?? 0);

        return match ($jenjang) {
            'SD' => $tingkat === 6,
            'SMP' => $tingkat === 9,
            'SMA' => $tingkat === 12,
            default => false,
        };
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
