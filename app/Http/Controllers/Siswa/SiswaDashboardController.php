<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Pengumuman;
use App\Models\Flyer;
use App\Models\KalenderAkademik;
use App\Models\JadwalPelajaran;
use App\Models\TenagaPendidik;
use App\Models\Presensi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\Materi;
use Carbon\Carbon;

class SiswaDashboardController extends Controller
{
    /**
     * Main Dashboard Router - Redirect ke SIA Dashboard
     */
    public function index()
    {
        // Default redirect ke SIA Dashboard
        return redirect()->route('siswa.sia.dashboard');
    }

    /**
     * Dashboard SIA - Tampilan pertama setelah login
     */
    public function dashboardSIA()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran', 'kelas.waliKelas', 'cabang'])
            ->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Pengumuman Aktif Hari Ini & Mendatang
        $pengumuman = Pengumuman::aktif()
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->orderBy('tanggal_pengumuman', 'asc')
            ->limit(5)
            ->get();

        // Flyer Pop-up untuk Siswa
        $flyers = Flyer::getPopupFlyers('siswa', 3);

        // Grafik Performa Siswa
        $performa = $this->getPerformaSiswa($siswa->id);

        // Rekap Absensi Bulan Ini
        $absensi = $this->getRekapAbsensi($siswa->id);

        // Determine Semester (Logic Sederhana berdasarkan bulan)
        $bulan = now()->month;
        $semester = ($bulan >= 7 && $bulan <= 12) ? 'Ganjil' : 'Genap';

        return view('siswa.sia.dashboard', compact(
            'siswa',
            'pengumuman',
            'flyers',
            'performa',
            'absensi',
            'semester'
        ));
    }

    /**
     * Dashboard LMS - Learning Management System
     */
    public function dashboardLMS()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran'])
            ->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Check if Alumni (Lulus)
        if ($siswa->status === 'lulus') {
            return $this->dashboardAlumni($siswa);
        }

        // Cek akses LMS ditangani oleh middleware 'lms.access'

        // Kalender Akademik Bulan Ini
        $kalenderBulanIni = KalenderAkademik::aktif()
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id)
            ->where('is_hidden_siswa', false)
            ->whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Pengumuman
        $pengumuman = Pengumuman::aktif()
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->limit(3)
            ->get();

        // Jadwal Pelajaran Minggu Ini
        $jadwalMingguIni = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->with(['mataPelajaran', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // Daftar Guru Pengajar
        $guruPengajar = TenagaPendidik::whereHas('guruKelas', function ($q) use ($siswa) {
            $q->where('kelas_id', $siswa->kelas_id);
        })
            ->with(['guruKelas.mataPelajaran'])
            ->get();

        // Notifikasi Hari Ini (Materi, Tugas, Ujian Baru)
        $notifikasiHariIni = $this->getNotifikasiHariIni($siswa->kelas_id);

        return view('siswa.lms.dashboard', compact(
            'siswa',
            'kalenderBulanIni',
            'pengumuman',
            'jadwalMingguIni',
            'guruPengajar',
            'notifikasiHariIni'
        ));
    }

    /**
     * Lihat Kalender Akademik Setahun
     */
    public function kalenderTahunan(Request $request)
    {
        \Carbon\Carbon::setLocale('id');
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas.tahunAjaran')->first();

        if (!$siswa || !$siswa->kelas) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data kelas tidak ditemukan');
        }

        $tahunAjaran = $siswa->kelas->tahunAjaran;
        $tahunAjaranId = $siswa->kelas->tahun_ajaran_id;

        // View Mode: 'bulan' (default), 'minggu', 'tahun'
        $viewMode = $request->get('mode', 'bulan');

        // Parameter Tanggal/Bulan/Tahun
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Base Date untuk navigasi Mingguan (jika mode minggu)
        $dateParam = $request->get('date');
        $baseDate = $dateParam ? Carbon::parse($dateParam) : Carbon::createFromDate($year, $month, 1);

        // Jika mode minggu, override month/year dari baseDate agar konsisten
        if ($viewMode === 'minggu') {
            $month = $baseDate->month;
            $year = $baseDate->year;
        }

        // Query Utama
        $queryEvents = KalenderAkademik::aktif()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->where('is_hidden_siswa', false);

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

        if ($viewMode === 'minggu') {
            $eventsMinggu = (clone $queryEvents)->where(function ($q) use ($startOfWeek, $endOfWeek) {
                // Event mulai di minggu ini OR Event selesai di minggu ini OR Event melibas minggu ini
                $q->whereBetween('tanggal_mulai', [$startOfWeek, $endOfWeek])
                    ->orWhereBetween('tanggal_selesai', [$startOfWeek, $endOfWeek])
                    ->orWhere(function ($sq) use ($startOfWeek, $endOfWeek) {
                        $sq->where('tanggal_mulai', '<=', $startOfWeek)
                            ->where('tanggal_selesai', '>=', $endOfWeek);
                    });
            })->orderBy('tanggal_mulai')->get();

            // Generate header days for the week grid (Min, Sen, ... Sab)
            $weekDays = [];
            $tmpDate = $startOfWeek->copy();
            while ($tmpDate->lte($endOfWeek)) {
                $weekDays[] = $tmpDate->copy();
                $tmpDate->addDay();
            }
        } else {
            // Default empty if not in week mode to avoid undefined variable
            $weekDays = [];
        }

        // --- DATA LOGIC: BULANAN (Default & Sidebar) ---
        // Kita tetap butuh $events bulanan untuk Sidebar/Keterangan walaupun mode 'minggu'/'tahun'
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $events = (clone $queryEvents)->where(function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('tanggal_mulai', [$startOfMonth, $endOfMonth])
                ->orWhereBetween('tanggal_selesai', [$startOfMonth, $endOfMonth])
                ->orWhere(function ($q2) use ($startOfMonth, $endOfMonth) {
                    $q2->where('tanggal_mulai', '<=', $startOfMonth)
                        ->where('tanggal_selesai', '>=', $endOfMonth);
                });
        })
            ->orderBy('tanggal_mulai')
            ->get();

        // --- GRID CALENDAR GENERATION (Hanya jika mode bulan) ---
        $calendarDays = [];
        // Kita generate structure kalender jika mode 'bulan' atau default
        if ($viewMode === 'bulan') {
            $firstDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Minggu) - 6 (Sabtu)

            // Add empty cells for days before the 1st
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

            // Add days of the month
            for ($day = 1; $day <= $endOfMonth->day; $day++) {
                $currentDate = Carbon::createFromDate($year, $month, $day);

                // Filter events for this day
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

            // Fill remaining cells to complete the last week
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
        // Navigation for Month Mode
        $prevMonth = [
            'month' => $startOfMonth->copy()->subMonth()->month,
            'year' => $startOfMonth->copy()->subMonth()->year,
        ];
        $nextMonth = [
            'month' => $startOfMonth->copy()->addMonth()->month,
            'year' => $startOfMonth->copy()->addMonth()->year,
        ];

        // Navigation for Week Mode
        $prevWeekDate = $startOfWeek->copy()->subWeek()->format('Y-m-d');
        $nextWeekDate = $startOfWeek->copy()->addWeek()->format('Y-m-d');

        // Navigation for Year Mode
        $prevYear = $year - 1;
        $nextYear = $year + 1;

        if ($request->ajax()) {
            // Return only the calendar-section and legend for AJAX requests
            // User JavaScript will replace these parts in the DOM
            return response()->json([
                'html' => view('siswa.lms.kalender.index', compact(
                    'siswa',
                    'tahunAjaran',
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
                ))->render()
            ]);
        }

        return view('siswa.lms.kalender.index', compact(
            'siswa',
            'tahunAjaran',
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

    /**
     * Lihat Detail Hari Tertentu di Kalender
     */
    /**
     * Lihat Detail Hari Tertentu di Kalender
     */
    public function kalenderDetail($tanggal)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        // Parse tanggal to Carbon
        $date = Carbon::parse($tanggal);

        // Kegiatan di tanggal tersebut
        $events = KalenderAkademik::aktif()
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id)
            ->where('is_hidden_siswa', false)
            ->whereDate('tanggal_mulai', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereDate('tanggal_selesai', '>=', $date)
                    ->orWhereNull('tanggal_selesai');
            })
            ->get();

        // Jadwal Pelajaran di hari tersebut
        $hariIndo = $date->locale('id')->dayName;
        $hariIndo = ucfirst($hariIndo);

        $jadwalPelajaran = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->where('hari', $hariIndo)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // Navigation dates
        $prevDate = $date->copy()->subDay();
        $nextDate = $date->copy()->addDay();

        // Check if weekday (Senin-Jumat)
        $isWeekday = $date->isWeekday();

        return view('siswa.lms.kalender.detail', compact(
            'siswa',
            'tanggal', // Keep string or overwrite with object? View expects object.
            'events',
            'jadwalPelajaran',
            'prevDate',
            'nextDate',
            'isWeekday'
        ))
            ->with('tanggal', $date); // Overwrite with Carbon object
    }

    /**
     * Helper: Hitung Performa Siswa (untuk Dashboard SIA)
     */
    private function getPerformaSiswa($siswaId)
    {
        // Total Tugas
        $totalTugas = Tugas::whereHas('kelas', function ($q) use ($siswaId) {
            $q->whereHas('siswa', function ($q2) use ($siswaId) {
                $q2->where('siswa.id', $siswaId);
            });
        })
            ->count();

        // Tugas Selesai
        $tugasSelesai = Tugas::whereHas('tugasSiswa', function ($q) use ($siswaId) {
            $q->where('siswa_id', $siswaId)
                ->whereIn('status', ['dikerjakan', 'dinilai']);
        })
            ->count();

        // Total Ujian
        $totalUjian = Ujian::whereHas('kelas.siswa', function ($q) use ($siswaId) {
            $q->where('siswa.id', $siswaId);
        })
            ->count();

        // Ujian Selesai
        $ujianSelesai = Ujian::whereHas('ujianSiswa', function ($q) use ($siswaId) {
            $q->where('siswa_id', $siswaId)
                ->whereIn('status', ['selesai', 'dinilai']);
        })
            ->count();

        // Total Materi
        $totalMateri = Materi::whereHas('kelas.siswa', function ($q) use ($siswaId) {
            $q->where('siswa.id', $siswaId);
        })
            ->count();

        // Materi Dipelajari (anggap semua materi bisa diakses)
        $materiDipelajari = $totalMateri; // Placeholder, bisa ditrack lebih detail

        return [
            'tugas' => [
                'total' => $totalTugas,
                'selesai' => $tugasSelesai,
                'persentase' => $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 1) : 0
            ],
            'ujian' => [
                'total' => $totalUjian,
                'selesai' => $ujianSelesai,
                'persentase' => $totalUjian > 0 ? round(($ujianSelesai / $totalUjian) * 100, 1) : 0
            ],
            'materi' => [
                'total' => $totalMateri,
                'dipelajari' => $materiDipelajari,
                'persentase' => $totalMateri > 0 ? round(($materiDipelajari / $totalMateri) * 100, 1) : 100
            ]
        ];
    }

    /**
     * Helper: Rekap Absensi Bulan Ini
     */
    private function getRekapAbsensi($siswaId)
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $sakit = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'sakit')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        $izin = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'izin')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        $alpha = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'alpha')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        return compact('sakit', 'izin', 'alpha');
    }

    /**
     * Helper: Notifikasi Hari Ini (Materi, Tugas, Ujian Baru)
     */
    private function getNotifikasiHariIni($kelasId)
    {
        $today = now()->toDateString();

        // Materi Baru Hari Ini
        $materiBaru = Materi::where('kelas_id', $kelasId)
            ->whereDate('tanggal_upload', $today)
            ->with('mataPelajaran')
            ->get();

        // Tugas Baru Hari Ini
        $tugasBaru = Tugas::where('kelas_id', $kelasId)
            ->whereDate('tanggal_mulai', $today)
            ->with('mataPelajaran')
            ->get();

        // Ujian Hari Ini
        $ujianHariIni = Ujian::where('kelas_id', $kelasId)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->with('mataPelajaran')
            ->get();

        return [
            'materi' => $materiBaru,
            'tugas' => $tugasBaru,
            'ujian' => $ujianHariIni
        ];
    }

    /**
     * Dashboard khusus Alumni (Status Lulus)
     */
    private function dashboardAlumni($siswa)
    {
        // Ambil riwayat rapor terakhir
        $raporTerakhir = \App\Models\Rapor::where('siswa_id', $siswa->id)
            ->with('tahunAjaran')
            ->orderBy('semester', 'desc')
            ->first();
        
        return view('siswa.alumni.dashboard', compact('siswa', 'raporTerakhir'));
    }
}
