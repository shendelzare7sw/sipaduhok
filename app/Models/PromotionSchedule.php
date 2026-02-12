<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionSchedule extends Model
{
    use HasFactory;

    protected $table = 'promotion_schedules';

    protected $fillable = [
        'tahun_ajaran_id',
        'scheduled_at',
        'status',
        'created_by',
        'executed_at',
        'students_processed',
        'students_promoted',
        'students_graduated',
        'students_failed',
        'execution_log',
        'notify_on_complete',
        'notification_email',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'executed_at' => 'datetime',
        'notify_on_complete' => 'boolean',
    ];

    // Statuses
    const STATUS_PENDING = 'PENDING';
    const STATUS_RUNNING = 'RUNNING';
    const STATUS_COMPLETED = 'COMPLETED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_CANCELLED = 'CANCELLED';

    // Relationships
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReadyToExecute($query)
    {
        return $query->pending()
            ->where('scheduled_at', '<=', now());
    }

    // Helper Methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsRunning()
    {
        $this->update(['status' => self::STATUS_RUNNING]);
    }

    public function markAsCompleted($stats)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'executed_at' => now(),
            'students_processed' => $stats['processed'] ?? 0,
            'students_promoted' => $stats['promoted'] ?? 0,
            'students_graduated' => $stats['graduated'] ?? 0,
            'students_failed' => $stats['failed'] ?? 0,
            'execution_log' => $stats['log'] ?? null,
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'executed_at' => now(),
            'execution_log' => $errorMessage,
        ]);
    }

    public function cancel()
    {
        if ($this->isPending()) {
            $this->update(['status' => self::STATUS_CANCELLED]);
            return true;
        }
        return false;
    }
}
