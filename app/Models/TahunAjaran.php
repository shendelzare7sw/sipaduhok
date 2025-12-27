<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama_tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Relationships
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapor()
    {
        return $this->hasMany(Rapor::class);
    }
}