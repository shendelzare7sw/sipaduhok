<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaporKegiatanEkstra extends Model
{
    use HasFactory;

    protected $table = 'rapor_kegiatan_ekstra';

    protected $fillable = [
        'rapor_id',
        'kegiatan_nama',
        'predikat',
        'keterangan',
    ];

    /**
     * Get the rapor that owns this ekstrakurikuler record.
     */
    public function rapor(): BelongsTo
    {
        return $this->belongsTo(Rapor::class, 'rapor_id');
    }

    /**
     * Get the default 7 ekstrakurikuler activities.
     *
     * @return array
     */
    public static function getDefaultKegiatan(): array
    {
        return [
            'Life Skill',
            'Live In',
            'Menggambar',
            'Karate',
            'Pengembangan Karakter',
            'Literasi',
            'Seni Musik',
        ];
    }
}
