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
        // 5 Tugas
        'tugas_1',
        'tugas_2',
        'tugas_3',
        'tugas_4',
        'tugas_5',
        'rata_tugas',
        // 5 Latihan
        'latihan_1',
        'latihan_2',
        'latihan_3',
        'latihan_4',
        'latihan_5',
        'rata_latihan',
        // 5 Ulangan Harian
        'uh_1',
        'uh_2',
        'uh_3',
        'uh_4',
        'uh_5',
        'rata_uh',
        // PTS & PAS
        'pts',
        'pas',
        // Final
        'nilai_akhir',
        'keterampilan',
        // Khusus Kelas 9 & 12
        'to_1',
        'to_2',
        'to_3',
        'upk',
        'ujian_praktek',
    ];

    protected $casts = [
        'tugas_1' => 'decimal:2',
        'tugas_2' => 'decimal:2',
        'tugas_3' => 'decimal:2',
        'tugas_4' => 'decimal:2',
        'tugas_5' => 'decimal:2',
        'rata_tugas' => 'decimal:2',
        'latihan_1' => 'decimal:2',
        'latihan_2' => 'decimal:2',
        'latihan_3' => 'decimal:2',
        'latihan_4' => 'decimal:2',
        'latihan_5' => 'decimal:2',
        'rata_latihan' => 'decimal:2',
        'uh_1' => 'decimal:2',
        'uh_2' => 'decimal:2',
        'uh_3' => 'decimal:2',
        'uh_4' => 'decimal:2',
        'uh_5' => 'decimal:2',
        'rata_uh' => 'decimal:2',
        'pts' => 'decimal:2',
        'pas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'keterampilan' => 'decimal:2',
        'to_1' => 'decimal:2',
        'to_2' => 'decimal:2',
        'to_3' => 'decimal:2',
        'upk' => 'decimal:2',
        'ujian_praktek' => 'decimal:2',
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

    // Helper: Hitung rata-rata tugas
    public function hitungRataTugas()
    {
        $nilai = array_filter([
            $this->tugas_1,
            $this->tugas_2,
            $this->tugas_3,
            $this->tugas_4,
            $this->tugas_5
        ], fn($v) => $v !== null);

        if (empty($nilai))
            return null;

        $this->rata_tugas = array_sum($nilai) / count($nilai);
        return $this->rata_tugas;
    }

    // Helper: Hitung rata-rata latihan
    public function hitungRataLatihan()
    {
        $nilai = array_filter([
            $this->latihan_1,
            $this->latihan_2,
            $this->latihan_3,
            $this->latihan_4,
            $this->latihan_5
        ], fn($v) => $v !== null);

        if (empty($nilai))
            return null;

        $this->rata_latihan = array_sum($nilai) / count($nilai);
        return $this->rata_latihan;
    }

    // Helper: Hitung rata-rata ulangan harian
    public function hitungRataUH()
    {
        $nilai = array_filter([
            $this->uh_1,
            $this->uh_2,
            $this->uh_3,
            $this->uh_4,
            $this->uh_5
        ], fn($v) => $v !== null);

        if (empty($nilai))
            return null;

        $this->rata_uh = array_sum($nilai) / count($nilai);
        return $this->rata_uh;
    }

    // Helper: Hitung semua rata-rata
    public function hitungSemuaRata()
    {
        $this->hitungRataTugas();
        $this->hitungRataLatihan();
        $this->hitungRataUH();
        $this->save();
    }

    // Helper: Hitung nilai akhir dengan bobot
    // Bobot default: Tugas 15%, Latihan 15%, UH 20%, PTS 20%, PAS 30%
    public function hitungNilaiAkhir($bobot = null)
    {
        $bobot = $bobot ?? [
            'tugas' => 15,
            'latihan' => 15,
            'uh' => 20,
            'pts' => 20,
            'pas' => 30,
        ];

        // Hitung rata-rata dulu
        $this->hitungSemuaRata();

        $rataTugas = $this->rata_tugas ?? 0;
        $rataLatihan = $this->rata_latihan ?? 0;
        $rataUH = $this->rata_uh ?? 0;
        $pts = $this->pts ?? 0;
        $pas = $this->pas ?? 0;

        $total =
            ($rataTugas * $bobot['tugas']) +
            ($rataLatihan * $bobot['latihan']) +
            ($rataUH * $bobot['uh']) +
            ($pts * $bobot['pts']) +
            ($pas * $bobot['pas']);

        $this->nilai_akhir = $total / 100;
        $this->save();

        return $this->nilai_akhir;
    }

    // Helper: Konversi nilai angka ke huruf
    public function nilaiHuruf()
    {
        $nilai = $this->nilai_akhir;

        if ($nilai >= 90)
            return 'A';
        if ($nilai >= 80)
            return 'B';
        if ($nilai >= 70)
            return 'C';
        if ($nilai >= 60)
            return 'D';
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

    // Helper: Cek apakah siswa kelas akhir (9 atau 12)
    public function isKelasAkhir()
    {
        if (!$this->kelas)
            return false;

        $namaKelas = strtolower($this->kelas->nama_kelas);
        return str_contains($namaKelas, '9') ||
            str_contains($namaKelas, '12') ||
            str_contains($namaKelas, 'ix') ||
            str_contains($namaKelas, 'xii');
    }

    // Helper: Get nilai untuk kolom TO/UPK (hanya untuk kelas akhir)
    public function getNilaiUjianAkhir()
    {
        if (!$this->isKelasAkhir())
            return null;

        return [
            'to_1' => $this->to_1,
            'to_2' => $this->to_2,
            'to_3' => $this->to_3,
            'upk' => $this->upk,
            'ujian_praktek' => $this->ujian_praktek,
        ];
    }
}