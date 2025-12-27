<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruPengajarKelas extends Model
{
    use HasFactory;

    protected $table = 'guru_pengajar_kelas';

    protected $fillable = [
        'tenaga_pendidik_id',
        'kelas_id',
        'mata_pelajaran_id',
    ];

    /**
     * Get the tenaga pendidik (guru).
     */
    public function tenagaPendidik()
    {
        return $this->belongsTo(TenagaPendidik::class, 'tenaga_pendidik_id');
    }

    /**
     * Get the kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Get the mata pelajaran.
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}
