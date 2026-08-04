<?php

namespace App\Http\Controllers\Admin\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;
use App\Models\Siswa;

class PromotionReportController extends Controller
{
    public function index(Request $request): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Year Selection for History
        $selectedYearId = $request->get('tahun_ajaran_id', $activeYear->id);
        $selectedYear = TahunAjaran::find($selectedYearId) ?? $activeYear;
        $allTahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        
        // Filter Inputs
        $search = $request->get('search');
        $kelasId = $request->get('kelas_id');
        $jenjangFilter = $request->get('jenjang');
        $filterStatus = $request->get('status');
        // Auto-filter by cabang for waka role
        $cabangId = auth()->user()->role === 'wakil_kepala_sekolah'
            ? auth()->user()->cabang_id
            : $request->get('cabang_id');

        // Get Statistics (filtered by cabang when waka)
        $stats = DB::table('status_naik_kelas_siswa')
            ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
            ->when($cabangId, fn($q) => $q->where('siswa.cabang_id', $cabangId))
            ->select('status_naik_kelas_siswa.status_kelulusan', DB::raw('count(*) as total'))
            ->groupBy('status_naik_kelas_siswa.status_kelulusan')
            ->pluck('total', 'status_kelulusan');

        // Get Detailed Lists
        // Filter: status (NAIK, TIDAK, LULUS, TUNGGAKAN)

        // Lists for Dropdown
        $cabangs = \App\Models\Cabang::all();
        $kelasList = \App\Models\Kelas::where('tahun_ajaran_id', $selectedYear->id)
            ->when($cabangId, fn($q) => $q->where('cabang_id', $cabangId))
            ->when($jenjangFilter, fn($q) => $q->where('jenjang', $jenjangFilter))
            ->orderBy('jenjang')->orderBy('nama_kelas')
            ->get();
        
        // --- 1. History Query ---
        $query = DB::table('status_naik_kelas_siswa')
            ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
            ->select(
                'status_naik_kelas_siswa.*',
                'siswa.nama_lengkap',
                'siswa.cabang_id',
                'siswa.status as siswa_status',
                DB::raw("COALESCE(kelas.nama_kelas, status_naik_kelas_siswa.kelas_asal) as kelas_current")
            );

        if ($filterStatus) $query->where('status_naik_kelas_siswa.status_kelulusan', $filterStatus);
        if ($search) $query->where('siswa.nama_lengkap', 'like', "%{$search}%");
        if ($cabangId) $query->where('siswa.cabang_id', $cabangId);
        if ($jenjangFilter) {
            $kelasIdsForJenjang = \App\Models\Kelas::where('jenjang', $jenjangFilter)->pluck('id');
            $query->whereIn('status_naik_kelas_siswa.kelas_asal', $kelasIdsForJenjang);
        }

        // FIX: Use historical kelas_asal instead of current kelas_id to include graduates
        if ($kelasId) {
            $query->where(function($q) use ($kelasId) {
                $q->where('status_naik_kelas_siswa.kelas_asal', $kelasId)
                  ->orWhere('siswa.kelas_id', $kelasId);
            });
        }

        $students = $query->orderBy('status_naik_kelas_siswa.tanggal_eksekusi', 'desc')
            ->paginate(20);

        // --- 2. Simulation Query ---
        // NEW: Support historical mode to show students as they were at execution time
        $simMode = $request->get('sim_mode', 'current');

        if ($simMode === 'historical') {
            // Historical mode: Show students as they were BEFORE execution
            $simQuery = DB::table('status_naik_kelas_siswa')
                ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
                ->leftJoin('kelas as kelas_asal', 'status_naik_kelas_siswa.kelas_asal', '=', 'kelas_asal.id')
                ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
                ->select(
                    'siswa.*',
                    'status_naik_kelas_siswa.status_kelulusan',
                    'status_naik_kelas_siswa.kelas_asal',
                    'status_naik_kelas_siswa.kelas_tujuan',
                    'kelas_asal.nama_kelas as kelas_nama'
                );

            if ($search) $simQuery->where('siswa.nama_lengkap', 'like', "%{$search}%");
            if ($cabangId) $simQuery->where('siswa.cabang_id', $cabangId);
            if ($jenjangFilter) $simQuery->where('kelas_asal.jenjang', $jenjangFilter);
            if ($kelasId) $simQuery->where('status_naik_kelas_siswa.kelas_asal', $kelasId);

            $activeStudents = $simQuery->paginate(20, ['*'], 'sim_page');

            // For historical mode, data already includes results
            $simulationData = $activeStudents->map(function($record) {
                // Determine eligibility based on status (already executed, so all were eligible)
                $isEligible = in_array($record->status_kelulusan, ['NAIK_KELAS', 'LULUS', 'NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN']);

                // Check if this student had dispensation
                $hadDispensasi = in_array($record->status_kelulusan, ['NAIK_KELAS_TUNGGAKAN', 'LULUS_TUNGGAKAN']);

                return [
                    'siswa' => (object)[
                        'id' => $record->id,
                        'nama_lengkap' => $record->nama_lengkap,
                        'nis' => $record->nis ?? '',
                        'kelas' => (object)['nama_kelas' => $record->kelas_nama ?? 'N/A']
                    ],
                    'result' => [
                        'eligible' => $isEligible,
                        'status' => $record->status_kelulusan,
                        'kelas_asal' => $record->kelas_asal,
                        'kelas_tujuan' => $record->kelas_tujuan,
                        'financial' => [
                            'status' => $hadDispensasi ? 'BELUM_LUNAS' : 'LUNAS',
                            'is_dispensasi' => $hadDispensasi,
                            'unpaid_amount' => 0  // Historical data - amount not stored
                        ],
                        'academic' => [
                            'is_tuntas' => $isEligible,
                            'percentage' => $isEligible ? 100 : 0,
                            'tuntas_count' => 0,  // Historical data - detail not stored
                            'total_mapel' => 0,   // Historical data - detail not stored
                            'threshold' => 70
                        ]
                    ]
                ];
            })->toArray();
        } else {
            // Current mode: Show students currently enrolled in this year
            // Kelas yang belum punya JadwalPelajaran sama sekali (mis. KB/TKA yang
            // baru dibuat, belum disetup) dikecualikan DARI TAMPILAN UTAMA -
            // siswanya TIDAK PERNAH bisa punya nilai (tidak ada guru yang punya
            // alasan/akses mengisi), jadi selalu nongol "RAWAN 0%" bukan karena
            // akademiknya kurang, tapi karena kelasnya memang belum siap dievaluasi.
            // Beda dengan kelas yang sudah lengkap tapi nilainya belum diisi guru -
            // itu tetap valid tampil RAWAN di tampilan utama.
            //
            // ?tanpa_jadwal=1 membalik kondisi ini: menampilkan KHUSUS siswa yang
            // tadi dikecualikan, supaya admin bisa sengaja pilih & "Naikkan Terpilih"
            // mereka lewat celah override manual di PromotionService (lihat
            // promoteSelectedStudents()) - dipisah dari tampilan utama supaya tidak
            // mengotori daftar RAWAN yang memang butuh perhatian guru/nilai asli.
            $showTanpaJadwal = $request->boolean('tanpa_jadwal');

            $simQuery = Siswa::where('status', 'aktif')
                ->whereHas('kelas', function ($q) use ($selectedYear, $showTanpaJadwal) {
                    $q->where('tahun_ajaran_id', $selectedYear->id);
                    if ($showTanpaJadwal) {
                        $q->whereDoesntHave('jadwalPelajaran');
                    } else {
                        $q->whereHas('jadwalPelajaran');
                    }
                })
                ->with(['kelas', 'tagihan']);

            if ($search) $simQuery->where('nama_lengkap', 'like', "%{$search}%");
            if ($cabangId) $simQuery->where('cabang_id', $cabangId);
            if ($jenjangFilter) $simQuery->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjangFilter));
            if ($kelasId) $simQuery->where('kelas_id', $kelasId);

            // Clone query before paginate (paginate modifies the builder with limit/offset)
            $allStudentsQuery = clone $simQuery;

            $activeStudents = $simQuery->paginate(20, ['*'], 'sim_page');

            $simulationData = [];
            $promotionService = app(\App\Services\PromotionService::class);

            foreach ($activeStudents as $siswa) {
                $check = $promotionService->checkEligibility($siswa, $selectedYear->id);
                $simulationData[] = [
                    'siswa' => $siswa,
                    'result' => $check
                ];
            }

            // Count total active students across ALL pages for "Select All" feature
            $totalActiveGlobal = $allStudentsQuery->count();
            $totalIneligibleGlobal = $totalActiveGlobal;

            // Info transparansi: berapa siswa aktif di TA ini yang dikecualikan dari
            // simulasi karena kelasnya belum ada jadwal sama sekali (dihitung terlepas
            // dari mode $showTanpaJadwal, supaya banner-nya tetap akurat di kedua mode).
            $siswaTanpaJadwalCount = Siswa::where('status', 'aktif')
                ->whereHas('kelas', function ($q) use ($selectedYear) {
                    $q->where('tahun_ajaran_id', $selectedYear->id)
                      ->whereDoesntHave('jadwalPelajaran');
                })
                ->count();
        }

        // --- 3. TA Validation for Promotion ---
        // Check if next TA exists (for students to be moved to)
        $nextTahunAjaran = TahunAjaran::where('is_active', false)
            ->where('tanggal_mulai', '>', $activeYear->tanggal_selesai)
            ->orderBy('tanggal_mulai', 'asc')
            ->first();
        
        $kelasBaruCount = $nextTahunAjaran 
            ? \App\Models\Kelas::where('tahun_ajaran_id', $nextTahunAjaran->id)->count() 
            : 0;
        
        // Check readiness
        $promotionReadiness = [
            'hasNextTA' => $nextTahunAjaran !== null,
            'nextTA' => $nextTahunAjaran,
            'kelasBaruCount' => $kelasBaruCount,
            'isReady' => $nextTahunAjaran !== null && $kelasBaruCount > 0,
        ];

        // --- 4. Get Schedules ---
        $schedules = \App\Models\PromotionSchedule::where('tahun_ajaran_id', $selectedYear->id)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.akademik.promotion.rekap', [
            'stats' => $stats,
            'students' => $students,
            'simulationData' => $simulationData, 
            'activeStudentsLinks' => $activeStudents,
            'totalActiveGlobal' => $totalActiveGlobal ?? 0,
            'totalIneligibleGlobal' => $totalIneligibleGlobal ?? 0,
            'siswaTanpaJadwalCount' => $siswaTanpaJadwalCount ?? 0,
            'showTanpaJadwal' => $showTanpaJadwal ?? false,
            'tahun' => $selectedYear, // Displayed Year
            'activeYear' => $activeYear, // Actual Active Year (for checks)
            'allTahunAjaran' => $allTahunAjaran, // Dropdown list
            'filterStatus' => $filterStatus,
            'cabangs' => $cabangs,
            'kelasList' => $kelasList,
            'search' => $search,
            'cabangId' => $cabangId,
            'kelasId' => $kelasId,
            'jenjangFilter' => $jenjangFilter,
            'promotionReadiness' => $promotionReadiness,
            'schedules' => $schedules,
            'simMode' => $simMode, // NEW: Simulation mode toggle
        ]);
    }

    public function print(Request $request): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        $selectedYearId = $request->get('tahun_ajaran_id', $activeYear->id);
        $selectedYear = TahunAjaran::find($selectedYearId) ?? $activeYear;

        $filterStatus = $request->get('status');
        // Auto-filter by cabang for waka role
        $cabangId = auth()->user()->role === 'wakil_kepala_sekolah'
            ? auth()->user()->cabang_id
            : $request->get('cabang_id');
        $kelasId = $request->get('kelas_id');

        $stats = DB::table('status_naik_kelas_siswa')
            ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
            ->when($cabangId, fn($q) => $q->where('siswa.cabang_id', $cabangId))
            ->select('status_naik_kelas_siswa.status_kelulusan', DB::raw('count(*) as total'))
            ->groupBy('status_naik_kelas_siswa.status_kelulusan')
            ->pluck('total', 'status_kelulusan');

        $query = DB::table('status_naik_kelas_siswa')
            ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->leftJoin('cabang', 'siswa.cabang_id', '=', 'cabang.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
            ->select(
                'status_naik_kelas_siswa.*',
                'siswa.nama_lengkap',
                'siswa.nis',
                'cabang.nama_cabang',
                DB::raw("COALESCE(kelas.nama_kelas, status_naik_kelas_siswa.kelas_asal) as kelas_current")
            );

        if ($filterStatus) $query->where('status_naik_kelas_siswa.status_kelulusan', $filterStatus);
        if ($cabangId) $query->where('siswa.cabang_id', $cabangId);
        if ($kelasId) {
            $query->where(function($q) use ($kelasId) {
                $q->where('status_naik_kelas_siswa.kelas_asal', $kelasId)
                  ->orWhere('siswa.kelas_id', $kelasId);
            });
        }

        $students = $query->orderBy('status_naik_kelas_siswa.kelas_asal')
            ->orderBy('siswa.nama_lengkap')
            ->get();

        $cabang = $cabangId ? \App\Models\Cabang::find($cabangId) : null;

        return view('admin.akademik.promotion.print', compact(
            'stats', 'students', 'selectedYear', 'filterStatus', 'cabang'
        ));
    }

    public function execute(Request $request)
    {
        // Require context year to be passed
        $tahunAjaranId = $request->input('tahun_ajaran_id');
        
        // If not provided, fallback to active but strictly warns/logs?
        // Better: strict fallback or fail.
        $contextYear = $tahunAjaranId 
            ? TahunAjaran::find($tahunAjaranId) 
            : TahunAjaran::where('is_active', true)->firstOrFail();
            
        $promotionService = app(\App\Services\PromotionService::class);
        
        // Scope students to those enrolled in the CONTEXT YEAR
        // Logic: Get students who have a class belonging to this year?
        // OR: Just iterate all 'aktif' students, and checkEligibility logic handles the rest?
        // checkEligibility(siswa, $contextYear->id) checks grades in that year.
        // executeStudentPromotion(siswa, $contextYear->id) moves them to Next Year relative to Context.
        
        // Issue: Siswa::where('status', 'aktif')->get() gets EVERYONE.
        // If we run this for 2024/2025 context, but student is already in 2025/2026 class?
        // executeStudentPromotion will move them to 2026/2027 class?
        // We need to filter students who are in classes OF THE CONTEXT YEAR.
        
        $students = Siswa::whereHas('kelas', function($q) use ($contextYear) {
                $q->where('tahun_ajaran_id', $contextYear->id);
            })
            ->where('status', 'aktif')
            ->get();
            
        // Safety check: if 0 students, maybe they are unassigned?
        if ($students->isEmpty()) {
             // Fallback: check historical Data? No, Simulation is for current active state.
             // If manual execute is run, it implies we want to process students CURRENTLY in that year.
        }

        $count = 0;
        
        DB::beginTransaction();
        try {
            foreach ($students as $siswa) {
                // Execute promotion logic
                // This checks grades in $contextYear->id
                // And moves them to Next Year (relative to $contextYear)
                $promotionService->executeStudentPromotion($siswa, $contextYear->id, now());
                $count++;
            }
            DB::commit();
            
            $route = str_contains($request->route()->getName(), 'waka') ? 'waka.kenaikan-kelas.report' : 'admin.akademik.kenaikan-kelas.report';
            
            return redirect()->route($route, ['tahun_ajaran_id' => $contextYear->id]) // Redirect back to same context
                ->with('success', "Proses kenaikan kelas berhasil dijalankan untuk {$count} siswa (Tahun: {$contextYear->nama_tahun_ajaran}).");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a pending schedule.
     */
    /**
     * Cancel a pending schedule.
     */
    public function cancelSchedule($id)
    {
        $schedule = \App\Models\PromotionSchedule::findOrFail($id);
        
        // Wrap in transaction to ensure consistent state
        DB::transaction(function () use ($schedule) {
            if ($schedule->cancel()) {
                // Also clear the setting to reflect that no schedule is active
                DB::table('pengaturan_naik_kelas')
                    ->where('tahun_ajaran_id', $schedule->tahun_ajaran_id)
                    ->update(['tanggal_eksekusi' => null]);
            }
        });
        
        // Reload to check status
        $schedule->refresh();

        if ($schedule->status === 'CANCELLED') {
            return back()->with('success', 'Jadwal berhasil dibatalkan dan pengaturan tanggal eksekusi dikosongkan.');
        }
        
        return back()->with('error', 'Gagal membatalkan jadwal. Status saat ini: ' . $schedule->status);
    }
    
    // ... (Keep existing rollback and promoteSelected methods)
    
    /**
     * Rollback a single student promotion.
     */
    public function rollback(Request $request, $statusId)
    {
        $promotionService = app(\App\Services\PromotionService::class);
        $result = $promotionService->rollbackStudent($statusId, auth()->id());
        
        if ($result['success']) {
            return back()->with('success', $result['message']);
        }
        return back()->with('error', $result['message']);
    }

    /**
     * Rollback multiple selected students.
     */
    public function rollbackSelected(Request $request)
    {
        $statusIds = $request->input('status_ids', []);
        
        if (empty($statusIds)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih untuk rollback.');
        }
        
        $promotionService = app(\App\Services\PromotionService::class);
        $successCount = 0;
        $errorMessages = [];
        
        foreach ($statusIds as $statusId) {
            $result = $promotionService->rollbackStudent($statusId, auth()->id());
            if ($result['success']) {
                $successCount++;
            } else {
                $errorMessages[] = $result['message'];
            }
        }
        
        if ($successCount > 0) {
            $message = "Berhasil rollback {$successCount} siswa.";
            if (!empty($errorMessages)) {
                $message .= " Gagal: " . count($errorMessages) . " siswa.";
            }
            return back()->with('success', $message);
        }
        
        return back()->with('error', 'Gagal rollback: ' . implode(', ', $errorMessages));
    }

    public function promoteSelected(Request $request)
    {
        $siswaIds = $request->input('siswa_ids', []);
        $tahunAjaranId = $request->input('tahun_ajaran_id'); // Get context year from form
        $selectAll = $request->input('select_all', false);
        
        // Use provided year or fallback to active (though form should always provide it)
        $contextYearId = $tahunAjaranId ?? TahunAjaran::where('is_active', true)->value('id');

        if ($selectAll) {
            $simQuery = Siswa::where('status', 'aktif')
                ->whereHas('kelas', function($q) use ($contextYearId) {
                    $q->where('tahun_ajaran_id', $contextYearId);
                });

            $cabangId = auth()->user()->role === 'wakil_kepala_sekolah' 
                ? auth()->user()->cabang_id 
                : $request->get('cabang_id');
            $jenjangFilter = $request->get('jenjang');
            $kelasId = $request->get('kelas_id');
            $search = $request->get('search');

            if ($search) $simQuery->where('nama_lengkap', 'like', "%{$search}%");
            if ($cabangId) $simQuery->where('cabang_id', $cabangId);
            if ($jenjangFilter) $simQuery->whereHas('kelas', fn($q) => $q->where('jenjang', $jenjangFilter));
            if ($kelasId) $simQuery->where('kelas_id', $kelasId);

            // Fetch all matching students to check eligibility and pluck IDs
            // We only want to select those who would normally have a checkbox (ineligible)
            // Or we could just pass all IDs and let promoteSelectedStudents handle the filtering.
            // Since promoteSelectedStudents checks eligibility and fails ineligible ones,
            // passing all IDs is safe, BUT the user only sees checkboxes for ineligible ones.
            // Wait, this is fine because promoteSelectedStudents will only promote the eligible ones anyway.
            $siswaIds = $simQuery->pluck('id')->toArray();
        }

        if (empty($siswaIds)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih untuk dinaikkan.');
        }
        
        $promotionService = app(\App\Services\PromotionService::class);
        
        $result = $promotionService->promoteSelectedStudents($siswaIds, $contextYearId);
        
        if ($result['success'] > 0) {
            $message = "Berhasil menaikkan {$result['success']} siswa.";
            if ($result['failed'] > 0) {
                // $message .= " Gagal: {$result['failed']} siswa.";
                // We don't need to show all fails if select_all is used, because many might naturally be ineligible
                $message .= " (Proses selesai)";
            }
            return back()->with('success', $message);
        }
        
        return back()->with('error', 'Tidak ada siswa yang berhasil dinaikkan. ' . implode(', ', array_slice($result['errors'], 0, 5)) . (count($result['errors']) > 5 ? ' dan ' . (count($result['errors']) - 5) . ' lainnya.' : ''));
    }
}

