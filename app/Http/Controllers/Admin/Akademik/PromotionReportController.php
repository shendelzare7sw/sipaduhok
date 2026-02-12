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
        
        // Get Statistics
        $stats = DB::table('status_naik_kelas_siswa')
            ->where('tahun_ajaran_id', $selectedYear->id)
            ->select('status_kelulusan', DB::raw('count(*) as total'))
            ->groupBy('status_kelulusan')
            ->pluck('total', 'status_kelulusan');
            
        // Get Detailed Lists
        // Filter: status (NAIK, TIDAK, LULUS, TUNGGAKAN)
        // Filter Inputs
        $search = $request->get('search');
        $cabangId = $request->get('cabang_id');
        $kelasId = $request->get('kelas_id');
        $filterStatus = $request->get('status');

        // Lists for Dropdown
        $cabangs = \App\Models\Cabang::all();
        $kelasList = \App\Models\Kelas::where('tahun_ajaran_id', $selectedYear->id)->get();
        
        // --- 1. History Query ---
        $query = DB::table('status_naik_kelas_siswa')
            ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
            ->select(
                'status_naik_kelas_siswa.*',
                'siswa.nama_lengkap',
                'siswa.cabang_id',
                DB::raw("COALESCE(kelas.nama_kelas, status_naik_kelas_siswa.kelas_asal) as kelas_current")
            );

        if ($filterStatus) $query->where('status_kelulusan', $filterStatus);
        if ($search) $query->where('siswa.nama_lengkap', 'like', "%{$search}%");
        if ($cabangId) $query->where('siswa.cabang_id', $cabangId);
        if ($kelasId) $query->where('siswa.kelas_id', $kelasId);

        $students = $query->paginate(20);

        // --- 2. Simulation Query ---
        // FIX: Only show students who are currently in classes of the SELECTED YEAR.
        // If selecting 2026/2027 (Future), and students are in 2025/2026, list should be empty.
        $simQuery = Siswa::where('status', 'aktif')
            ->whereHas('kelas', function($q) use ($selectedYear) {
                $q->where('tahun_ajaran_id', $selectedYear->id);
            })
            ->with(['kelas', 'tagihan']);

        if ($search) $simQuery->where('nama_lengkap', 'like', "%{$search}%");
        if ($cabangId) $simQuery->where('cabang_id', $cabangId);
        if ($kelasId) $simQuery->where('kelas_id', $kelasId);

        $activeStudents = $simQuery->paginate(20, ['*'], 'sim_page');

        $simulationData = [];
        $promotionService = app(\App\Services\PromotionService::class);
        
        foreach ($activeStudents as $siswa) {
            $simulationData[] = [
                'siswa' => $siswa,
                'result' => $promotionService->checkEligibility($siswa, $selectedYear->id)
            ];
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
            'tahun' => $selectedYear, // Displayed Year
            'activeYear' => $activeYear, // Actual Active Year (for checks)
            'allTahunAjaran' => $allTahunAjaran, // Dropdown list
            'filterStatus' => $filterStatus,
            'cabangs' => $cabangs,
            'kelasList' => $kelasList,
            'search' => $search,
            'cabangId' => $cabangId,
            'kelasId' => $kelasId,
            'promotionReadiness' => $promotionReadiness,
            'schedules' => $schedules,
        ]);
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
            
            $route = str_contains($request->route()->getName(), 'waka') ? 'waka.promotion.report' : 'admin.akademik.promotion.report';
            
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

    /**
     * Promote selected students individually (for those who initially failed).
     */
    public function promoteSelected(Request $request)
    {
        $siswaIds = $request->input('siswa_ids', []);
        $tahunAjaranId = $request->input('tahun_ajaran_id'); // Get context year from form
        
        if (empty($siswaIds)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih untuk dinaikkan.');
        }

        // Use provided year or fallback to active (though form should always provide it)
        $contextYearId = $tahunAjaranId ?? TahunAjaran::where('is_active', true)->value('id');
        
        $promotionService = app(\App\Services\PromotionService::class);
        
        $result = $promotionService->promoteSelectedStudents($siswaIds, $contextYearId);
        
        if ($result['success'] > 0) {
            $message = "Berhasil menaikkan {$result['success']} siswa.";
            if ($result['failed'] > 0) {
                $message .= " Gagal: {$result['failed']} siswa.";
            }
            return back()->with('success', $message);
        }
        
        return back()->with('error', 'Tidak ada siswa yang berhasil dinaikkan. ' . implode(', ', $result['errors']));
    }
}

