<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporNilai extends Model
{
    use HasFactory;

    protected $table = 'rapor_nilai';

    protected $fillable = [
        'rapor_id',
        'mata_pelajaran_id',
        'nilai_id',
        'nilai_angka',
        'nilai_huruf',
        'deskripsi',
    ];

    protected $casts = [
        'nilai_angka' => 'decimal:2',
    ];

    // Relationships
    public function rapor()
    {
        return $this->belongsTo(Rapor::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function nilai()
    {
        return $this->belongsTo(Nilai::class);
    }
}