<?php
// app/Models/Catatan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catatan extends Model
{
    use HasFactory;

    protected $table = 'catatan';

    protected $fillable = [
        'pengirim_id',
        'judul',
        'isi_catatan',
        'tipe_penerima',
        'role_penerima',
        'penerima_id',
        'prioritas',
        'is_read',
        'tanggal_kirim',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'tanggal_kirim' => 'datetime',
    ];

    // Relationships
    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function pembaca()
    {
        return $this->belongsToMany(User::class, 'catatan_dibaca', 'catatan_id', 'user_id')
            ->withPivot('dibaca_pada')
            ->withTimestamps();
    }

    // Helper: Tandai catatan sudah dibaca
    public function markAsRead($userId)
    {
        if (!$this->pembaca()->where('user_id', $userId)->exists()) {
            $this->pembaca()->attach($userId, ['dibaca_pada' => now()]);
        }
    }

    // Helper: Cek apakah user sudah baca
    public function isReadBy($userId)
    {
        return $this->pembaca()->where('user_id', $userId)->exists();
    }

    // Helper: Hitung total pembaca
    public function totalPembaca()
    {
        return $this->pembaca()->count();
    }
}