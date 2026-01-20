<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumDiskusi extends Model
{
    use HasFactory;

    protected $table = 'forum_diskusi';

    protected $fillable = [
        'mata_pelajaran_id',
        'pertemuan_id',
        'kelas_id',
        'user_id',
        'topik',
        'judul',
        'isi',
        'reference_id',
        'is_pinned',
        'is_closed',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_closed' => 'boolean',
    ];

    // Topik constants
    const TOPIK_MATERI = 'materi';
    const TOPIK_TUGAS = 'tugas';
    const TOPIK_UJIAN = 'ujian';
    const TOPIK_UMUM = 'umum';

    // Relationships
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class)->orderBy('created_at', 'asc');
    }

    public function latestReply()
    {
        return $this->hasOne(ForumReply::class)->latest();
    }

    // Scopes
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }

    public function scopeOfTopic($query, $topik)
    {
        return $query->where('topik', $topik);
    }

    // Helpers
    public function getReplyCount()
    {
        return $this->replies()->count();
    }

    public function hasAnswer()
    {
        return $this->replies()->where('is_answer', true)->exists();
    }

    public function isFromTeacher()
    {
        return $this->user && in_array($this->user->role, ['guru_pengajar', 'wali_kelas', 'admin']);
    }
}
