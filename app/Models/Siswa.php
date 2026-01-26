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
        'validasi_ujian_wali',
        'tanggal_validasi_ujian_wali',
        'validasi_ujian_oleh',
        'validasi_rapor_bendahara',
        'validasi_rapor_wali',
        'tanggal_validasi_rapor_wali',
        'validasi_rapor_oleh',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
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

    // New relationship for parents (many-to-many)
    public function parents()
    {
        return $this->belongsToMany(User::class, 'student_parents', 'siswa_id', 'parent_id')
                    ->withPivot('relationship', 'is_primary', 'is_financial_responsible', 'can_access_academic')
                    ->withTimestamps();
    }

    // Alias untuk orang tua (sama dengan parents)
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
}