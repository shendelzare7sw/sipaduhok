<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'siswa_id',
        'kode_pembayaran',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status_validasi',
        'divalidasi_oleh',
        'tanggal_validasi',
        'catatan',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
        'tanggal_validasi' => 'datetime',
    ];

    // Relationships
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}