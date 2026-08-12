<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\KalenderAkademik;
use App\Models\Pengumuman;
use App\Models\JadwalPelajaran;
use App\Models\TenagaPendidik;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\Presensi;
use App\Models\PengaturanIstirahat;
use Carbon\Carbon;

class LmsDashboardController extends Controller
{
    /**
     * Dashboard LMS - Beranda
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran'])
            ->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek akses LMS ditangani oleh middleware 'lms.access'

        // Statistik Kehadiran
        $totalHariAktif = Presensi::where('siswa_id', $siswa->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $hadir = Presensi::where('siswa_id', $siswa->id)
            ->where('status', 'hadir')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $persenKehadiran = $totalHariAktif > 0 ? round(($hadir / $totalHariAktif) * 100) : 0;

        // Tugas Pending
        $tugasPending = Tugas::where('kelas_id', $siswa->kelas_id)
            ->where('tanggal_deadline', '>=', now())
            ->whereDoesntHave('tugasSiswa', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id)
                    ->whereIn('status', ['dikerjakan', 'dinilai']);
            })
            ->count();

        // Agenda Bulan Ini
        $agendaBulanIni = KalenderAkademik::where('status', 'aktif')
            ->where('is_hidden_siswa', false) // Filter hidden
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id ?? null)
            ->whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->count();

        // Jadwal Hari Ini
        $hariIni = Carbon::now()->locale('id')->dayName;
        $hariIni = ucfirst($hariIni); // Senin, Selasa, etc.

        $jadwalHariIni = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->where('hari', $hariIni)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get()
            ->filter(fn($j) => $siswa->canAccessMapel($j->mataPelajaran))
            ->values();

        // Pengumuman Terbaru (Ambil 5)
        $pengumumanList = Pengumuman::where('status', 'aktif')
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->orderBy('tanggal_pengumuman', 'desc')
            ->take(5)
            ->get();

        // ============ DATA TAMBAHAN UNTUK ENHANCED DASHBOARD ============

        // Kalender Mini - Kegiatan minggu ini
        $kalenderMingguIni = KalenderAkademik::where('status', 'aktif')
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id ?? null)
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [now()->startOfWeek(), now()->endOfWeek()])
                    ->orWhere(function ($q2) {
                        $q2->where('tanggal_mulai', '<=', now()->endOfWeek())
                            ->where('tanggal_selesai', '>=', now()->startOfWeek());
                    });
            })
            ->where('is_hidden_siswa', false) // Filter hidden
            ->orderBy('tanggal_mulai')
            ->take(5)
            ->get();

        // Notifikasi Hari Ini
        $notifikasiHariIni = \App\Models\Notification::where('user_id', $user->id)
            ->today()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Daftar Mata Pelajaran untuk Kelas Ini
        $mataPelajaranList = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->with(['mataPelajaran', 'guru'])
            ->get()
            ->filter(fn($j) => $siswa->canAccessMapel($j->mataPelajaran))
            ->unique('mata_pelajaran_id')
            ->take(8);

        // Daftar Guru Pengajar
        $guruPengajar = TenagaPendidik::whereHas('guruKelas', function ($q) use ($siswa) {
            $q->where('kelas_id', $siswa->kelas_id);
        })
            ->with([
                'guruKelas' => function ($q) use ($siswa) {
                    $q->where('kelas_id', $siswa->kelas_id)->with('mataPelajaran');
                }
            ])
            ->take(6)
            ->get();

        // Ujian Mendatang
        $ujianMendatang = Ujian::where('kelas_id', $siswa->kelas_id)
            ->where('tanggal_mulai', '>', now())
            ->where('tanggal_mulai', '<=', now()->addDays(7))
            ->orderBy('tanggal_mulai')
            ->take(3)
            ->get();

        // Tugas Deadline Terdekat
        $tugasDeadline = Tugas::where('kelas_id', $siswa->kelas_id)
            ->where('tanggal_deadline', '>=', now())
            ->where('tanggal_deadline', '<=', now()->addDays(7))
            ->whereDoesntHave('tugasSiswa', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id)
                    ->whereIn('status', ['dikerjakan', 'dinilai']);
            })
            ->with('mataPelajaran')
            ->orderBy('tanggal_deadline')
            ->take(5)
            ->take(5)
            ->get()
            ->filter(fn($t) => $siswa->canAccessMapel($t->mataPelajaran))
            ->values();

        return view('siswa.lms.dashboard', compact(
            'siswa',
            'persenKehadiran',
            'tugasPending',
            'agendaBulanIni',
            'jadwalHariIni',
            'pengumumanList',
            'kalenderMingguIni',
            'notifikasiHariIni',
            'mataPelajaranList',
            'guruPengajar',
            'ujianMendatang',
            'tugasDeadline'
        ));
    }

    /**
     * Daftar Pengumuman
     */
    public function pengumumanIndex()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }
        
        $pengumumanList = Pengumuman::where('status', 'aktif')
            ->where('tanggal_pengumuman', '<=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('siswa.lms.pengumuman.index', compact('siswa', 'pengumumanList'));
    }

    /**
     * Detail Pengumuman
     */
    public function pengumumanDetail($id)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $pengumuman = Pengumuman::findOrFail($id);

        return view('siswa.lms.pengumuman.show', compact('siswa', 'pengumuman'));
    }

    /**
     * Kalender Akademik
     */
    public function kalender(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas.tahunAjaran')->first();

        if (!$siswa || !$siswa->kelas) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data kelas tidak ditemukan');
        }

        $tahunAjaran = $siswa->kelas->tahunAjaran;
        $tahunAjaranId = $siswa->kelas->tahun_ajaran_id;

        // Get month and year from request or use current
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Build calendar dates
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Get first day of week (0 = Sunday)
        $firstDayOfWeek = $startOfMonth->dayOfWeek;

        // Get events for this month
        $events = KalenderAkademik::where('status', 'aktif')
            ->where('is_hidden_siswa', false) // Filter hidden
            ->where('tahun_ajaran_id', $tahunAjaranId)
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

        // Build calendar days array
        $calendarDays = [];

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

        // Navigation helpers
        $prevMonth = [
            'month' => $startOfMonth->copy()->subMonth()->month,
            'year' => $startOfMonth->copy()->subMonth()->year,
        ];
        $nextMonth = [
            'month' => $startOfMonth->copy()->addMonth()->month,
            'year' => $startOfMonth->copy()->addMonth()->year,
        ];

        return view('siswa.lms.kalender.index', compact(
            'siswa',
            'tahunAjaran',
            'year',
            'month',
            'calendarDays',
            'prevMonth',
            'nextMonth'
        ));
    }

    /**
     * Detail Kalender per Tanggal
     */
    public function kalenderDetail($tanggal)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        // Kegiatan di tanggal tersebut
        $kegiatan = KalenderAkademik::where('status', 'aktif')
            ->where('is_hidden_siswa', false) // Filter hidden
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
                $q->whereDate('tanggal_selesai', '>=', $tanggal)
                    ->orWhereNull('tanggal_selesai');
            })
            ->get();

        // Jadwal Pelajaran di hari tersebut
        $hariIndo = Carbon::parse($tanggal)->locale('id')->dayName;
        $hariIndo = ucfirst($hariIndo);

        $jadwalHariIni = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->where('hari', $hariIndo)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        return view('siswa.lms.kalender.detail', compact(
            'siswa',
            'tanggal',
            'kegiatan',
            'jadwalHariIni'
        ));
    }

    /**
     * Jadwal Pelajaran Lengkap
     */
    public function jadwal()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with(['kelas.tahunAjaran', 'kelas.waliKelas'])->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $kelas = $siswa->kelas;
        
        // Get all jadwal for this class
        $allJadwal = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // Get break times for this jenjang
        $istirahatList = PengaturanIstirahat::jenjang($kelas->jenjang)
            ->aktif()
            ->orderBy('jam_mulai')
            ->get();

        // Build Grid using the helper
        $scheduleGrid = $this->buildScheduleGrid($allJadwal, $istirahatList);

        // Today's schedule
        $hariIni = Carbon::now()->locale('id')->dayName;
        $hariIni = ucfirst($hariIni);
        $jadwalHariIni = $allJadwal->where('hari', $hariIni)->sortBy('jam_mulai')->values();

        // Complete subject list (unique)
        $mataPelajaranList = $allJadwal->unique('mata_pelajaran_id')
            ->map(function ($j) {
                return $j->mataPelajaran;
            })
            ->filter()
            ->sortBy('nama_mapel')
            ->values();

        return view('siswa.lms.jadwal', compact(
            'siswa',
            'kelas',
            'scheduleGrid',
            'hariIni',
            'jadwalHariIni',
            'mataPelajaranList'
        ));
    }

    /**
     * Build grid structure for schedule
     */
    private function buildScheduleGrid($jadwalList, $istirahatList)
    {
        // 1. Collect all unique Start Times to define Grid Rows
        $startTimes = collect();
        
        foreach ($jadwalList as $jadwal) {
            $startTimes->push($jadwal->jam_mulai->format('H:i'));
        }
        
        foreach ($istirahatList as $ist) {
            $startTimes->push(substr($ist->jam_mulai, 0, 5));
        }

        $gridRows = $startTimes->unique()->sort()->values(); 

        // 2. Build the Grid
        $grid = [];
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']; 
        
        // Check if we have Sabtu data
        if ($jadwalList->where('hari', 'Sabtu')->count() > 0) {
            $hariList[] = 'Sabtu';
        }

        // Initialize Grid
        foreach ($gridRows as $index => $time) {
            $grid[$index] = [
                'time_start' => $time,
                'days' => []
            ];
            foreach ($hariList as $hari) {
                $grid[$index]['days'][$hari] = ['type' => 'empty'];
            }
        }

        // Helper to find grid index for a given time
        $getGridIndex = function($time) use ($gridRows) {
            return $gridRows->search($time);
        };

        // 3. Place Items into Grid
        
        // A. Place Lessons
        foreach ($jadwalList as $jadwal) {
            $startTime = $jadwal->jam_mulai->format('H:i');
            $endTime = $jadwal->jam_selesai->format('H:i');
            $day = $jadwal->hari;
            
            if (!in_array($day, $hariList)) continue;

            $startIndex = $getGridIndex($startTime);
            if ($startIndex === false) continue; 

            // Calculate Rowspan
            $span = 0;
            for ($i = $startIndex; $i < count($gridRows); $i++) {
                if ($gridRows[$i] < $endTime) {
                    $span++;
                } else {
                    break;
                }
            }
            if ($span < 1) $span = 1;

            // Mark cells
            if (isset($grid[$startIndex]['days'][$day]['type']) && $grid[$startIndex]['days'][$day]['type'] == 'taken') {
                $existing = $grid[$startIndex]['days'][$day];
                 if (isset($existing['type']) && $existing['type'] == 'lesson') {
                     // Append content
                     $grid[$startIndex]['days'][$day]['data'][] = $jadwal;
                 } else {
                     // Create new if conflict (should be handled better but for now overwrite if taken by span?)
                     // Actually logic from Admin controller handles this: if taken, try to append?
                     // If it is 'taken' it means it is covered by a previous rowspan. 
                     // Ideally we shouldn't be here if schedule is non-overlapping.
                     // But if overlapping, we might lose display. 
                     // Let's assume Valid Data for now, or just force create.
                     if ($existing['type'] == 'taken') {
                        // Conflict with previous span.
                        // Can't easily merge into previous span.
                        // Ideally we should start a new item here? But grid structure is fixed.
                        // For simplicity, we overwrite 'taken' with 'lesson' (renders on top) or we just ignore?
                        // Admin logic handles "if ($grid[$startIndex]['days'][$day]['type'] == 'taken')".
                        // Wait, looking at Admin logic:
                        /*
                        if (isset($grid[$startIndex]['days'][$day]['type']) && $grid[$startIndex]['days'][$day]['type'] == 'taken') {
                            $existing = $grid[$startIndex]['days'][$day];
                             if ($existing['type'] == 'lesson') { ... } 
                             else { 
                                // It was 'taken'. Admin logic creates new lesson here effectively overwriting the 'taken' status for this cell.
                                // This means the previous rowspan might visually clash? 
                                // HTML table handles overlapping rowspan poorly (pushes cells).
                                // But let's stick to Admin logic.
                                 $grid[$startIndex]['days'][$day] = [ 'type' => 'lesson', ... ];
                             }
                        }
                        */
                         $grid[$startIndex]['days'][$day] = [
                             'type' => 'lesson',
                             'rowspan' => $span,
                             'data' => [$jadwal]
                         ];
                     }
                 }
            } else if ($grid[$startIndex]['days'][$day]['type'] == 'empty') {
                 $grid[$startIndex]['days'][$day] = [
                     'type' => 'lesson',
                     'rowspan' => $span,
                     'data' => [$jadwal]
                 ];
                 // Mark covered
                 for ($r = 1; $r < $span; $r++) {
                     if (isset($grid[$startIndex + $r])) {
                        $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                     }
                 }
            }
        }

        // B. Place Breaks
        foreach ($istirahatList as $ist) {
            $startTime = substr($ist->jam_mulai, 0, 5);
            $endTime = substr($ist->jam_selesai, 0, 5);
            $targetDays = is_array($ist->hari_aktif) ? $ist->hari_aktif : json_decode($ist->hari_aktif, true);
            
            if (!$targetDays) $targetDays = $hariList;

            $startIndex = $getGridIndex($startTime);
            if ($startIndex === false) continue;

             $span = 0;
            for ($i = $startIndex; $i < count($gridRows); $i++) {
                if ($gridRows[$i] < $endTime) {
                    $span++;
                } else {
                    break;
                }
            }
            if ($span < 1) $span = 1;

            foreach ($targetDays as $day) {
                if (!in_array($day, $hariList)) continue;
                
                $grid[$startIndex]['days'][$day] = [
                    'type' => 'break',
                    'rowspan' => $span,
                    'data' => $ist
                ];

                 // Mark covered
                 for ($r = 1; $r < $span; $r++) {
                     if (isset($grid[$startIndex + $r])) {
                        $grid[$startIndex + $r]['days'][$day] = ['type' => 'taken'];
                     }
                 }
            }
        }
        
        return [
            'rows' => $grid,
            'days' => $hariList
        ];
    }
    

    /**
     * Print Jadwal Pelajaran
     */


    public function printJadwal()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with(['kelas.tahunAjaran', 'kelas.waliKelas'])->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $kelas = $siswa->kelas;
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            // Get jadwal pelajaran
            $jadwalPelajaran = JadwalPelajaran::whereHas('kelas', function($q) use ($kelas) {
                    $q->where('kelas.id', $kelas->id);
                })
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();

            // Get waktu istirahat untuk jenjang dan hari ini
            $istirahatList = PengaturanIstirahat::jenjang($kelas->jenjang)
                ->aktif()
                ->untukHari($hari)
                ->orderBy('jam_mulai')
                ->get();

            // Merge jadwal dan istirahat, kemudian sort by jam_mulai
            $merged = collect();

            // Add jadwal pelajaran
            foreach ($jadwalPelajaran as $jadwal) {
                $merged->push([
                    'type' => 'jadwal',
                    'data' => $jadwal,
                    'jam_mulai' => $jadwal->jam_mulai->format('H:i:s'),
                ]);
            }

            // Add istirahat
            foreach ($istirahatList as $istirahat) {
                $merged->push([
                    'type' => 'istirahat',
                    'data' => $istirahat,
                    'jam_mulai' => Carbon::parse($istirahat->jam_mulai)->format('H:i:s'),
                ]);
            }

            // Sort by jam_mulai
            $jadwalPerHari[$hari] = $merged->sortBy('jam_mulai')->values();
        }

        return view('siswa.lms.jadwal-print', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }



    /**
     * Daftar Guru Pengajar
     */
    public function guru()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $guruPengajar = TenagaPendidik::whereHas('guruKelas', function ($q) use ($siswa) {
            $q->where('kelas_id', $siswa->kelas_id);
        })
            ->with([
                'guruKelas' => function ($q) use ($siswa) {
                    $q->where('kelas_id', $siswa->kelas_id)->with('mataPelajaran');
                }
            ])
            ->get();

        return view('siswa.lms.guru', compact('siswa', 'guruPengajar'));
    }
}