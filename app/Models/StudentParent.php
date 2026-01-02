<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentParent extends Model
{
    use HasFactory;

    protected $table = 'student_parents';

    protected $fillable = [
        'siswa_id',
        'parent_id',
        'relationship',
        'is_primary',
        'is_financial_responsible',
        'can_access_academic',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_financial_responsible' => 'boolean',
        'can_access_academic' => 'boolean',
    ];

    /**
     * Get the student (siswa) for this parent relationship
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Get the parent user for this relationship
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
