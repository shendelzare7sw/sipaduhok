<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'cabang_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function tenagaPendidik()
    {
        return $this->hasOne(TenagaPendidik::class);
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }

    // Helper methods untuk check role
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isKetuaPKBM()
    {
        return $this->role === 'ketua_pkbm';
    }

    public function isBendahara()
    {
        return $this->role === 'bendahara';
    }

    public function isWaliKelas()
    {
        return $this->role === 'wali_kelas';
    }

    public function isGuruPengajar()
    {
        return $this->role === 'guru_pengajar';
    }

    public function isSiswa()
    {
        return $this->role === 'siswa';
    }
}