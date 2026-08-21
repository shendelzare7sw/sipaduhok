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
        // Data payment gateway
        'payment_gateway',
        'order_id',
        'transaction_id',
        'payment_type',
        'gateway_response',
        'payment_url',
        'gateway_total',
        'payment_environment',
        'gateway_status',
        'payment_expires_at',
        'gateway_settled_at',
        'gateway_error',
        'gateway_attempts',
        'paid_by_parent_id',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
        'tanggal_validasi' => 'datetime',
        'gateway_response' => 'array',
        'gateway_total' => 'decimal:2',
        'payment_expires_at' => 'datetime',
        'gateway_settled_at' => 'datetime',
        'gateway_attempts' => 'integer',
    ];

    public function getPaymentChannelLabelAttribute(): string
    {
        if ($this->metode_pembayaran === 'tunai') {
            return 'Tunai';
        }

        if ($this->metode_pembayaran === 'transfer') {
            return 'Direct Transfer';
        }

        if ($this->payment_gateway !== 'paywuz' && $this->metode_pembayaran !== 'paywuz') {
            return ucfirst((string) $this->metode_pembayaran);
        }

        $code = strtoupper(trim((string) $this->payment_type));

        return match ($code) {
            'QRIS' => 'QRIS',
            'VA' => 'Virtual Account (Pilih Bank)',
            'BCAVA' => 'BCA Virtual Account',
            '014' => 'BCA Virtual Account',
            'BNIVA' => 'BNI Virtual Account',
            '009' => 'BNI Virtual Account',
            'BRIVA' => 'BRI Virtual Account',
            '002' => 'BRI Virtual Account',
            'BSIVA' => 'BSI Virtual Account',
            '451' => 'BSI Virtual Account',
            'CIMBVA' => 'CIMB Niaga Virtual Account',
            '022' => 'CIMB Niaga Virtual Account',
            'DANAMONVA' => 'Danamon Virtual Account',
            '011' => 'Danamon Virtual Account',
            'MANDIRIVA' => 'Mandiri Virtual Account',
            '008' => 'Mandiri Virtual Account',
            'MAYBANKVA' => 'Maybank Virtual Account',
            '016' => 'Maybank Virtual Account',
            'OCBCVA' => 'OCBC Virtual Account',
            '028' => 'OCBC Virtual Account',
            'PERMATAVA' => 'Permata Virtual Account',
            '013' => 'Permata Virtual Account',
            'ALFAMART' => 'Alfamart',
            'INDOMARET' => 'Indomaret',
            '' => 'Kanal Pembayaran',
            default => str_ends_with($code, 'VA')
                ? substr($code, 0, -2).' Virtual Account'
                : str($code)->replace('_', ' ')->title()->toString(),
        };
    }

    public function getPaymentChannelGroupAttribute(): string
    {
        $code = strtoupper(trim((string) $this->payment_type));

        return match (true) {
            $code === 'QRIS' => 'qris',
            in_array($code, ['ALFAMART', 'INDOMARET'], true) => 'retail',
            $code === 'VA' || str_ends_with($code, 'VA') || preg_match('/^\d{3}$/', $code) === 1 => 'va',
            default => 'digital',
        };
    }

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
