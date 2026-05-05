<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanMonitoring extends Model
{
    use HasFactory;

    protected $table = 'catatan_monitoring';

    const KONTEN_MATERI = 'materi';
    const KONTEN_TUGAS = 'tugas';
    const KONTEN_UJIAN = 'ujian';

    protected $fillable = [
        'pengirim_id',
        'pengirim_role',
        'guru_id',
        'konten_type',
        'konten_id',
        'kelas_id',
        'mata_pelajaran_id',
        'isi_catatan',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function guru()
    {
        return $this->belongsTo(TenagaPendidik::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function konten()
    {
        return match ($this->konten_type) {
            self::KONTEN_MATERI => Materi::find($this->konten_id),
            self::KONTEN_TUGAS => Tugas::find($this->konten_id),
            self::KONTEN_UJIAN => Ujian::find($this->konten_id),
            default => null,
        };
    }

    public function kontenJudul(): string
    {
        $konten = $this->konten();
        if (!$konten) return '(konten dihapus)';
        return $konten->judul_materi ?? $konten->judul_tugas ?? $konten->judul_ujian ?? '-';
    }

    public function kontenLabel(): string
    {
        return match ($this->konten_type) {
            self::KONTEN_MATERI => 'Materi',
            self::KONTEN_TUGAS => 'Tugas/Latihan',
            self::KONTEN_UJIAN => 'Ujian/Latihan',
            default => 'Konten',
        };
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('dibaca_pada');
    }

    public function scopeForGuru($query, int $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    public function markAsRead(): void
    {
        if (!$this->dibaca_pada) {
            $this->update(['dibaca_pada' => now()]);
        }
    }
}
