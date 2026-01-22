<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KalenderAkademik extends Model
{
    use HasFactory;

    protected $table = 'kalender_akademik';

    protected $fillable = [
        'tahun_ajaran_id',
        'nama_kegiatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_mulai',
        'waktu_selesai',
        'keterangan',
        'jenis_kegiatan',
        'lampiran_surat',
        'status',
        'is_hidden_siswa',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_hidden_siswa' => 'boolean',
    ];

    /**
     * Relasi ke TahunAjaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Relasi ke Pengumuman
     */
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'kalender_akademik_id');
    }

    /**
     * Scope untuk kalender aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk kegiatan mendatang
     */
    public function scopeMendatang($query)
    {
        return $query->where('tanggal_mulai', '>=', now()->toDateString());
    }

    /**
     * Scope untuk kegiatan hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_mulai', '<=', now())
                    ->where(function($q) {
                        $q->whereDate('tanggal_selesai', '>=', now())
                          ->orWhereNull('tanggal_selesai');
                    });
    }

    /**
     * Scope untuk kegiatan dalam rentang tanggal
     */
    public function scopeRentangTanggal($query, $mulai, $selesai)
    {
        return $query->where(function($q) use ($mulai, $selesai) {
            $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
              ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
              ->orWhere(function($q2) use ($mulai, $selesai) {
                  $q2->where('tanggal_mulai', '<=', $mulai)
                     ->where('tanggal_selesai', '>=', $selesai);
              });
        });
    }

    /**
     * Cek apakah kegiatan sedang berlangsung
     */
    public function isBerlangsung()
    {
        $today = now()->toDateString();
        
        if ($this->tanggal_selesai) {
            return $this->tanggal_mulai <= $today && $this->tanggal_selesai >= $today;
        }
        
        return $this->tanggal_mulai == $today;
    }

    /**
     * Get durasi kegiatan dalam hari
     */
    public function getDurasiAttribute()
    {
        if ($this->tanggal_selesai) {
            return Carbon::parse($this->tanggal_mulai)->diffInDays($this->tanggal_selesai) + 1;
        }
        return 1;
    }

    /**
     * Get label jenis kegiatan
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'field_trip' => 'Field Trip',
            'outing' => 'Outing',
            'live_in' => 'Live In',
            'hokfest' => 'HOK Fest',
            'pts' => 'PTS',
            'pas' => 'PAS',
            'libur' => 'Libur',
            'ujian' => 'Ujian',
            'acara_sekolah' => 'Acara Sekolah',
            'lainnya' => 'Lainnya'
        ];

        return $labels[$this->jenis_kegiatan] ?? 'Lainnya';
    }
}