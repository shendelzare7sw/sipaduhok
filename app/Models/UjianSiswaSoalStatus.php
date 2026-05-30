<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianSiswaSoalStatus extends Model
{
    use HasFactory;

    protected $table = 'ujian_siswa_soal_statuses';

    protected $fillable = [
        'ujian_siswa_id',
        'soal_ujian_id',
        'nomor_soal',
        'is_visited',
        'is_answered',
        'is_doubt',
        'last_visited_at',
        'last_answered_at',
        'last_doubt_at',
    ];

    protected $casts = [
        'is_visited' => 'boolean',
        'is_answered' => 'boolean',
        'is_doubt' => 'boolean',
        'last_visited_at' => 'datetime',
        'last_answered_at' => 'datetime',
        'last_doubt_at' => 'datetime',
    ];

    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class);
    }

    public function soalUjian()
    {
        return $this->belongsTo(SoalUjian::class);
    }
}
