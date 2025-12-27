<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapor extends Model
{
    use HasFactory;

    protected $table = 'rapor';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tahun_ajaran_id',
        'semester',
        'catatan_wali_kelas',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_alpha',
        'status',
        'tanggal_terbit',
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

    // Helper: Terbitkan rapor
    public function terbitkan()
    {
        $this->status = 'diterbitkan';
        $this->tanggal_terbit = now();
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
            // Buat atau update rapor nilai
            RaporNilai::updateOrCreate(
                [
                    'rapor_id' => $this->id,
                    'mata_pelajaran_id' => $nilai->mata_pelajaran_id,
                ],
                [
                    'nilai_id' => $nilai->id,
                    'nilai_angka' => $nilai->nilai_akhir,
                    'nilai_huruf' => $nilai->nilaiHuruf(),
                ]
            );
        }

        return $this;
    }
}