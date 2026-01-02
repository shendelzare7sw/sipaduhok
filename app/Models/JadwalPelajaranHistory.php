<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaranHistory extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran_history';

    protected $fillable = [
        'jadwal_pelajaran_id',
        'field_changed',
        'old_value',
        'new_value',
        'keterangan',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    // Relationships
    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
