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
        'paywuz_sandbox_api_key',
        'paywuz_production_api_key',
        'paywuz_is_production',
        'paywuz_enabled',
        'paywuz_fee_by_merchant',
        'tunai_lokasi',
        'tunai_jam_operasional',
        'tunai_deskripsi',
        'updated_by',
    ];

    protected $casts = [
        'paywuz_is_production' => 'boolean',
        'paywuz_enabled' => 'boolean',
        'paywuz_fee_by_merchant' => 'boolean',
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

        if (! $info) {
            $info = self::create([]);
        }

        return $info;
    }

    /**
     * Check apakah rekening bank sudah diatur
     */
    public function hasRekeningBank()
    {
        return ! empty($this->rekening_bank) &&
               ! empty($this->nama_bank) &&
               ! empty($this->atas_nama);
    }

    /**
     * Cek kesiapan konfigurasi Paywuz untuk environment yang sedang aktif.
     */
    public function hasPaywuz(): bool
    {
        return filled($this->getPaywuzApiKey($this->paywuz_is_production ? 'production' : 'sandbox'));
    }

    public function isPaywuzEnabled(): bool
    {
        return $this->hasPaywuz() && (bool) $this->paywuz_enabled;
    }

    public function getPaywuzApiKey(string $environment): ?string
    {
        $column = $environment === 'production'
            ? 'paywuz_production_api_key'
            : 'paywuz_sandbox_api_key';
        $encrypted = $this->{$column};

        if (blank($encrypted)) {
            return $environment === 'production'
                ? config('services.paywuz.production_api_key')
                : config('services.paywuz.sandbox_api_key');
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return null;
        }
    }

    public function getPaywuzModeAttribute(): string
    {
        return $this->paywuz_is_production ? 'Production' : 'Sandbox';
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
     * Check apakah info tunai sudah diatur
     */
    public function hasTunaiInfo()
    {
        return ! empty($this->tunai_lokasi) ||
               ! empty($this->tunai_jam_operasional) ||
               ! empty($this->tunai_deskripsi);
    }

    /**
     * Accessor untuk mendapatkan info tunai dalam format array
     */
    public function getTunaiInfoAttribute()
    {
        return [
            'lokasi' => $this->tunai_lokasi ?? 'Loket Pembayaran Sekolah',
            'jam_operasional' => $this->tunai_jam_operasional ?? 'Senin - Jumat, 08:00 - 15:00 WIB',
            'deskripsi' => $this->tunai_deskripsi ?? 'Harap membawa kartu siswa atau bukti identitas.',
        ];
    }
}
