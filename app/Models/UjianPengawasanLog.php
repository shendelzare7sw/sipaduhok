<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianPengawasanLog extends Model
{
    use HasFactory;

    protected $table = 'ujian_pengawasan_logs';

    protected $fillable = [
        'ujian_siswa_id',
        'event_type',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function ujianSiswa()
    {
        return $this->belongsTo(UjianSiswa::class);
    }
}
