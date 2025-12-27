<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'judul_materi',
        'deskripsi',
        'file_materi',
        'tipe_file',
        'tanggal_upload',
    ];

    protected $casts = [
        'tanggal_upload' => 'date',
    ];

    // Relationships
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
}