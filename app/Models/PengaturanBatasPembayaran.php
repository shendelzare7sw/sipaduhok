<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanBatasPembayaran extends Model
{
    protected $table = 'pengaturan_batas_pembayaran';

    protected $fillable = [
        'tahun_ajaran_id',
        'periode',
        'jenis_tagihan_required',
        'created_by',
    ];

    protected $casts = [
        'jenis_tagihan_required' => 'array',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Cek apakah siswa sudah lunas semua tagihan yang required untuk periode ini.
     */
    public function cekSiswaLunas(Siswa $siswa): bool
    {
        $requiredTypes = $this->jenis_tagihan_required ?? [];

        if (empty($requiredTypes)) {
            return true; // Tidak ada requirement = lunas
        }

        $tagihanBelumLunas = Tagihan::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->whereIn('jenis_tagihan', $requiredTypes)
            ->where('status', '!=', 'sudah_bayar')
            ->exists();

        return !$tagihanBelumLunas;
    }
}
