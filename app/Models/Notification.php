<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'tipe',
        'judul',
        'pesan',
        'link',
        'data',
        'icon',
        'color',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Tipe notifikasi constants
    const TIPE_MATERI = 'materi';
    const TIPE_TUGAS = 'tugas';
    const TIPE_UJIAN = 'ujian';
    const TIPE_FORUM = 'forum';
    const TIPE_PENGUMUMAN = 'pengumuman';
    const TIPE_DEADLINE = 'deadline';
    const TIPE_NILAI = 'nilai';
    const TIPE_IZIN = 'izin';
    const TIPE_CATATAN = 'catatan';
    const TIPE_PEMBAYARAN = 'pembayaran';
    const TIPE_RAPOR = 'rapor';
    const TIPE_SISTEM = 'sistem';
    const TIPE_KELAS = 'kelas';
    const TIPE_KENAIKAN = 'kenaikan';
    const TIPE_RECOVERY = 'recovery';

    // Icon mapping
    public static function getIcon($tipe)
    {
        return match ($tipe) {
            self::TIPE_MATERI => 'fas fa-book',
            self::TIPE_TUGAS => 'fas fa-tasks',
            self::TIPE_UJIAN => 'fas fa-file-alt',
            self::TIPE_FORUM => 'fas fa-comments',
            self::TIPE_PENGUMUMAN => 'fas fa-bullhorn',
            self::TIPE_DEADLINE => 'fas fa-clock',
            self::TIPE_NILAI => 'fas fa-star',
            self::TIPE_IZIN => 'fas fa-file-medical',
            self::TIPE_CATATAN => 'fas fa-sticky-note',
            self::TIPE_PEMBAYARAN => 'fas fa-money-check-alt',
            self::TIPE_RAPOR => 'fas fa-graduation-cap',
            self::TIPE_SISTEM => 'fas fa-cog',
            self::TIPE_KELAS => 'fas fa-users',
            self::TIPE_KENAIKAN => 'fas fa-level-up-alt',
            self::TIPE_RECOVERY => 'fas fa-life-ring',
            default => 'fas fa-bell',
        };
    }

    // Color mapping
    public static function getColor($tipe)
    {
        return match ($tipe) {
            self::TIPE_MATERI => 'primary',
            self::TIPE_TUGAS => 'warning',
            self::TIPE_UJIAN => 'danger',
            self::TIPE_FORUM => 'info',
            self::TIPE_PENGUMUMAN => 'success',
            self::TIPE_DEADLINE => 'danger',
            self::TIPE_NILAI => 'success',
            self::TIPE_IZIN => 'warning',
            self::TIPE_CATATAN => 'info',
            self::TIPE_PEMBAYARAN => 'success',
            self::TIPE_RAPOR => 'primary',
            self::TIPE_SISTEM => 'secondary',
            self::TIPE_KELAS => 'info',
            self::TIPE_KENAIKAN => 'success',
            self::TIPE_RECOVERY => 'danger',
            default => 'secondary',
        };
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeOfType($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Normalize link to relative path so old absolute URLs
     * (e.g. http://sipaduhok.test/...) work correctly in production.
     */
    public function getLinkAttribute($value)
    {
        if ($value && filter_var($value, FILTER_VALIDATE_URL)) {
            $path  = parse_url($value, PHP_URL_PATH) ?? '/';
            $query = parse_url($value, PHP_URL_QUERY);
            return $path . ($query ? '?' . $query : '');
        }

        return $value;
    }

    // Helpers
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isUnread()
    {
        return is_null($this->read_at);
    }
}
