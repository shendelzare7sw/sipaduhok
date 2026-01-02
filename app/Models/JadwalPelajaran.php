<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'tahun_ajaran_id',
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'status',
        'keterangan',
        'updated_by',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    // Relationships
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
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
        return $query->where('kelas_id', $kelasId);
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