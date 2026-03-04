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
        'personal_email',
        'foto_profil',
        'username',
        'password',
        'security_question',
        'security_answer',
        'security_pin',
        'phone',
        'avatar',
        'role', // keep for backward compatibility during migration
        'role_id', // new role system
        'cabang_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'security_answer',
        'security_pin',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
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

    // New role-based relationship
    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // For parent users - students they are responsible for
    public function students()
    {
        return $this->belongsToMany(Siswa::class, 'student_parents', 'parent_id', 'siswa_id')
                    ->withPivot('relationship', 'is_primary', 'is_financial_responsible', 'can_access_academic')
                    ->withTimestamps();
    }

    // Payments made by this user (if parent)
    public function payments()
    {
        return $this->hasMany(Pembayaran::class, 'paid_by_parent_id');
    }

    // Financial audit logs created by this user
    public function financialAuditLogs()
    {
        return $this->hasMany(FinancialAuditLog::class);
    }

    // Helper methods untuk check role (updated for role_id system with fallback)
    public function isAdmin()
    {
        // Support new role_id system
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'admin';
        }
        // Fallback to old enum (attribute 'role')
        return $this->attributes['role'] === 'admin';
    }

    // Alias - Admin IS the super admin (level 1)
    public function isSuperAdmin()
    {
        return $this->isAdmin();
    }

    public function isKetuaPKBM()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'ketua_pkbm';
        }
        return $this->attributes['role'] === 'ketua_pkbm';
    }

    public function isWakilKepalaSekolah()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'wakil_kepala_sekolah';
        }
        return $this->attributes['role'] === 'wakil_kepala_sekolah';
    }

    public function isSekretaris()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'sekretaris';
        }
        return $this->attributes['role'] === 'sekretaris';
    }

    public function isBendahara()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'bendahara';
        }
        return $this->attributes['role'] === 'bendahara';
    }

    public function isWaliKelas()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'wali_kelas';
        }
        return $this->attributes['role'] === 'wali_kelas';
    }

    public function isGuruPengajar()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'guru_pengajar';
        }
        return $this->attributes['role'] === 'guru_pengajar';
    }

    public function isSiswa()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'siswa';
        }
        return $this->attributes['role'] === 'siswa';
    }

    public function isOrangTua()
    {
        if ($this->role_id && $this->roleRelation) {
            return $this->roleRelation->name === 'orang_tua';
        }
        return $this->attributes['role'] === 'orang_tua';
    }

    // Check if user has role level equal or higher
    public function hasRoleLevel($level)
    {
        return $this->role_id && $this->roleRelation && $this->roleRelation->level <= $level;
    }

    // Check if user can access specific feature based on role level
    public function canAccessFinancial()
    {
        return $this->isAdmin() || $this->isBendahara();
    }

    public function canManageUsers()
    {
        return $this->isAdmin();
    }

    public function canImpersonate()
    {
        return $this->isAdmin(); // Only admin (level 1) can impersonate
    }

    /**
     * Relasi untuk orang tua - mendapatkan anak-anak (siswa)
     * Menggunakan tabel pivot student_parents
     */
    public function children()
    {
        return $this->belongsToMany(Siswa::class, 'student_parents', 'parent_id', 'siswa_id')
                    ->withPivot('relationship', 'is_primary', 'is_financial_responsible', 'can_access_academic')
                    ->withTimestamps();
    }

    /**
     * Relasi ke tabel student_parents (untuk query detail parent-student relationship)
     */
    public function studentParents()
    {
        return $this->hasMany(StudentParent::class, 'parent_id');
    }
}