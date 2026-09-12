<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenagaPendidik extends Model
{
    use HasFactory;

    protected $table = 'tenaga_pendidik';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'telepon',
        'email',
        'pendidikan_terakhir',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Batasi kandidat pengajar ke akun Guru Pengajar yang aktif.
     *
     * Kolom role lama tetap dipakai selama migrasi role_id. Jika role_id sudah
     * terisi, keduanya wajib konsisten agar role lama yang stale tidak membuka
     * kembali akses mengajar.
     */
    public function scopeWithTeachingRole(Builder $query): Builder
    {
        return $query->whereHas('user', function (Builder $userQuery): void {
            $userQuery
                ->where('role', 'guru_pengajar')
                ->where(function (Builder $roleQuery): void {
                    $roleQuery
                        ->whereNull('role_id')
                        ->orWhereHas('roleRelation', fn (Builder $relationQuery) => $relationQuery->where('name', 'guru_pengajar'));
                });
        });
    }

    public function scopeEligibleToTeach(Builder $query): Builder
    {
        return $query
            ->withTeachingRole()
            ->whereHas('user', fn (Builder $userQuery) => $userQuery->where('is_active', true));
    }

    public function isEligibleToTeach(): bool
    {
        if (! $this->relationLoaded('user')) {
            $this->load('user.roleRelation');
        } elseif ($this->user && ! $this->user->relationLoaded('roleRelation')) {
            $this->user->load('roleRelation');
        }

        if (! $this->user || ! $this->user->is_active || $this->user->role !== 'guru_pengajar') {
            return false;
        }

        return ! $this->user->role_id || $this->user->roleRelation?->name === 'guru_pengajar';
    }

    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }

    public function jadwalMengajar()
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'guru_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'guru_id');
    }

    public function ujian()
    {
        return $this->hasMany(Ujian::class, 'guru_id');
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'guru_id');
    }

    /**
     * Get penugasan mengajar guru ini.
     */
    public function guruKelas()
    {
        return $this->hasMany(\App\Models\GuruPengajarKelas::class, 'tenaga_pendidik_id');
    }

    /**
     * Get wali kelas assignments (many-to-many).
     */
    public function waliKelasAssignments()
    {
        return $this->hasMany(\App\Models\WaliKelasAssignment::class, 'tenaga_pendidik_id');
    }

    /**
     * Get all kelas yang dipegang sebagai wali kelas (via pivot).
     */
    public function kelasWaliMultiple()
    {
        return $this->belongsToMany(Kelas::class, 'wali_kelas_assignments', 'tenaga_pendidik_id', 'kelas_id')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }
}
