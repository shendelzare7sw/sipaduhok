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
        'tagihan_asal_id',
        'dialihkan_ke_id',
        'dialihkan_pada',
        'jenis_tagihan',
        'keterangan',
        'jumlah',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
        'dialihkan_pada' => 'datetime',
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
     * Tagihan asal di TA lama (jika ini adalah carryover).
     */
    public function tagihanAsal()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_asal_id');
    }

    /**
     * Tagihan turunan di TA aktif (jika tagihan ini sudah dialihkan).
     */
    public function tagihanAlihan()
    {
        return $this->belongsTo(Tagihan::class, 'dialihkan_ke_id');
    }

    /**
     * Scope: tagihan asli yang belum lunas (exclude yang sudah dialihkan ke TA lain).
     * Dipakai dashboard agar tidak double-count carryover.
     */
    public function scopeBelumLunasOriginal($query)
    {
        return $query->whereNull('dialihkan_ke_id')
            ->whereIn('status', ['belum_bayar', 'cicilan', 'terlambat']);
    }

    /**
     * Tandai tagihan ini sebagai sudah dialihkan ke tagihan baru di TA aktif.
     */
    public function tandaiDialihkan(int $tagihanBaruId): void
    {
        $this->update([
            'dialihkan_ke_id' => $tagihanBaruId,
            'dialihkan_pada' => now(),
        ]);
    }

    /**
     * Hitung sisa pembayaran (jumlah - total pembayaran disetujui).
     */
    public function getSisaPembayaranAttribute(): float
    {
        $terbayar = $this->pembayaran()
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');

        return max(0, (float) $this->jumlah - (float) $terbayar);
    }

    /**
     * Update status tagihan berdasarkan total pembayaran yang disetujui.
     * Jika tagihan ini adalah carryover (punya tagihan_asal_id) dan menjadi lunas,
     * propagasikan status lunas ke tagihan asal.
     */
    public function updateStatusBayar()
    {
        $oldStatus = $this->status;

        // Jika jumlah = 0, tandai sebagai sudah bayar (lunas)
        if ($this->jumlah == 0) {
            $this->update(['status' => 'sudah_bayar']);
        } else {
            // Hitung total pembayaran yang sudah disetujui
            $totalDibayar = $this->pembayaran()
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            if ($totalDibayar >= $this->jumlah) {
                $this->update(['status' => 'sudah_bayar']);
            } elseif ($totalDibayar > 0) {
                $this->update(['status' => 'cicilan']);
            } else {
                $this->update(['status' => 'belum_bayar']);
            }
        }

        // Propagasi ke tagihan asal: bila tagihan ini carryover dan baru menjadi lunas,
        // tandai tagihan asal di TA lama juga lunas (audit-trail tetap utuh).
        if ($this->status === 'sudah_bayar' && $oldStatus !== 'sudah_bayar' && $this->tagihan_asal_id) {
            $asal = static::find($this->tagihan_asal_id);
            if ($asal && $asal->status !== 'sudah_bayar') {
                $asal->update(['status' => 'sudah_bayar']);
            }
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
