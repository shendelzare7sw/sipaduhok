<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsMeeting extends Model
{
    use HasFactory;

    protected $table = 'lms_meetings';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'guru_id',
        'judul',
        'platform',
        'link_meeting',
        'waktu_mulai',
        'waktu_selesai',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_active' => 'boolean',
    ];

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
