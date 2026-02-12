<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama_tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_mulai_genap',
        'is_active'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_mulai_genap' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapor()
    {
        return $this->hasMany(Rapor::class);
    }

    /**
     * Get current semester based on configured dates or fallback to month logic
     * July-December = Ganjil, January-June = Genap
     */
    public static function getCurrentSemester(): string
    {
        $tahunAjaran = static::where('is_active', true)->first();
        
        if ($tahunAjaran && $tahunAjaran->tanggal_mulai_genap) {
            // Use configured semester dates
            $today = now()->startOfDay();
            $genepStart = $tahunAjaran->tanggal_mulai_genap;
            
            return $today->lt($genepStart) ? 'ganjil' : 'genap';
        }
        
        // Fallback to month-based logic
        $month = now()->month;
        return ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';
    }

    /**
     * Get semester for a specific date
     */
    public function getSemesterForDate($date): string
    {
        $date = Carbon::parse($date)->startOfDay();
        
        if ($this->tanggal_mulai_genap) {
            return $date->lt($this->tanggal_mulai_genap) ? 'ganjil' : 'genap';
        }
        
        // Fallback to month-based logic
        return ($date->month >= 7 && $date->month <= 12) ? 'ganjil' : 'genap';
    }

    /**
     * Get semester period dates
     */
    public function getSemesterPeriods(): array
    {
        return [
            'ganjil' => [
                'start' => $this->tanggal_mulai,
                'end' => $this->tanggal_mulai_genap 
                    ? $this->tanggal_mulai_genap->copy()->subDay() 
                    : Carbon::parse($this->tanggal_mulai)->month(12)->endOfMonth(),
            ],
            'genap' => [
                'start' => $this->tanggal_mulai_genap ?? Carbon::parse($this->tanggal_selesai)->startOfYear(),
                'end' => $this->tanggal_selesai,
            ],
        ];
    }
}