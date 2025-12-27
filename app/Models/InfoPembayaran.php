<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class InfoPembayaran extends Model
{
    use HasFactory;

    protected $table = 'info_pembayaran';

    protected $fillable = [
        'nama_bank',
        'rekening_bank',
        'atas_nama',
        'midtrans_merchant_id',
        'midtrans_server_key',
        'midtrans_client_key',
        'midtrans_is_production',
        'updated_by',
    ];

    protected $casts = [
        'midtrans_is_production' => 'boolean',
    ];

    /**
     * Relasi ke User yang terakhir update
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get instance info pembayaran (singleton pattern)
     * Hanya ada 1 row di tabel ini
     */
    public static function getInstance()
    {
        $info = self::first();
        
        if (!$info) {
            $info = self::create([]);
        }
        
        return $info;
    }

    /**
     * Check apakah rekening bank sudah diatur
     */
    public function hasRekeningBank()
    {
        return !empty($this->rekening_bank) && 
               !empty($this->nama_bank) && 
               !empty($this->atas_nama);
    }

    /**
     * Check apakah Midtrans sudah dikonfigurasi
     */
    public function hasMidtrans()
    {
        return !empty($this->midtrans_merchant_id) && 
               !empty($this->midtrans_server_key) && 
               !empty($this->midtrans_client_key);
    }

    /**
     * Get display text untuk mode Midtrans
     */
    public function getMidtransModeAttribute()
    {
        return $this->midtrans_is_production ? 'Production' : 'Sandbox';
    }

    /**
     * Get decrypted server key
     */
    public function getDecryptedServerKey()
    {
        if (empty($this->midtrans_server_key)) {
            return null;
        }
        
        try {
            return Crypt::decryptString($this->midtrans_server_key);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Accessor untuk mendapatkan info rekening dalam format array
     */
    public function getRekeningInfoAttribute()
    {
        return [
            'nama_bank' => $this->nama_bank,
            'rekening_bank' => $this->rekening_bank,
            'atas_nama' => $this->atas_nama,
        ];
    }

    /**
     * Accessor untuk mendapatkan info Midtrans dalam format array
     */
    public function getMidtransInfoAttribute()
    {
        return [
            'merchant_id' => $this->midtrans_merchant_id,
            'client_key' => $this->midtrans_client_key,
            'is_production' => $this->midtrans_is_production,
        ];
    }
}