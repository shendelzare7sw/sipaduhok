<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    protected $table = 'pengumuman';

    protected $fillable = [
        'kalender_akademik_id',
        'dibuat_oleh',
        'judul',
        'isi_pengumuman',
        'tanggal_pengumuman',
        'prioritas',
        'lampiran_surat',
        'is_from_kalender',
        'status',
    ];

    protected $casts = [
        'tanggal_pengumuman' => 'date',
        'is_from_kalender' => 'boolean',
    ];

    /**
     * Relasi ke KalenderAkademik
     */
    public function kalenderAkademik()
    {
        return $this->belongsTo(KalenderAkademik::class, 'kalender_akademik_id');
    }

    /**
     * Relasi ke User (pembuat)
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Scope untuk pengumuman aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk pengumuman hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_pengumuman', now()->toDateString());
    }

    /**
     * Scope untuk pengumuman mendatang
     */
    public function scopeMendatang($query)
    {
        return $query->where('tanggal_pengumuman', '>=', now()->toDateString());
    }

    /**
     * Scope untuk pengumuman berdasarkan prioritas
     */
    public function scopePrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    /**
     * Get label prioritas dengan warna badge
     */
    public function getPrioritasBadgeAttribute()
    {
        $badges = [
            'biasa' => ['label' => 'Biasa', 'class' => 'bg-secondary'],
            'penting' => ['label' => 'Penting', 'class' => 'bg-warning text-dark'],
            'mendesak' => ['label' => 'Mendesak', 'class' => 'bg-danger'],
        ];

        return $badges[$this->prioritas] ?? $badges['biasa'];
    }

    /**
     * Get label status dengan warna badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => ['label' => 'Draft', 'class' => 'bg-secondary'],
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-success'],
            'arsip' => ['label' => 'Arsip', 'class' => 'bg-dark'],
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    /**
     * Auto-generate pengumuman dari kalender (H-3)
     */
    public static function autoGenerateFromKalender()
    {
        $tiga_hari_lagi = now()->addDays(3)->toDateString();
        
        $kegiatan = KalenderAkademik::aktif()
            ->whereDate('tanggal_mulai', $tiga_hari_lagi)
            ->whereDoesntHave('pengumuman')
            ->get();

        foreach ($kegiatan as $k) {
            self::create([
                'kalender_akademik_id' => $k->id,
                'dibuat_oleh' => auth()->id() ?? 1, // Default ke user ID 1 jika scheduler
                'judul' => 'Pengingat: ' . $k->nama_kegiatan,
                'isi_pengumuman' => "Kegiatan {$k->nama_kegiatan} akan dilaksanakan pada tanggal {$k->tanggal_mulai->format('d F Y')}. {$k->keterangan}",
                'tanggal_pengumuman' => now()->toDateString(),
                'prioritas' => 'penting',
                'lampiran_surat' => $k->lampiran_surat,
                'is_from_kalender' => true,
                'status' => 'aktif',
            ]);
        }
    }
}