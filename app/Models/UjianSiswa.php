<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianSiswa extends Model
{
    use HasFactory;

    protected $table = 'ujian_siswa';

    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'current_soal_ujian_id',
        'current_nomor_soal',
        'waktu_mulai',
        'waktu_selesai',
        'last_activity_at',
        'last_heartbeat_at',
        'nilai',
        'status',
        'pengulangan_ke',
        'nilai_terbaik',
        'answered_count',
        'doubt_count',
        'visited_count',
        'focus_lost_count',
        'focus_lost_total_seconds',
        'active_focus_lost_at',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'last_activity_at' => 'datetime',
        'last_heartbeat_at' => 'datetime',
        'nilai' => 'decimal:2',
        'answered_count' => 'integer',
        'doubt_count' => 'integer',
        'visited_count' => 'integer',
        'focus_lost_count' => 'integer',
        'focus_lost_total_seconds' => 'integer',
        'active_focus_lost_at' => 'datetime',
    ];

    // Relationships
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jawabanSiswa()
    {
        return $this->hasMany(JawabanSiswa::class);
    }

    public function currentSoalUjian()
    {
        return $this->belongsTo(SoalUjian::class, 'current_soal_ujian_id');
    }

    public function soalStatuses()
    {
        return $this->hasMany(UjianSiswaSoalStatus::class);
    }

    public function pengawasanLogs()
    {
        return $this->hasMany(UjianPengawasanLog::class);
    }

    // Helper: Hitung sisa waktu ujian
    public function remainingTime()
    {
        if ($this->waktu_mulai && $this->ujian) {
            // Jika durasi 0, waktu tak terbatas (return angka besar)
            if ($this->ujian->durasi_menit == 0) {
                return 999999; 
            }
            
            $deadline = $this->waktu_mulai->addMinutes($this->ujian->durasi_menit);
            // Tambah toleransi waktu (buffer) misal 1 menit untuk latency jaringan
            $deadlineWithTolerance = $deadline->addMinutes(1);
            
            // Hitung selisih dalam menit
            $remaining = now()->diffInMinutes($deadlineWithTolerance, false);
            return max(0, $remaining);
        }
        return 0;
    }

    // Helper: Cek apakah waktu ujian habis
    public function isTimeUp()
    {
        if ($this->ujian && $this->ujian->durasi_menit == 0) {
            return false; // Unlimited time
        }
        return $this->remainingTime() <= 0;
    }
}
