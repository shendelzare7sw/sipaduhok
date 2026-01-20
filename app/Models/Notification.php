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
