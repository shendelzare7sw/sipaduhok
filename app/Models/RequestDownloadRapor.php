<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RequestDownloadRapor extends Model
{
    protected $table = 'request_download_rapor';

    protected $fillable = [
        'rapor_id',
        'user_id',
        'siswa_id',
        'status',
        'alasan',
        'diputuskan_oleh',
        'catatan_admin',
        'tanggal_request',
        'tanggal_keputusan',
        'download_expired_at',
        'download_token',
    ];

    protected $casts = [
        'tanggal_request' => 'datetime',
        'tanggal_keputusan' => 'datetime',
        'download_expired_at' => 'datetime',
    ];

    public function rapor()
    {
        return $this->belongsTo(Rapor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function pemutus()
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    public function isExpired(): bool
    {
        return $this->download_expired_at && now()->gt($this->download_expired_at);
    }

    public function isDownloadable(): bool
    {
        return $this->status === 'disetujui' && $this->download_token && !$this->isExpired();
    }

    public function generateDownloadToken(int $hoursValid = 24): void
    {
        $this->update([
            'download_token' => Str::random(64),
            'download_expired_at' => now()->addHours($hoursValid),
        ]);
    }
}
