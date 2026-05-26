<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    public const COMPONENT_FIELDS = [
        'tugas_1', 'tugas_2', 'tugas_3', 'tugas_4', 'tugas_5',
        'latihan_1', 'latihan_2', 'latihan_3', 'latihan_4', 'latihan_5',
        'uh_1', 'uh_2', 'uh_3', 'uh_4', 'uh_5',
        'pts', 'pas', 'keterampilan',
        'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek',
    ];

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'kelas_id',
        'tahun_ajaran_id',
        'semester',
        'guru_id',
        'edited_by_wali_id',
        'guru_terakhir_simpan_at',
        'wali_terakhir_edit_at',
        // 5 Tugas
        'tugas_1', 'tugas_2', 'tugas_3', 'tugas_4', 'tugas_5', 'rata_tugas',
        // 5 Latihan
        'latihan_1', 'latihan_2', 'latihan_3', 'latihan_4', 'latihan_5', 'rata_latihan',
        // 5 Ulangan Harian
        'uh_1', 'uh_2', 'uh_3', 'uh_4', 'uh_5', 'rata_uh',
        // PTS & PAS
        'pts', 'pas',
        // Final
        'nilai_akhir', 'keterampilan',
        // Khusus Kelas Tingkat Akhir
        'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek',
        // Snapshot dari Guru (sumber kebenaran nilai murni dari guru pengajar)
        'tugas_1_guru', 'tugas_2_guru', 'tugas_3_guru', 'tugas_4_guru', 'tugas_5_guru',
        'latihan_1_guru', 'latihan_2_guru', 'latihan_3_guru', 'latihan_4_guru', 'latihan_5_guru',
        'uh_1_guru', 'uh_2_guru', 'uh_3_guru', 'uh_4_guru', 'uh_5_guru',
        'pts_guru', 'pas_guru', 'keterampilan_guru',
        'to_1_guru', 'to_2_guru', 'to_3_guru', 'upk_guru', 'ujian_praktek_guru',
    ];

    protected $casts = [
        'tugas_1' => 'decimal:2', 'tugas_2' => 'decimal:2', 'tugas_3' => 'decimal:2',
        'tugas_4' => 'decimal:2', 'tugas_5' => 'decimal:2', 'rata_tugas' => 'decimal:2',
        'latihan_1' => 'decimal:2', 'latihan_2' => 'decimal:2', 'latihan_3' => 'decimal:2',
        'latihan_4' => 'decimal:2', 'latihan_5' => 'decimal:2', 'rata_latihan' => 'decimal:2',
        'uh_1' => 'decimal:2', 'uh_2' => 'decimal:2', 'uh_3' => 'decimal:2',
        'uh_4' => 'decimal:2', 'uh_5' => 'decimal:2', 'rata_uh' => 'decimal:2',
        'pts' => 'decimal:2', 'pas' => 'decimal:2',
        'nilai_akhir' => 'decimal:2', 'keterampilan' => 'decimal:2',
        'to_1' => 'decimal:2', 'to_2' => 'decimal:2', 'to_3' => 'decimal:2',
        'upk' => 'decimal:2', 'ujian_praktek' => 'decimal:2',
        // Snapshot fields
        'tugas_1_guru' => 'decimal:2', 'tugas_2_guru' => 'decimal:2', 'tugas_3_guru' => 'decimal:2',
        'tugas_4_guru' => 'decimal:2', 'tugas_5_guru' => 'decimal:2',
        'latihan_1_guru' => 'decimal:2', 'latihan_2_guru' => 'decimal:2', 'latihan_3_guru' => 'decimal:2',
        'latihan_4_guru' => 'decimal:2', 'latihan_5_guru' => 'decimal:2',
        'uh_1_guru' => 'decimal:2', 'uh_2_guru' => 'decimal:2', 'uh_3_guru' => 'decimal:2',
        'uh_4_guru' => 'decimal:2', 'uh_5_guru' => 'decimal:2',
        'pts_guru' => 'decimal:2', 'pas_guru' => 'decimal:2', 'keterampilan_guru' => 'decimal:2',
        'to_1_guru' => 'decimal:2', 'to_2_guru' => 'decimal:2', 'to_3_guru' => 'decimal:2',
        'upk_guru' => 'decimal:2', 'ujian_praktek_guru' => 'decimal:2',
        'guru_terakhir_simpan_at' => 'datetime',
        'wali_terakhir_edit_at' => 'datetime',
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

    public function editedByWali()
    {
        return $this->belongsTo(TenagaPendidik::class, 'edited_by_wali_id');
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
    // Formula: ((rata_tugas × 1) + (rata_latihan × 1) + (rata_uh × 2) + (pts × 3) + (pas × 3)) / 10
    public function hitungNilaiAkhir()
    {
        // Hitung rata-rata dulu
        $this->hitungSemuaRata();

        $rataTugas = $this->rata_tugas ?? 0;
        $rataLatihan = $this->rata_latihan ?? 0;
        $rataUH = $this->rata_uh ?? 0;
        $pts = $this->pts ?? 0;
        $pas = $this->pas ?? 0;

        // Formula baru: (rata_tugas×1 + rata_latihan×1 + rata_uh×2 + pts×3 + pas×3) / 10
        $total = ($rataTugas * 1) + ($rataLatihan * 1) + ($rataUH * 2) + ($pts * 3) + ($pas * 3);

        $this->nilai_akhir = $total / 10;
        $this->save();

        return $this->nilai_akhir;
    }

    // Helper: Hitung nilai rapor tengah semester (PTS)
    // Bobot default: Tugas 25%, Latihan 25%, UH 25%, PTS 25% (Total 100%) - Tanpa PAS
    public function hitungNilaiTengahSemester($bobot = null)
    {
        $bobot = $bobot ?? [
            'tugas' => 25,
            'latihan' => 25,
            'uh' => 25,
            'pts' => 25,
        ];

        // Hitung rata-rata dulu
        $this->hitungSemuaRata();

        $rataTugas = $this->rata_tugas ?? 0;
        $rataLatihan = $this->rata_latihan ?? 0;
        $rataUH = $this->rata_uh ?? 0;
        $pts = $this->pts ?? 0;

        $total =
            ($rataTugas * $bobot['tugas']) +
            ($rataLatihan * $bobot['latihan']) +
            ($rataUH * $bobot['uh']) +
            ($pts * $bobot['pts']);

        // Return nilai angka tanpa save ke nilai_akhir database (karena nilai_akhir di DB utk PAS)
        return $total / 100;
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

    public function isKelasAkhir(): bool
    {
        return $this->kelas ? $this->kelas->isTingkatAkhir() : false;
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

    /**
     * Get current semester based on current month
     * July-December = Ganjil, January-June = Genap
     */
    public static function getCurrentSemester(): string
    {
        $month = now()->month;
        return ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';
    }

    /**
     * Scope to filter by semester
     */
    public function scopeForSemester($query, string $semester)
    {
        return $query->where('semester', $semester);
    }

    public function hasGuruUpdate(): bool
    {
        foreach (self::COMPONENT_FIELDS as $field) {
            if ($this->valueDiffers($this->{$field}, $this->{$field . '_guru'})) {
                return true;
            }
        }
        return false;
    }

    public function diffWithGuru(): array
    {
        $diff = [];
        foreach (self::COMPONENT_FIELDS as $field) {
            $current = $this->{$field};
            $guru = $this->{$field . '_guru'};
            if ($this->valueDiffers($current, $guru)) {
                $diff[$field] = ['guru' => $guru, 'current' => $current];
            }
        }
        return $diff;
    }

    public function syncFromGuru(): void
    {
        foreach (self::COMPONENT_FIELDS as $field) {
            $this->{$field} = $this->{$field . '_guru'};
        }
        $this->wali_terakhir_edit_at = null;
        $this->edited_by_wali_id = null;
        $this->save();
        $this->hitungNilaiAkhir();
    }

    public function scopeWhereGuruDifferent($query)
    {
        return $query->where(function ($q) {
            foreach (self::COMPONENT_FIELDS as $field) {
                $q->orWhereColumn($field, '!=', $field . '_guru')
                  ->orWhere(function ($q2) use ($field) {
                      $q2->whereNotNull($field)->whereNull($field . '_guru');
                  })
                  ->orWhere(function ($q2) use ($field) {
                      $q2->whereNull($field)->whereNotNull($field . '_guru');
                  });
            }
        });
    }

    private function valueDiffers($a, $b): bool
    {
        if ($a === null && $b === null) return false;
        if ($a === null || $b === null) return true;
        return (float) $a !== (float) $b;
    }
}