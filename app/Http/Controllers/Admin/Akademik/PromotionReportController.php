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
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $selectedYear->id)
            ->select(
                'status_naik_kelas_siswa.*',
                'siswa.nama_lengkap',
                'siswa.cabang_id', // Select for filter
                'kelas.nama_kelas as kelas_current' 
            );

        if ($filterStatus) $query->where('status_kelulusan', $filterStatus);
        if ($search) $query->where('siswa.nama_lengkap', 'like', "%{$search}%");
        if ($cabangId) $query->where('siswa.cabang_id', $cabangId);
        if ($kelasId) $query->where('siswa.kelas_id', $kelasId);

        $students = $query->paginate(20);

        // --- 2. Simulation Query ---
        $simQuery = Siswa::where('status', 'aktif')
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
                'result' => $promotionService->checkEligibility($siswa, $activeYear->id)
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
        // ... (keep existing logic if manual execution is still desired, 
        // OR redirect to schedule if we want to enforce scheduling)
        // For now, keep manual as is.
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        $promotionService = app(\App\Services\PromotionService::class);
        
        // ... (existing code)
        $students = Siswa::where('status', 'aktif')->get();
        $count = 0;
        
        DB::beginTransaction();
        try {
            foreach ($students as $siswa) {
                // Execute promotion logic
                $promotionService->executeStudentPromotion($siswa, $activeYear->id, now());
                $count++;
            }
            DB::commit();
            
            // Redirect based on role helper or loose determination
            $route = str_contains($request->route()->getName(), 'waka') ? 'waka.promotion.report' : 'admin.akademik.promotion.report';
            
            return redirect()->route($route)
                ->with('success', "Proses kenaikan kelas berhasil dijalankan untuk {$count} siswa.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Create a new scheduled promotion execution.
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'notify_email' => 'nullable|email',
        ]);
        
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        
        \App\Models\PromotionSchedule::create([
            'tahun_ajaran_id' => $activeYear->id,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'PENDING',
            'created_by' => auth()->id(),
            'notify_on_complete' => true,
            'notification_email' => $request->notify_email ?? auth()->user()->email,
        ]);
        
        return back()->with('success', 'Jadwal kenaikan kelas berhasil dibuat.');
    }

    /**
     * Cancel a pending schedule.
     */
    public function cancelSchedule($id)
    {
        $schedule = \App\Models\PromotionSchedule::findOrFail($id);
        
        if ($schedule->cancel()) {
            return back()->with('success', 'Jadwal berhasil dibatalkan.');
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
     * Promote selected students (for those who initially failed).
     */
    public function promoteSelected(Request $request)
    {
        $siswaIds = $request->input('siswa_ids', []);
        
        if (empty($siswaIds)) {
            return back()->with('error', 'Tidak ada siswa yang dipilih untuk dinaikkan.');
        }
        
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        $promotionService = app(\App\Services\PromotionService::class);
        
        $result = $promotionService->promoteSelectedStudents($siswaIds, $activeYear->id);
        
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

