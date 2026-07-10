<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateCapaianKompetensi extends Model
{
    use HasFactory;

    protected $table = 'template_capaian_kompetensi';

    protected $fillable = [
        'mata_pelajaran_id',
        'template_text',
        'created_by',
    ];

    /**
     * Get the mata pelajaran (subject) that this template belongs to.
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
