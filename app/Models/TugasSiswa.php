<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasSiswa extends Model
{
    use HasFactory;

    protected $table = 'tugas_siswa';

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'file_jawaban',
        'jawaban_text',
        'tanggal_submit',
        'nilai',
        'status',
        'pengulangan_ke',
        'feedback_guru',
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
        'nilai' => 'decimal:2',
    ];

    // Relationships
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Helper: Cek apakah terlambat submit
    public function isLate()
    {
        if ($this->tanggal_submit && $this->tugas) {
            return $this->tanggal_submit->gt($this->tugas->tanggal_deadline);
        }
        return false;
    }
}