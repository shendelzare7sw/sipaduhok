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
