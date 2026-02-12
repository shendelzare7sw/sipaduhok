<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'guru_id',
        'kelas_id', // Deprecated, use pivot but kept for DB compatibility
        'mata_pelajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'status',
        'created_by',
        'updated_by',
        'siswa_ids',
        'tahun_ajaran_id',
    ];

    protected $casts = [
        'hari' => 'string',
        'jam_mulai' => 'datetime',
        'jam_selesai' => 'datetime',
        'siswa_ids' => 'array',
    ];

    // Relationships
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'jadwal_kelas');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function guru()
    {
        return $this->belongsTo(TenagaPendidik::class, 'guru_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function histories()
    {
        return $this->hasMany(JadwalPelajaranHistory::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByTahunAjaran($query, $tahunAjaranId)
    {
        return $query->where('tahun_ajaran_id', $tahunAjaranId);
    }

    public function scopeByKelas($query, $kelasId)
    {
        return $query->whereHas('kelas', function ($q) use ($kelasId) {
            $q->where('kelas.id', $kelasId);
        });
    }

    public function scopeByGuru($query, $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }
}