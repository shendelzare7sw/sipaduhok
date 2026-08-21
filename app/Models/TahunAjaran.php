<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
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
        'tanggal_akhir_pts_ganjil',
        'tanggal_akhir_pts_genap',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_mulai_genap' => 'date',
        'tanggal_akhir_pts_ganjil' => 'date',
        'tanggal_akhir_pts_genap' => 'date',
        'is_active' => 'boolean',
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
     * Get semester period dates (full semester range — untuk PAS / akhir_semester).
     * Ganjil: tanggal_mulai → (tanggal_mulai_genap - 1 day) atau Des akhir
     * Genap : tanggal_mulai_genap → tanggal_selesai
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

    /**
     * Get periode untuk rapor sesuai jenis (PTS vs PAS).
     *
     * Semua periode di-baca DINAMIS dari konfigurasi admin di menu
     * /admin/tahun-ajaran (field: tanggal_mulai, tanggal_mulai_genap,
     * tanggal_akhir_pts_ganjil, tanggal_akhir_pts_genap, tanggal_selesai).
     *
     * Mapping:
     * - PTS Ganjil : tanggal_mulai → tanggal_akhir_pts_ganjil
     * - PAS Ganjil : tanggal_mulai → tanggal_mulai_genap - 1 day
     * - PTS Genap  : tanggal_mulai_genap → tanggal_akhir_pts_genap
     * - PAS Genap  : tanggal_mulai_genap → tanggal_selesai
     *
     * Fallback kalau tanggal_akhir_pts_*  belum di-set admin:
     * → otomatis pakai 3 bulan pertama dari awal semester.
     *
     * @param  string  $semester  'ganjil' | 'genap'
     * @param  string  $jenisRapor  'tengah_semester' | 'akhir_semester'
     * @return array{start: \Carbon\Carbon, end: \Carbon\Carbon}
     */
    public function getRaporPeriod(string $semester, string $jenisRapor): array
    {
        $full = $this->getSemesterPeriods()[$semester] ?? null;
        if (! $full) {
            return [
                'start' => Carbon::parse($this->tanggal_mulai),
                'end' => Carbon::parse($this->tanggal_selesai),
            ];
        }

        $start = Carbon::parse($full['start']);
        $endFull = Carbon::parse($full['end']);

        if ($jenisRapor === 'tengah_semester') {
            // PTS: pakai tanggal admin dulu, fallback 3 bulan kalau kosong
            $ptsEnd = $semester === 'ganjil'
                ? $this->tanggal_akhir_pts_ganjil
                : $this->tanggal_akhir_pts_genap;

            if ($ptsEnd) {
                $end = Carbon::parse($ptsEnd);
            } else {
                // Fallback: 3 bulan pertama
                $end = $start->copy()->addMonths(3)->subDay();
            }

            // Clamp: PTS end tidak boleh melebihi end semester full
            if ($end->gt($endFull)) {
                $end = $endFull;
            }
            // Clamp: PTS end tidak boleh sebelum start semester
            if ($end->lt($start)) {
                $end = $start;
            }

            return ['start' => $start, 'end' => $end];
        }

        // PAS: full semester
        return ['start' => $start, 'end' => $endFull];
    }

    /**
     * Default jatuh tempo tagihan mengikuti akhir tahun ajaran.
     *
     * Tagihan massal adalah kewajiban untuk satu periode akademik penuh. Karena
     * itu, pada tahun ajaran lintas kalender (mis. 2026/2027), nilai awal form
     * harus ikut menunjuk ke 2027 dan tidak berhenti di tahun kalender saat ini.
     */
    public function getDefaultTagihanDueDate(): Carbon
    {
        return $this->tanggal_selesai->copy()->startOfDay();
    }

    /**
     * Normalisasi tanggal lama ke periode tahun ajaran target. Jika tahunnya
     * sudah usang, bulan dan tanggal dipertahankan selama masih masuk periode.
     */
    public function normalizeTagihanDueDate($date = null): Carbon
    {
        if (empty($date)) {
            return $this->getDefaultTagihanDueDate();
        }

        try {
            $parsed = Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            return $this->getDefaultTagihanDueDate();
        }

        if ($this->containsTagihanDate($parsed)) {
            return $parsed;
        }

        $startYear = $this->tanggal_mulai->year;
        $endYear = $this->tanggal_selesai->year;

        for ($year = $startYear; $year <= $endYear; $year++) {
            $candidate = Carbon::create($year, $parsed->month, 1)->startOfDay();
            $candidate->day(min($parsed->day, $candidate->daysInMonth));

            if ($this->containsTagihanDate($candidate)) {
                return $candidate;
            }
        }

        return $this->getDefaultTagihanDueDate();
    }

    public function containsTagihanDate($date): bool
    {
        $date = Carbon::parse($date)->startOfDay();

        return $date->betweenIncluded(
            $this->tanggal_mulai->copy()->startOfDay(),
            $this->tanggal_selesai->copy()->startOfDay()
        );
    }

    /**
     * Bulan kalender yang termasuk dalam tahun ajaran, lengkap dengan tahunnya.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getTagihanMonths(): array
    {
        $months = [];
        $cursor = $this->tanggal_mulai->copy()->startOfMonth();
        $lastMonth = $this->tanggal_selesai->copy()->startOfMonth();

        while ($cursor->lte($lastMonth)) {
            $months[] = [
                'value' => $cursor->format('Y-m'),
                'label' => $cursor->copy()->locale('id')->translatedFormat('F Y'),
            ];
            $cursor->addMonthNoOverflow();
        }

        return $months;
    }

    /**
     * Bangun jatuh tempo untuk bulan SPP tertentu dan batasi pada awal/akhir TA.
     */
    public function getTagihanDueDateForMonth(string $yearMonth, int $day): Carbon
    {
        $availableMonths = collect($this->getTagihanMonths())->pluck('value');

        if (! $availableMonths->contains($yearMonth)) {
            throw new \InvalidArgumentException('Bulan tagihan berada di luar tahun ajaran.');
        }

        $month = Carbon::createFromFormat('Y-m-d', $yearMonth.'-01')->startOfDay();
        $month->day(min(max($day, 1), $month->daysInMonth));

        return $this->clampTagihanDate($month);
    }

    private function clampTagihanDate(CarbonInterface $date): Carbon
    {
        $candidate = Carbon::parse($date)->startOfDay();
        $start = $this->tanggal_mulai->copy()->startOfDay();
        $end = $this->tanggal_selesai->copy()->startOfDay();

        if ($candidate->lt($start)) {
            return $start;
        }

        if ($candidate->gt($end)) {
            return $end;
        }

        return $candidate;
    }
}
