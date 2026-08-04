<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Flyer;
use App\Models\KalenderAkademik;
use App\Models\Pengumuman;
use App\Models\TahunAjaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekretarisController extends Controller
{
    // ============================================
    // DASHBOARD
    // ============================================

    public function dashboard()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        // Stats
        $stats = [
            'totalKalender' => KalenderAkademik::where('tahun_ajaran_id', $tahunAjaranAktif?->id)->count(),
            'kegiatanAktif' => KalenderAkademik::where('tahun_ajaran_id', $tahunAjaranAktif?->id)->aktif()->count(),
            'pengumumanAktif' => Pengumuman::aktif()->count(),
            'flyerAktif' => Flyer::aktif()->count(),
            'beritaAktif' => Berita::aktif()->count(),
        ];

        // Kegiatan hari ini dan mendatang
        $kegiatanHariIni = KalenderAkademik::where('tahun_ajaran_id', $tahunAjaranAktif?->id)
            ->hariIni()
            ->aktif()
            ->orderBy('tanggal_mulai')
            ->get();

        return view('dashboard.sekretaris', compact('stats', 'kegiatanHariIni', 'tahunAjaranAktif'));
    }

    // ============================================
    // KALENDER AKADEMIK
    // ============================================

    /**
     * Get view path (can be overridden by child controller)
     */
    protected function viewPath($view)
    {
        return $view;
    }

    // Kalender Akademik

    public function kalenderToggleVisibility($id)
    {
        try {
            \Log::info('Toggle Visibility Request for ID: '.$id);
            $kalender = \App\Models\KalenderAkademik::findOrFail($id);
            \Log::info('Current status: '.$kalender->is_hidden_siswa);

            $kalender->is_hidden_siswa = ! $kalender->is_hidden_siswa;
            $saved = $kalender->save();

            \Log::info('New status: '.$kalender->is_hidden_siswa.' | Saved: '.($saved ? 'Yes' : 'No'));

            return response()->json([
                'success' => true,
                'is_hidden' => $kalender->is_hidden_siswa,
                'message' => $kalender->is_hidden_siswa ? 'Kegiatan disembunyikan dari siswa' : 'Kegiatan ditampilkan ke siswa',
            ]);
        } catch (\Exception $e) {
            \Log::error('Toggle Error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function kalenderIndex(Request $request)
    {

        \Carbon\Carbon::setLocale('id');
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        // Jika tidak ada tahun ajaran aktif, buat default
        if (! $tahunAjaranAktif) {
            $tahunAjaranAktif = (object) ['nama_tahun_ajaran' => 'Belum Ada', 'id' => null];
        }

        // --- LIST VIEW DATA (Legacy/Existing with Pagination) ---
        $kalender = KalenderAkademik::with('tahunAjaran')
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id ?? null)
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(15);

        // --- ALL EVENTS FOR JAVASCRIPT VISUAL CALENDAR (without pagination) ---
        $allCalendarEvents = KalenderAkademik::with('tahunAjaran')
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id ?? null)
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // --- VISUAL CALENDAR LOGIC (New) ---
        // View Mode: 'bulan' (default), 'minggu', 'tahun'
        $viewMode = $request->get('mode', 'bulan');

        // Parameter Tanggal/Bulan/Tahun
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Base Date untuk navigasi Mingguan (jika mode minggu)
        $dateParam = $request->get('date');
        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::createFromDate($year, $month, 1);

        if ($viewMode === 'minggu') {
            $month = $baseDate->month;
            $year = $baseDate->year;
        }

        // Query Utama Visual (Tanpa scope aktif agar admin bisa lihat draft)
        // Adjust for visibility: Admin/Sekretaris should see everything.
        // If we strictly want what Students see, use scopeAktif().
        // But Admins need to see everything.
        $queryEvents = KalenderAkademik::where('tahun_ajaran_id', $tahunAjaranAktif->id);

        // --- DATA LOGIC: TAHUNAN ---
        $eventsTahun = collect([]);
        if ($viewMode === 'tahun') {
            $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

            $eventsTahun = (clone $queryEvents)->where(function ($q) use ($startOfYear, $endOfYear) {
                $q->whereBetween('tanggal_mulai', [$startOfYear, $endOfYear])
                    ->orWhereBetween('tanggal_selesai', [$startOfYear, $endOfYear])
                    ->orWhere(function ($sq) use ($startOfYear, $endOfYear) {
                        $sq->where('tanggal_mulai', '<=', $startOfYear)
                            ->where('tanggal_selesai', '>=', $endOfYear);
                    });
            })->orderBy('tanggal_mulai')->get();
        }

        // --- DATA LOGIC: MINGGUAN ---
        $eventsMinggu = collect([]);
        $startOfWeek = $baseDate->copy()->startOfWeek();
        $endOfWeek = $baseDate->copy()->endOfWeek();
        $weekDays = [];

        if ($viewMode === 'minggu') {
            $eventsMinggu = (clone $queryEvents)->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                    ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek])
                    ->orWhere(function ($sq) use ($startOfWeek, $endOfWeek) {
                        $sq->where('tanggal_mulai', '<=', $startOfWeek)
                            ->where('tanggal_selesai', '>=', $endOfWeek);
                    });
            })->orderBy('tanggal_mulai')->get();

            $tmpDate = $startOfWeek->copy();
            while ($tmpDate->lte($endOfWeek)) {
                $weekDays[] = $tmpDate->copy();
                $tmpDate->addDay();
            }
        }

        // --- DATA LOGIC: BULANAN (Default & Sidebar) ---
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $events = (clone $queryEvents)->where(function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                    $q2->where('tanggal_mulai', '<=', $startOfMonth)
                        ->where('tanggal_selesai', '>=', $endOfMonth);
                });
        })->orderBy('tanggal_mulai')->get();

        // --- GRID GENERATION ---
        $calendarDays = [];
        if ($viewMode === 'bulan') {
            $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Minggu)

            // Add empty cells
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                $prevDate = $startOfMonth->copy()->subDays($firstDayOfWeek - $i);
                $calendarDays[] = [
                    'day' => $prevDate->day,
                    'fullDate' => $prevDate->format('Y-m-d'),
                    'isOtherMonth' => true,
                    'isToday' => false,
                    'events' => collect([]),
                ];
            }

            for ($day = 1; $day <= $endOfMonth->day; $day++) {
                $currentDate = Carbon::createFromDate($year, $month, $day);

                $dayEvents = $events->filter(function ($event) use ($currentDate) {
                    $start = Carbon::parse($event->tanggal_mulai)->startOfDay();
                    $end = $event->tanggal_selesai
                        ? Carbon::parse($event->tanggal_selesai)->endOfDay()
                        : $start->copy()->endOfDay();

                    return $currentDate->between($start, $end);
                });

                $calendarDays[] = [
                    'day' => $day,
                    'fullDate' => $currentDate->format('Y-m-d'),
                    'isOtherMonth' => false,
                    'isToday' => $currentDate->isToday(),
                    'events' => $dayEvents,
                ];
            }

            // Fill remaining
            $remaining = 7 - (count($calendarDays) % 7);
            if ($remaining < 7) {
                for ($i = 1; $i <= $remaining; $i++) {
                    $nextDate = $endOfMonth->copy()->addDays($i);
                    $calendarDays[] = [
                        'day' => $nextDate->day,
                        'fullDate' => $nextDate->format('Y-m-d'),
                        'isOtherMonth' => true,
                        'isToday' => false,
                        'events' => collect([]),
                    ];
                }
            }
        }

        // --- NAVIGATION HELPERS ---
        $prevMonth = [
            'month' => $startOfMonth->copy()->subMonth()->month,
            'year' => $startOfMonth->copy()->subMonth()->year,
        ];
        $nextMonth = [
            'month' => $startOfMonth->copy()->addMonth()->month,
            'year' => $startOfMonth->copy()->addMonth()->year,
        ];

        $prevWeekDate = $startOfWeek->copy()->subWeek()->format('Y-m-d');
        $nextWeekDate = $startOfWeek->copy()->addWeek()->format('Y-m-d');
        $prevYear = $year - 1;
        $nextYear = $year + 1;

        // AJAX Response for Navigation
        if ($request->ajax()) {
            return response()->json([
                'html' => view($this->viewPath('sekretaris.kalender.index'), compact( // Use dynamic viewPath
                    'kalender',
                    'allCalendarEvents',
                    'tahunAjaranAktif',
                    'year',
                    'month',
                    'calendarDays',
                    'prevMonth',
                    'nextMonth',
                    'events',
                    'viewMode',
                    'eventsMinggu',
                    'eventsTahun',
                    'startOfWeek',
                    'endOfWeek',
                    'prevWeekDate',
                    'nextWeekDate',
                    'prevYear',
                    'nextYear',
                    'baseDate',
                    'weekDays'
                ))->render(),
            ]);
        }

        return view('sekretaris.kalender.index', compact(
            'kalender',
            'allCalendarEvents',
            'tahunAjaranAktif',
            'year',
            'month',
            'calendarDays',
            'prevMonth',
            'nextMonth',
            'events',
            'viewMode',
            'eventsMinggu',
            'eventsTahun',
            'startOfWeek',
            'endOfWeek',
            'prevWeekDate',
            'nextWeekDate',
            'prevYear',
            'nextYear',
            'baseDate',
            'weekDays'
        ));
    }

    public function kalenderCreate()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        return view('sekretaris.kalender.form', compact('tahunAjaranAktif'));
    }

    public function kalenderStore(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'keterangan' => 'nullable|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'custom_jenis_kegiatan' => 'nullable|string|max:50',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        // Process custom event type
        if ($request->jenis_kegiatan === 'lainnya' && $request->custom_jenis_kegiatan) {
            $validated['jenis_kegiatan'] = $request->custom_jenis_kegiatan;
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $validated['tahun_ajaran_id'] = $tahunAjaranAktif->id;

        if ($request->hasFile('lampiran_surat')) {
            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('kalender/lampiran', 'public');
        }

        KalenderAkademik::create($validated);

        return redirect_to_previous('sekretaris.kalender.index')
            ->with('success', 'Kalender akademik berhasil ditambahkan!');
    }

    public function kalenderShow($id)
    {
        $kalender = KalenderAkademik::findOrFail($id);

        return view('sekretaris.kalender.show', compact('kalender'));
    }

    public function kalenderEdit($id)
    {
        $kalender = KalenderAkademik::findOrFail($id);
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        return view('sekretaris.kalender.form', compact('kalender', 'tahunAjaranAktif'));
    }

    public function kalenderUpdate(Request $request, $id)
    {
        $kalender = KalenderAkademik::findOrFail($id);

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'waktu_mulai' => 'nullable|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i|after:waktu_mulai',
            'keterangan' => 'nullable|string',
            'jenis_kegiatan' => 'required|string|max:100',
            'custom_jenis_kegiatan' => 'nullable|string|max:50',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        // Process custom event type
        if ($request->jenis_kegiatan === 'lainnya' && $request->custom_jenis_kegiatan) {
            $validated['jenis_kegiatan'] = $request->custom_jenis_kegiatan;
        }

        if ($request->hasFile('lampiran_surat')) {
            if ($kalender->lampiran_surat) {
                Storage::disk('public')->delete($kalender->lampiran_surat);
            }

            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('kalender/lampiran', 'public');
        }

        $kalender->update($validated);

        // Update pengumuman terkait
        if ($kalender->pengumuman()->exists()) {
            $kalender->pengumuman()->update([
                'judul' => 'Pengingat: '.$validated['nama_kegiatan'],
                'isi_pengumuman' => "Kegiatan {$validated['nama_kegiatan']} akan dilaksanakan pada tanggal ".
                    Carbon::parse($validated['tanggal_mulai'])->format('d F Y').'. '.($validated['keterangan'] ?? ''),
            ]);
        }

        return redirect_to_previous('sekretaris.kalender.index')
            ->with('success', 'Kalender akademik berhasil diperbarui!');
    }

    public function kalenderDestroy($id)
    {
        $kalender = KalenderAkademik::findOrFail($id);

        if ($kalender->lampiran_surat) {
            Storage::disk('public')->delete($kalender->lampiran_surat);
        }

        $kalender->delete();

        return redirect_to_previous('sekretaris.kalender.index')
            ->with('success', 'Kalender akademik berhasil dihapus!');
    }

    public function kalenderBulanan(Request $request)
    {
        try {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

            if (! $tahunAjaranAktif) {
                return response()->json([]);
            }

            // Get bulan parameter (format: YYYY-MM)
            $bulan = $request->get('bulan', now()->format('Y-m'));

            // Parse tanggal
            $startOfMonth = Carbon::parse($bulan.'-01')->startOfMonth();
            $endOfMonth = Carbon::parse($bulan.'-01')->endOfMonth();

            // Query dengan kondisi yang lebih fleksibel
            $kegiatan = KalenderAkademik::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->where('status', 'aktif')
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    // Event yang mulai dalam bulan ini
                    $q->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                        // ATAU event yang selesai dalam bulan ini
                        ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                        // ATAU event yang melewati bulan ini (mulai sebelum, selesai sesudah)
                        ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                            $q2->where('tanggal_mulai', '<=', $startOfMonth)
                                ->where('tanggal_selesai', '>=', $endOfMonth);
                        });
                })
                ->orderBy('tanggal_mulai')
                ->get();

            // Map ke format FullCalendar
            $events = $kegiatan->map(function ($k) {
                // FullCalendar menggunakan exclusive end date
                // Jadi tanggal_selesai harus ditambah 1 hari
                $endDate = $k->tanggal_selesai
                    ? Carbon::parse($k->tanggal_selesai)->addDay()->format('Y-m-d')
                    : null;

                return [
                    'id' => $k->id,
                    'title' => $k->nama_kegiatan,
                    'start' => Carbon::parse($k->tanggal_mulai)->format('Y-m-d'),
                    'end' => $endDate,
                    'backgroundColor' => $this->getEventColor($k->jenis_kegiatan),
                    'borderColor' => $this->getEventColor($k->jenis_kegiatan),
                    'extendedProps' => [
                        'jenis' => $k->jenis_label,
                        'keterangan' => $k->keterangan,
                    ],
                ];
            });

            return response()->json($events);

        } catch (\Exception $e) {
            \Log::error('❌ Kalender Bulanan Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Gagal memuat kalender: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cetak Kalender Akademik (Bulanan atau Tahunan)
     */
    public function kalenderCetak(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (! $tahunAjaranAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $jenisCetak = $request->get('jenis', 'bulanan'); // bulanan atau tahunan
        $bulan = $request->get('bulan', now()->format('Y-m'));

        if ($jenisCetak === 'bulanan') {
            return $this->cetakBulanan($tahunAjaranAktif, $bulan);
        } else {
            return $this->cetakTahunan($tahunAjaranAktif);
        }
    }

    /**
     * Cetak Kalender Bulanan
     */
    private function cetakBulanan($tahunAjaran, $bulan)
    {
        $tanggal = Carbon::parse($bulan.'-01');
        $startOfMonth = $tanggal->copy()->startOfMonth();
        $endOfMonth = $tanggal->copy()->endOfMonth();

        // Get all events in this month
        $kegiatan = KalenderAkademik::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('status', 'aktif')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                        $q2->where('tanggal_mulai', '<=', $startOfMonth)
                            ->where('tanggal_selesai', '>=', $endOfMonth);
                    });
            })
            ->orderBy('tanggal_mulai')
            ->get();

        // Build calendar grid
        $calendarGrid = $this->buildCalendarGrid($tanggal, $kegiatan);

        $data = [
            'tahunAjaran' => $tahunAjaran,
            'bulan' => $tanggal->translatedFormat('F Y'),
            'tanggal' => $tanggal,
            'kegiatan' => $kegiatan,
            'calendarGrid' => $calendarGrid,
            'namaBulan' => $tanggal->translatedFormat('F'),
            'tahun' => $tanggal->year,
        ];

        $pdf = Pdf::loadView('sekretaris.kalender.cetak-bulanan', $data)
            ->setPaper('a4', 'landscape');

        // ⭐ FIX: Replace slash dengan dash
        $fileName = 'Kalender-'.$tanggal->format('F-Y').'.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Cetak Kalender Tahunan (List)
     */
    private function cetakTahunan($tahunAjaran)
    {
        $kegiatan = KalenderAkademik::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('status', 'aktif')
            ->orderBy('tanggal_mulai')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_mulai)->format('Y-m');
            });

        $data = [
            'tahunAjaran' => $tahunAjaran,
            'kegiatanPerBulan' => $kegiatan,
        ];

        $pdf = Pdf::loadView('sekretaris.kalender.cetak-tahunan', $data)
            ->setPaper('a4', 'portrait');

        // ⭐ FIX: Replace slash dengan dash
        $fileName = 'Kalender-Akademik-'.str_replace('/', '-', $tahunAjaran->nama_tahun_ajaran).'.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Build calendar grid untuk tampilan kalender
     */
    private function buildCalendarGrid($tanggal, $kegiatan)
    {
        $startOfMonth = $tanggal->copy()->startOfMonth();
        $endOfMonth = $tanggal->copy()->endOfMonth();

        // Get first day of week (0 = Sunday, 1 = Monday)
        $firstDayOfWeek = $startOfMonth->dayOfWeek;

        // Adjust untuk mulai dari Senin (0 = Senin)
        $firstDayOfWeek = ($firstDayOfWeek === 0) ? 6 : $firstDayOfWeek - 1;

        $daysInMonth = $startOfMonth->daysInMonth;

        // Build grid
        $grid = [];
        $week = 0;

        // Initialize first week with empty cells
        $grid[$week] = array_fill(0, 7, null);

        // Fill calendar
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayOfWeek = ($firstDayOfWeek + $day - 1) % 7;

            if ($dayOfWeek === 0 && $day > 1) {
                $week++;
                $grid[$week] = array_fill(0, 7, null);
            }

            $currentDate = $startOfMonth->copy()->day($day);

            // Get events for this day
            $eventsForDay = $kegiatan->filter(function ($event) use ($currentDate) {
                $start = Carbon::parse($event->tanggal_mulai);
                $end = $event->tanggal_selesai ? Carbon::parse($event->tanggal_selesai) : $start;

                return $currentDate->between($start, $end);
            });

            $grid[$week][$dayOfWeek] = [
                'date' => $day,
                'fullDate' => $currentDate,
                'events' => $eventsForDay,
                'isToday' => $currentDate->isToday(),
            ];
        }

        return $grid;
    }

    /**
     * Get event color (HANYA SATU METHOD INI YANG DIPAKAI)
     */
    private function getEventColor($jenis)
    {
        $colors = [
            'field_trip' => '#17a2b8',
            'outing' => '#28a745',
            'live_in' => '#6610f2',
            'hokfest' => '#fd7e14',
            'pts' => '#ffc107',
            'pas' => '#dc3545',
            'libur' => '#6c757d',
            'ujian' => '#e83e8c',
            'acara_sekolah' => '#20c997',
            'lainnya' => '#007bff',
        ];

        return $colors[$jenis] ?? '#007bff';
    }
    // ============================================
    // PENGUMUMAN
    // ============================================

    public function pengumumanIndex()
    {
        $pengumuman = Pengumuman::with(['kalenderAkademik', 'pembuat'])
            ->orderBy('tanggal_pengumuman', 'desc')
            ->paginate(15);

        return view('sekretaris.pengumuman.index', compact('pengumuman'));
    }

    public function pengumumanCreate()
    {
        $kalender = KalenderAkademik::aktif()
            ->where('status', 'aktif')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('sekretaris.pengumuman.form', compact('kalender'));
    }

    public function pengumumanStore(Request $request)
    {
        $validated = $request->validate([
            'kalender_akademik_id' => 'nullable|exists:kalender_akademik,id',
            'judul' => 'required|string|max:255',
            'isi_pengumuman' => 'required|string',
            'tanggal_pengumuman' => 'required|date',
            'prioritas' => 'required|in:biasa,penting,mendesak',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,arsip',
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['is_from_kalender'] = $request->filled('kalender_akademik_id');

        if ($request->hasFile('lampiran_surat')) {
            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('pengumuman/lampiran', 'public');
        }

        Pengumuman::create($validated);

        return redirect_to_previous('sekretaris.pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan!');
    }

    public function pengumumanEdit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $kalender = KalenderAkademik::aktif()
            ->where('status', 'aktif')
            ->orderBy('tanggal_mulai')
            ->get();

        return view('sekretaris.pengumuman.form', compact('pengumuman', 'kalender'));
    }

    public function pengumumanUpdate(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $validated = $request->validate([
            'kalender_akademik_id' => 'nullable|exists:kalender_akademik,id',
            'judul' => 'required|string|max:255',
            'isi_pengumuman' => 'required|string',
            'tanggal_pengumuman' => 'required|date',
            'prioritas' => 'required|in:biasa,penting,mendesak',
            'lampiran_surat' => 'nullable|file|mimes:pdf|max:5120',
            'status' => 'required|in:draft,aktif,arsip',
        ]);

        if ($request->hasFile('lampiran_surat')) {
            if ($pengumuman->lampiran_surat) {
                Storage::disk('public')->delete($pengumuman->lampiran_surat);
            }

            $validated['lampiran_surat'] = $request->file('lampiran_surat')
                ->store('pengumuman/lampiran', 'public');
        }

        $pengumuman->update($validated);

        return redirect_to_previous('sekretaris.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function pengumumanDestroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->lampiran_surat) {
            Storage::disk('public')->delete($pengumuman->lampiran_surat);
        }

        $pengumuman->delete();

        return redirect_to_previous('sekretaris.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus!');
    }

    // ============================================
    // FLYER
    // ============================================

    public function flyerIndex()
    {
        $flyer = Flyer::with('pembuat')
            ->orderBy('urutan_tampil')
            ->paginate(15);

        return view('sekretaris.flyer.index', compact('flyer'));
    }

    public function flyerCreate()
    {
        return view('sekretaris.flyer.form');
    }

    public function flyerStore(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar_flyer' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_audience' => 'required|in:semua,siswa,guru,wali_kelas,orang_tua',
            'urutan_tampil' => 'required|integer|min:1',
            'status' => 'required|in:draft,aktif,nonaktif',
        ]);

        $validated['dibuat_oleh'] = auth()->id();

        if ($request->hasFile('gambar_flyer')) {
            $validated['gambar_flyer'] = $request->file('gambar_flyer')
                ->store('flyer/gambar', 'public');
        }

        Flyer::create($validated);

        return redirect_to_previous('sekretaris.flyer.index')
            ->with('success', 'Flyer berhasil ditambahkan!');
    }

    public function flyerEdit($id)
    {
        $flyer = Flyer::findOrFail($id);

        return view('sekretaris.flyer.form', compact('flyer'));
    }

    public function flyerUpdate(Request $request, $id)
    {
        $flyer = Flyer::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'gambar_flyer' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'target_audience' => 'required|in:semua,siswa,guru,wali_kelas,orang_tua',
            'urutan_tampil' => 'required|integer|min:1',
            'status' => 'required|in:draft,aktif,nonaktif',
        ]);

        if ($request->hasFile('gambar_flyer')) {
            if ($flyer->gambar_flyer) {
                Storage::disk('public')->delete($flyer->gambar_flyer);
            }

            $validated['gambar_flyer'] = $request->file('gambar_flyer')
                ->store('flyer/gambar', 'public');
        }

        $flyer->update($validated);

        return redirect_to_previous('sekretaris.flyer.index')
            ->with('success', 'Flyer berhasil diperbarui!');
    }

    public function flyerDestroy($id)
    {
        $flyer = Flyer::findOrFail($id);

        if ($flyer->gambar_flyer) {
            Storage::disk('public')->delete($flyer->gambar_flyer);
        }

        $flyer->delete();

        return redirect_to_previous('sekretaris.flyer.index')
            ->with('success', 'Flyer berhasil dihapus!');
    }

    // ============================================
    // BERITA MANAGEMENT
    // ============================================

    public function beritaIndex(Request $request)
    {
        $query = Berita::with('pembuat')->ordered();

        // Filter kategori
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->kategori($request->kategori);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $berita = $query->paginate(15);
        $kategoriOptions = Berita::getKategoriOptions();
        $statusOptions = Berita::getStatusOptions();

        return view('sekretaris.berita.index', compact('berita', 'kategoriOptions', 'statusOptions'));
    }

    public function beritaCreate()
    {
        $kategoriOptions = Berita::getKategoriOptions();
        $statusOptions = Berita::getStatusOptions();

        return view('sekretaris.berita.form', compact('kategoriOptions', 'statusOptions'));
    }

    public function beritaStore(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string|max:500',
            'gambar_thumbnail' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'url_berita' => 'required|url|max:500',
            'kategori' => 'required|in:kegiatan,prestasi,pengumuman,artikel,ujian',
            'tanggal_berita' => 'required|date',
            'is_featured' => 'boolean',
            'urutan_tampil' => 'required|integer|min:1|max:999',
            'status' => 'required|in:draft,aktif,arsip',
        ], [
            'judul.required' => 'Judul berita harus diisi',
            'deskripsi_singkat.required' => 'Deskripsi singkat harus diisi',
            'deskripsi_singkat.max' => 'Deskripsi singkat maksimal 500 karakter',
            'gambar_thumbnail.required' => 'Gambar thumbnail harus diunggah',
            'gambar_thumbnail.image' => 'File harus berupa gambar',
            'gambar_thumbnail.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar_thumbnail.max' => 'Ukuran gambar maksimal 2MB',
            'url_berita.required' => 'URL berita harus diisi',
            'url_berita.url' => 'URL berita tidak valid',
            'kategori.required' => 'Kategori harus dipilih',
            'tanggal_berita.required' => 'Tanggal berita harus diisi',
            'urutan_tampil.required' => 'Urutan tampil harus diisi',
        ]);

        $validated['dibuat_oleh'] = auth()->id();
        $validated['is_featured'] = $request->has('is_featured');

        // Handle upload gambar
        if ($request->hasFile('gambar_thumbnail')) {
            try {
                $file = $request->file('gambar_thumbnail');
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                // Pastikan folder ada
                $destinationPath = public_path('img/berita');
                if (! file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Pindahkan file
                $file->move($destinationPath, $filename);
                $validated['gambar_thumbnail'] = $filename;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_thumbnail' => 'Gagal mengunggah gambar: '.$e->getMessage()])
                    ->withInput();
            }
        }

        Berita::create($validated);

        return redirect_to_previous('sekretaris.berita.index')
            ->with('success', 'Berita berhasil ditambahkan!');
    }

    public function beritaEdit($id)
    {
        $berita = Berita::findOrFail($id);
        $kategoriOptions = Berita::getKategoriOptions();
        $statusOptions = Berita::getStatusOptions();

        return view('sekretaris.berita.form', compact('berita', 'kategoriOptions', 'statusOptions'));
    }

    public function beritaUpdate(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi_singkat' => 'required|string|max:500',
            'gambar_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'url_berita' => 'required|url|max:500',
            'kategori' => 'required|in:kegiatan,prestasi,pengumuman,artikel,ujian',
            'tanggal_berita' => 'required|date',
            'is_featured' => 'boolean',
            'urutan_tampil' => 'required|integer|min:1|max:999',
            'status' => 'required|in:draft,aktif,arsip',
        ], [
            'judul.required' => 'Judul berita harus diisi',
            'deskripsi_singkat.required' => 'Deskripsi singkat harus diisi',
            'deskripsi_singkat.max' => 'Deskripsi singkat maksimal 500 karakter',
            'gambar_thumbnail.image' => 'File harus berupa gambar',
            'gambar_thumbnail.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'gambar_thumbnail.max' => 'Ukuran gambar maksimal 2MB',
            'url_berita.required' => 'URL berita harus diisi',
            'url_berita.url' => 'URL berita tidak valid',
            'kategori.required' => 'Kategori harus dipilih',
            'tanggal_berita.required' => 'Tanggal berita harus diisi',
            'urutan_tampil.required' => 'Urutan tampil harus diisi',
        ]);

        $validated['is_featured'] = $request->has('is_featured');

        // Handle upload gambar baru
        if ($request->hasFile('gambar_thumbnail')) {
            try {
                // Hapus gambar lama
                if ($berita->gambar_thumbnail) {
                    $oldImagePath = public_path('img/berita/'.$berita->gambar_thumbnail);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $file = $request->file('gambar_thumbnail');
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                // Pastikan folder ada
                $destinationPath = public_path('img/berita');
                if (! file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                // Pindahkan file
                $file->move($destinationPath, $filename);
                $validated['gambar_thumbnail'] = $filename;
            } catch (\Exception $e) {
                return back()->withErrors(['gambar_thumbnail' => 'Gagal mengunggah gambar: '.$e->getMessage()])
                    ->withInput();
            }
        }

        $berita->update($validated);

        return redirect_to_previous('sekretaris.berita.index')
            ->with('success', 'Berita berhasil diperbarui!');
    }

    public function beritaDestroy($id)
    {
        $berita = Berita::findOrFail($id);

        // Hapus gambar
        if ($berita->gambar_thumbnail) {
            $imagePath = public_path('img/berita/'.$berita->gambar_thumbnail);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $berita->delete();

        return redirect_to_previous('sekretaris.berita.index')
            ->with('success', 'Berita berhasil dihapus!');
    }

    public function beritaToggleFeatured($id)
    {
        $berita = Berita::findOrFail($id);
        $berita->is_featured = ! $berita->is_featured;
        $berita->save();

        $status = $berita->is_featured ? 'ditampilkan sebagai berita utama' : 'dihapus dari berita utama';

        return response()->json([
            'success' => true,
            'message' => "Berita berhasil {$status}",
            'is_featured' => $berita->is_featured,
        ]);
    }
}
