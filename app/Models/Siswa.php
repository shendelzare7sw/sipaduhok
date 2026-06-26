<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'cabang_id',
        'kelas_id',
        'nisn',
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_ayah',
        'nama_ibu',
        'telepon_orangtua',
        'foto',
        'tanggal_masuk',
        'status',
        // Validasi Akses fields
        'validasi_ujian_bendahara',
        'tanggal_validasi_ujian_bendahara',
        'validasi_ujian_wali',
        'tanggal_validasi_ujian_wali',
        'validasi_ujian_oleh',
        
        'validasi_rapor_bendahara',
        'tanggal_validasi_rapor_bendahara',
        'validasi_rapor_wali',
        'tanggal_validasi_rapor_wali',
        'validasi_rapor_oleh',
        // NEW: Ketua PKBM validation (3rd level)
        'validasi_rapor_ketua',
        'tanggal_validasi_rapor_ketua',
        'validasi_rapor_ketua_oleh',
        // New Religion Fields
        'agama',
        'pelajaran_agama',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_validasi_ujian_bendahara' => 'datetime',
        'tanggal_validasi_ujian_wali' => 'datetime',
        'tanggal_validasi_rapor_bendahara' => 'datetime',
        'tanggal_validasi_rapor_wali' => 'datetime',
        'tanggal_validasi_rapor_ketua' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function tugasSiswa()
    {
        return $this->hasMany(TugasSiswa::class);
    }

    public function ujianSiswa()
    {
        return $this->hasMany(UjianSiswa::class);
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapor()
    {
        return $this->hasMany(Rapor::class);
    }

    /**
     * Riwayat status naik kelas per tahun ajaran (snapshot dari PromotionService).
     * Source of truth untuk laporan historis: kelas_asal/kelas_tujuan/status_kelulusan.
     */
    public function statusNaikKelas()
    {
        return $this->hasMany(StatusNaikKelasSiswa::class);
    }

    /**
     * Scope: siswa yang relevan dengan TA tertentu.
     * - Punya snapshot di status_naik_kelas_siswa untuk TA itu, ATAU
     * - Sedang berada di kelas yang berada di TA itu (untuk siswa aktif TA aktif).
     */
    public function scopeForTahunAjaran($query, $tahunAjaranId)
    {
        return $query->where(function ($q) use ($tahunAjaranId) {
            $q->whereHas('statusNaikKelas', fn($s) => $s->where('tahun_ajaran_id', $tahunAjaranId))
              ->orWhereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
        });
    }

    // New relationship for parents (many-to-many)
    public function parents()
    {
        return $this->belongsToMany(User::class, 'student_parents', 'siswa_id', 'parent_id')
                    ->withPivot('relationship', 'is_primary', 'is_financial_responsible', 'can_access_academic')
                    ->withTimestamps();
    }

    // Alias untuk wali siswa (sama dengan parents)
    public function orangTua()
    {
        return $this->parents();
    }

    // Helper to get primary parent (penanggung jawab utama)
    public function primaryParent()
    {
        return $this->parents()->wherePivot('is_primary', true)->first();
    }

    // Helper to get parents who can access financial
    public function financialResponsibleParents()
    {
        return $this->parents()->wherePivot('is_financial_responsible', true)->get();
    }

    // Direct relationship to student_parents pivot table
    public function studentParents()
    {
        return $this->hasMany(StudentParent::class, 'siswa_id');
    }

    /**
     * Check if student can access the subject based on religion.
     */
    public function canAccessMapel($mapel)
    {
        if (!$mapel) return true;
        
        $mapelName = strtolower($mapel->nama_mapel);
        $myAgama = strtolower($this->agama ?? '');

        // List of basic religion keywords
        $religions = [
            'islam' => 'islam',
            'kristen' => 'kristen',
            'katolik' => 'katolik',
            'katholik' => 'katolik',
            'hindu' => 'hindu',
            'buddha' => 'buddha',
            'budha' => 'buddha',
            'konghucu' => 'konghucu'
        ];

        // Check mapel name for religion keywords
        $targetReligion = null;
        foreach ($religions as $keyword => $standardName) {
            if (str_contains($mapelName, $keyword)) {
                // Special check: Only if it really implies a religion class
                // e.g. "Sejarah Islam" might be history, not religion class?
                // User said "Agama Islam" or "Religi".
                // But usually "Pendidikan Agama Islam".
                // If the mapel name contains "agama" or "religi", treat as strict.
                // Or if it simply mentions the religion?
                
                // Let's check for 'agama' or 'religi' context first?
                // Or just assume any subject named "Islam" is restricted?
                // Safe bet: if it has "Agama" or "Religi" AND the keyword.
                // BUT User's request implied filtering "Agama Islam".
                // Let's go with permissive detection but strict matching.
                
                if (str_contains($mapelName, 'agama') || str_contains($mapelName, 'religi')) {
                    $targetReligion = $standardName;
                    break;
                }
            }
        }

        if ($targetReligion) {
            return str_contains($myAgama, $targetReligion);
        }

        return true;
    }

    /**
     * Check if student has full rapor access.
     * New flow: wali kirim → ketua approve → cek keuangan (lunas/dispensasi) → akses terbuka
     */
    public function hasFullRaporAccess(): bool
    {
        // Wali harus sudah kirim
        if (!$this->validasi_rapor_wali) {
            return false;
        }

        // Ketua harus sudah approve
        if (!$this->validasi_rapor_ketua) {
            return false;
        }

        // Bendahara sudah validasi (manual atau auto via lunas/dispensasi)
        if ($this->validasi_rapor_bendahara) {
            return true;
        }

        // Fallback: cek via service (lunas otomatis / dispensasi)
        return app(\App\Services\ValidasiAksesService::class)->cekAksesRapor($this);
    }
}