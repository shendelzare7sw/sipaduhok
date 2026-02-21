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
        // Midtrans fields
        'payment_gateway',
        'order_id',
        'transaction_id',
        'payment_type',
        'gateway_response',
        'paid_by_parent_id',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
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

    // Relationship to find all payments in the same bulk transaction
    public function groupTransactions()
    {
        return $this->hasMany(Pembayaran::class, 'order_id', 'order_id');
    }
}