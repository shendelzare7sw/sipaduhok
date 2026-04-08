<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleSheetsSyncLog extends Model
{
    protected $table = 'google_sheets_sync_logs';

    protected $fillable = [
        'module',
        'direction',
        'spreadsheet_id',
        'sheet_name',
        'rows_synced',
        'status',
        'error_message',
        'synced_by',
        'synced_at',
    ];

    protected $casts = [
        'rows_synced' => 'integer',
        'synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'synced_by');
    }

    /**
     * Scope: Filter by module
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope: Filter by direction (push/pull)
     */
    public function scopeDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get recent syncs
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('synced_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Scope: Get last sync for module
     */
    public function scopeLastSyncFor($query, $module, $direction = null)
    {
        $query = $query->where('module', $module);

        if ($direction) {
            $query = $query->where('direction', $direction);
        }

        return $query->latest('synced_at')->first();
    }

    /**
     * Check if sync was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if sync failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Get human-readable status
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'success' => '✅ Berhasil',
            'failed' => '❌ Gagal',
            'partial' => '⚠️ Sebagian',
            default => 'Unknown',
        };
    }

    /**
     * Get human-readable direction
     */
    public function getDirectionLabel(): string
    {
        return match ($this->direction) {
            'push' => '⬆️ Push (ke Google Sheets)',
            'pull' => '⬇️ Pull (dari Google Sheets)',
            default => 'Unknown',
        };
    }
}
