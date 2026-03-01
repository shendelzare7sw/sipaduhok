<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanRaporKetua extends Model
{
    protected $table = 'pengajuan_rapor_ketua';

    protected $fillable = [
        'siswa_id',
        'diajukan_oleh',
        'alasan',
        'tipe',
        'periode',
        'status',
        'diputuskan_oleh',
        'catatan_ketua',
        'tanggal_pengajuan',
        'tanggal_keputusan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_keputusan' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function pemutus()
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }
}
