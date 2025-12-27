<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'jenis_tagihan',
        'jumlah',
        'tanggal_jatuh_tempo',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_jatuh_tempo' => 'date',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}