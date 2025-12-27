<?php
// app/Models/CatatanDibaca.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanDibaca extends Model
{
    use HasFactory;

    protected $table = 'catatan_dibaca';

    protected $fillable = [
        'catatan_id',
        'user_id',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    // Relationships
    public function catatan()
    {
        return $this->belongsTo(Catatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}