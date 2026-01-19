<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'jenis_tagihan',
        'keterangan',
        'jumlah',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Update status tagihan berdasarkan total pembayaran yang disetujui
     */
    public function updateStatusBayar()
    {
        // Hitung total pembayaran yang sudah disetujui
        $totalDibayar = $this->pembayaran()
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');

        // Update status tagihan based on payment progress
        if ($totalDibayar >= $this->jumlah) {
            // Fully paid
            $this->update(['status' => 'sudah_bayar']);
        } elseif ($totalDibayar > 0) {
            // Partial payment (cicilan)
            $this->update(['status' => 'cicilan']);
        } else {
            // Not paid yet
            $this->update(['status' => 'belum_bayar']);
        }

        return $this;
    }

    /**
     * Get Human Readable Label for Jenis Tagihan
     */
    public static function getLabelJenis($jenis)
    {
        $labels = [
            'uang_pendaftaran' => 'Formulir Pendaftaran/ Daftar Ulang',
            'uang_pangkal' => 'Uang Pangkal',
            'kegiatan' => 'Uang Kegiatan',
            'buku' => 'Buku Paket',
            'seragam' => 'Seragam',
            'rapor_foto' => 'Rapor Foto',
            'ujian' => 'Ujian & Wisuda',
            'akm' => 'AKM',
            'spp' => 'SPP',
        ];

        // Handle SPP with suffixes like spp_juli
        if (str_starts_with($jenis, 'spp')) {
            return 'SPP';
        }

        return $labels[$jenis] ?? null;
    }
}