<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'level',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Helper methods
    public function isSuperAdmin()
    {
        return $this->name === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->name === 'admin';
    }

    public function isBendahara()
    {
        return $this->name === 'bendahara';
    }

    public function isOrangTua()
    {
        return $this->name === 'orang_tua';
    }
}
