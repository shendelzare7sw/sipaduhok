<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaliKelasAssignment extends Model
{
    use HasFactory;

    protected $table = 'wali_kelas_assignments';

    protected $fillable = [
        'tenaga_pendidik_id',
        'kelas_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * Get the tenaga pendidik (wali kelas).
     */
    public function tenagaPendidik()
    {
        return $this->belongsTo(TenagaPendidik::class, 'tenaga_pendidik_id');
    }

    /**
     * Get the kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
