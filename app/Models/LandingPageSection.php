<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = ['landing_page_id', 'section_key', 'type', 'content', 'order', 'is_visible'];

    protected $casts = [
        'content' => 'array',
        'is_visible' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }
}
