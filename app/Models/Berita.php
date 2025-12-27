<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;

    protected $table = 'berita';

    protected $fillable = [
        'judul',
        'deskripsi_singkat',
        'gambar_thumbnail',
        'url_berita',
        'kategori',
        'tanggal_berita',
        'is_featured',
        'urutan_tampil',
        'status',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_berita' => 'date',
        'is_featured' => 'boolean',
    ];

    // ============================================
    // RELASI
    // ============================================
    
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    // ============================================
    // SCOPES
    // ============================================
    
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan_tampil', 'asc')
                    ->orderBy('tanggal_berita', 'desc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('deskripsi_singkat', 'like', "%{$search}%");
        });
    }

    // ============================================
    // ACCESSORS
    // ============================================
    
    public function getGambarUrlAttribute()
    {
        if ($this->gambar_thumbnail) {
            $path = public_path('img/berita/' . $this->gambar_thumbnail);
            if (file_exists($path)) {
                return asset('img/berita/' . $this->gambar_thumbnail);
            }
        }
        return asset('img/news-default.jpg');
    }

    public function getExcerptAttribute()
    {
        return Str::limit($this->deskripsi_singkat, 150);
    }

    public function getKategoriLabelAttribute()
    {
        $labels = [
            'kegiatan' => 'Kegiatan',
            'prestasi' => 'Prestasi',
            'pengumuman' => 'Pengumuman',
            'artikel' => 'Artikel',
            'ujian' => 'Ujian'
        ];
        return $labels[$this->kategori] ?? 'Kegiatan';
    }

    public function getKategoriBadgeClassAttribute()
    {
        $badges = [
            'kegiatan' => 'bg-secondary text-white',
            'prestasi' => 'bg-accent-yellow text-gray-800',
            'pengumuman' => 'bg-primary text-white',
            'artikel' => 'bg-secondary text-white',
            'ujian' => 'bg-secondary text-white'
        ];
        return $badges[$this->kategori] ?? 'bg-secondary text-white';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => ['label' => 'Draft', 'class' => 'bg-gray-500 text-white'],
            'aktif' => ['label' => 'Aktif', 'class' => 'bg-green-500 text-white'],
            'arsip' => ['label' => 'Arsip', 'class' => 'bg-gray-700 text-white'],
        ];
        return $badges[$this->status] ?? $badges['draft'];
    }

    public function getTanggalFormatIndonesiaAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $tanggal = $this->tanggal_berita;
        return $tanggal->format('d') . ' ' . $bulan[(int)$tanggal->format('m')] . ' ' . $tanggal->format('Y');
    }

    // ============================================
    // STATIC METHODS
    // ============================================
    
    public static function getBeritaPublic($kategori = null, $search = null, $perPage = 6)
    {
        $query = self::aktif()->ordered();

        if ($kategori && $kategori !== 'all') {
            $query->kategori($kategori);
        }

        if ($search) {
            $query->search($search);
        }

        return $query->paginate($perPage);
    }

    public static function getBeritaUtama()
    {
        return self::aktif()->featured()->ordered()->first();
    }

    public static function getKategoriOptions()
    {
        return [
            'kegiatan' => 'Kegiatan',
            'prestasi' => 'Prestasi',
            'pengumuman' => 'Pengumuman',
            'artikel' => 'Artikel',
            'ujian' => 'Ujian'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'draft' => 'Draft',
            'aktif' => 'Aktif',
            'arsip' => 'Arsip'
        ];
    }
}