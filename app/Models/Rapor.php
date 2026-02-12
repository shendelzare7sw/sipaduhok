<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Presensi;
use App\Models\RaporKegiatanEkstra;

class Rapor extends Model
{
    use HasFactory;

    protected $table = 'rapor';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tahun_ajaran_id',
        'semester',
        'jenis_rapor',
        'catatan_wali_kelas',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpha',
        'status',
        'tanggal_terbit',
        'uploaded_pdf_path', // NEW: Path to uploaded PDF file
        'input_mode', // NEW: auto_generate or upload_pdf
        'allow_download', // NEW: Download control toggle
    ];

    protected $casts = [
        'jumlah_sakit' => 'integer',
        'jumlah_izin' => 'integer',
        'jumlah_alpha' => 'integer',
        'tanggal_terbit' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function raporNilai()
    {
        return $this->hasMany(RaporNilai::class);
    }

    public function kegiatanEkstra()
    {
        return $this->hasMany(RaporKegiatanEkstra::class);
    }

    // Helper: Terbitkan rapor
    public function terbitkan()
    {
        $this->status = 'diterbitkan';
        $this->tanggal_terbit = now();
        $this->save();

        return $this;
    }

    // Helper: Tarik kembali rapor yang sudah diterbitkan
    public function tarikKembali()
    {
        $this->status = 'draft';
        $this->save();

        return $this;
    }

    // Helper: Hitung rata-rata nilai
    public function rataRataNilai()
    {
        return $this->raporNilai()->avg('nilai_angka');
    }

    // Helper: Generate rapor dari data nilai
    public function generateFromNilai()
    {
        // Ambil semua nilai siswa di kelas dan tahun ajaran ini
        $nilaiList = Nilai::where('siswa_id', $this->siswa_id)
            ->where('kelas_id', $this->kelas_id)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->get();

        foreach ($nilaiList as $nilai) {
            // Tentukan nilai angka berdasarkan jenis rapor
            $nilaiAngka = 0;
            if ($this->jenis_rapor == 'tengah_semester') {
                $nilaiAngka = $nilai->hitungNilaiTengahSemester();
            } else {
                // Akhir Semester (Default)
                $nilaiAngka = $nilai->nilai_akhir;
            }

            // Buat atau update rapor nilai
            RaporNilai::updateOrCreate(
                [
                    'rapor_id' => $this->id,
                    'mata_pelajaran_id' => $nilai->mata_pelajaran_id,
                ],
                [
                    'nilai_id' => $nilai->id,
                    'nilai_angka' => $nilaiAngka,
                    'nilai_huruf' => $this->konversiHuruf($nilaiAngka), // Use helper for consistency
                ]
            );
        }

        return $this;
    }

    // Helper local untuk konversi huruf jika logic beda atau reuse
    private function konversiHuruf($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    /**
     * Auto-fill kehadiran (attendance) from presensi table.
     * Calculate sakit, izin, alpha based on semester period.
     */
    public function hitungKehadiranOtomatis()
    {
        // Get tahun ajaran period
        $tahunAjaran = $this->tahunAjaran;
        if (!$tahunAjaran) {
            return $this;
        }

        // Determine date range based on semester
        $startDate = $this->semester === 'ganjil'
            ? $tahunAjaran->tanggal_mulai
            : date('Y-m-d', strtotime($tahunAjaran->tanggal_mulai . ' +6 months'));

        $endDate = $this->semester === 'ganjil'
            ? date('Y-m-d', strtotime($tahunAjaran->tanggal_mulai . ' +6 months'))
            : $tahunAjaran->tanggal_selesai;

        // Count from presensi table
        $presensi = Presensi::where('siswa_id', $this->siswa_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as total_alpha
            ')
            ->first();

        $this->jumlah_sakit = $presensi->total_sakit ?? 0;
        $this->jumlah_izin = $presensi->total_izin ?? 0;
        $this->jumlah_alpha = $presensi->total_alpha ?? 0;
        $this->save();

        return $this;
    }

    /**
     * Get periode label (PTS Ganjil, PAS Genap, etc.).
     */
    public function getPeriodeLabel(): string
    {
        $jenisLabel = $this->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS';
        $semesterLabel = ucfirst($this->semester);

        return "$jenisLabel $semesterLabel";
    }

    /**
     * Scope to filter by periode (semester + jenis_rapor).
     */
    public function scopeByPeriode($query, $semester, $jenisRapor)
    {
        return $query->where('semester', $semester)
                     ->where('jenis_rapor', $jenisRapor);
    }
}