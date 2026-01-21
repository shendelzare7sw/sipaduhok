<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumReply extends Model
{
    use HasFactory;

    protected $table = 'forum_replies';

    protected $fillable = [
        'forum_diskusi_id',
        'user_id',
        'parent_id',
        'isi',
        'attachment',
        'attachment_type',
        'is_answer',
    ];

    protected $casts = [
        'is_answer' => 'boolean',
        'attachment' => 'array',
    ];

    // Relationships
    public function forumDiskusi()
    {
        return $this->belongsTo(ForumDiskusi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    // Scopes
    public function scopeAnswers($query)
    {
        return $query->where('is_answer', true);
    }

    public function scopeRootReplies($query)
    {
        return $query->whereNull('parent_id');
    }

    // Helpers
    public function isFromTeacher()
    {
        return $this->user && in_array($this->user->role, ['guru_pengajar', 'wali_kelas', 'admin']);
    }

    public function markAsAnswer()
    {
        $this->update(['is_answer' => true]);
    }

    /**
     * Check if the reply can be edited by the user.
     * Students/Users can only edit their own reply within 1 hour.
     * Teachers/Admins can always edit (if implemented).
     */
    public function getCanEditAttribute()
    {
        // If user is not logged in, cannot edit
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // If user is owner
        if ($this->user_id === $user->id) {
            // Check if user is student (role logic might vary, assuming 'siswa' or checking if NOT teacher/admin)
            // Or simply apply the time limit to everyone or just students. Requirement says "siswa reply ini hanya bisa bertahan 1 jam saja".

            if ($user->role === 'siswa') {
                return $this->created_at->diffInHours(now()) < 1;
            }

            // Non-students (Teacher) might have unlimited time or different rules.
            return true;
        }

        return false;
    }
}
