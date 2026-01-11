<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengaturanIstirahat extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_istirahat';

    protected $fillable = [
        'jenjang',
        'urutan',
        'jam_mulai',
        'jam_selesai',
        'hari_aktif',
        'nama_istirahat',
        'is_active',
    ];

    protected $casts = [
        'hari_aktif' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope untuk filter berdasarkan jenjang
     */
    public function scopeJenjang($query, $jenjang)
    {
        return $query->where('jenjang', $jenjang);
    }

    /**
     * Scope untuk filter istirahat aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get istirahat untuk hari tertentu
     */
    public function scopeUntukHari($query, $hari)
    {
        return $query->whereJsonContains('hari_aktif', $hari);
    }

    /**
     * Check apakah waktu bentrok dengan istirahat ini
     *
     * Logika: Dua interval waktu TIDAK bentrok jika:
     * - Interval 1 selesai SEBELUM ATAU TEPAT saat interval 2 mulai (jamSelesai <= jam_mulai), ATAU
     * - Interval 1 mulai SETELAH ATAU TEPAT saat interval 2 selesai (jamMulai >= jam_selesai)
     *
     * Dengan menggunakan <= dan >=, jadwal yang berakhir/mulai TEPAT pada boundary
     * istirahat TIDAK dianggap bentrok (no overlap).
     *
     * Contoh TIDAK bentrok:
     * - Jadwal 07:00-08:30, Istirahat 08:30-09:00 ✅ (jadwal selesai tepat saat istirahat mulai)
     * - Jadwal 09:00-11:00, Istirahat 08:30-09:00 ✅ (jadwal mulai tepat saat istirahat selesai)
     *
     * Contoh BENTROK:
     * - Jadwal 08:00-08:45, Istirahat 08:30-09:00 ❌ (overlap 15 menit)
     * - Jadwal 08:45-09:15, Istirahat 08:30-09:00 ❌ (overlap 15 menit)
     */
    public function isBentrok($jamMulai, $jamSelesai)
    {
        return !($jamSelesai <= $this->jam_mulai || $jamMulai >= $this->jam_selesai);
    }

    /**
     * Get daftar istirahat untuk jenjang dan hari tertentu
     */
    public static function getIstirahatUntuk($jenjang, $hari)
    {
        return self::jenjang($jenjang)
            ->aktif()
            ->untukHari($hari)
            ->orderBy('jam_mulai')
            ->get();
    }
}
