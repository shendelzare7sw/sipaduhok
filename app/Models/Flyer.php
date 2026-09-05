<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flyer extends Model
{
    use HasFactory;

    protected $table = 'flyer';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar_flyer',
        'link_url',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_audience',
        'urutan_tampil',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Relasi ke User (pembuat)
     */
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    /**
     * Scope untuk flyer aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                    ->whereDate('tanggal_mulai', '<=', now())
                    ->whereDate('tanggal_selesai', '>=', now());
    }

    /**
     * Scope untuk flyer berdasarkan target
     */
    public function scopeTargetAudience($query, $target)
    {
        return $query->where(function($q) use ($target) {
            $q->where('target_audience', $target)
              ->orWhere('target_audience', 'semua');
        });
    }

    /**
     * Scope untuk urutan tampil
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan_tampil', 'asc');
    }

    /**
     * Get URL gambar flyer
     */
    public function getGambarUrlAttribute()
    {
        if ($this->gambar_flyer && \Storage::disk('public')->exists($this->gambar_flyer)) {
            return asset('storage/' . $this->gambar_flyer);
        }

        return asset('img/hero-img.jpg');
    }

    /**
     * Cek apakah flyer sedang berlaku
     */
    public function isBerlaku()
    {
        $today = now()->toDateString();
        return $this->status === 'aktif' 
            && $this->tanggal_mulai <= $today 
            && $this->tanggal_selesai >= $today;
    }

    /**
     * Get label target audience
     */
    public function getTargetLabelAttribute()
    {
        $labels = [
            'semua' => 'Semua',
            'siswa' => 'Siswa',
            'guru' => 'Guru',
            'wali_kelas' => 'Wali Kelas',
            'orang_tua' => 'Wali Siswa'
        ];

        return $labels[$this->target_audience] ?? 'Semua';
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => ['label' => 'Draft', 'class' => 'bg-secondary'],
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-success'],
            'nonaktif' => ['label' => 'Nonaktif', 'class' => 'bg-danger'],
        ];

        return $badges[$this->status] ?? $badges['draft'];
    }

    /**
     * Get flyer aktif untuk popup (random order)
     */
    public static function getPopupFlyers($target = 'siswa', $limit = 3)
    {
        return self::aktif()
            ->targetAudience($target)
            ->ordered()
            ->limit($limit)
            ->get();
    }
}
