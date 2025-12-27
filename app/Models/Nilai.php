<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'kelas_id',
        'tahun_ajaran_id',
        'guru_id',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_akhir',
    ];

    protected $casts = [
        'nilai_tugas' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(TenagaPendidik::class, 'guru_id');
    }

    public function raporNilai()
    {
        return $this->hasMany(RaporNilai::class);
    }

    // Helper: Hitung nilai akhir otomatis
    public function hitungNilaiAkhir($bobotTugas = 30, $bobotUTS = 30, $bobotUAS = 40)
    {
        $tugas = $this->nilai_tugas ?? 0;
        $uts = $this->nilai_uts ?? 0;
        $uas = $this->nilai_uas ?? 0;

        $this->nilai_akhir = (($tugas * $bobotTugas) + ($uts * $bobotUTS) + ($uas * $bobotUAS)) / 100;
        $this->save();

        return $this->nilai_akhir;
    }

    // Helper: Konversi nilai angka ke huruf
    public function nilaiHuruf()
    {
        $nilai = $this->nilai_akhir;

        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    // Helper: Predikat nilai
    public function predikat()
    {
        $huruf = $this->nilaiHuruf();

        $predikat = [
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            'D' => 'Kurang',
            'E' => 'Sangat Kurang',
        ];

        return $predikat[$huruf] ?? '-';
    }
}