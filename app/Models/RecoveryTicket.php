<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecoveryTicket extends Model
{
    protected $fillable = [
        'user_id',
        'tipe_recovery',
        'status',
        'token_reset',
        'target_phone',
        'requested_ip',
        'user_agent',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
