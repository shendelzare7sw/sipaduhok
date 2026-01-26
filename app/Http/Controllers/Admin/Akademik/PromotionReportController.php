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
        
        // Get Statistics
        $stats = DB::table('status_naik_kelas_siswa')
            ->where('tahun_ajaran_id', $activeYear->id)
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
        $kelasList = \App\Models\Kelas::where('tahun_ajaran_id', $activeYear->id)->get();
        
        // --- 1. History Query ---
        $query = DB::table('status_naik_kelas_siswa')
            ->join('siswa', 'status_naik_kelas_siswa.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('status_naik_kelas_siswa.tahun_ajaran_id', $activeYear->id)
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

        return view('admin.akademik.promotion.rekap', [
            'stats' => $stats,
            'students' => $students,
            'simulationData' => $simulationData, 
            'activeStudentsLinks' => $activeStudents,
            'tahun' => $activeYear,
            'filterStatus' => $filterStatus,
            'cabangs' => $cabangs,      // Pass to view
            'kelasList' => $kelasList,  // Pass to view
            'search' => $search,        // Pass inputs back
            'cabangId' => $cabangId,
            'kelasId' => $kelasId
        ]);
    }
    public function execute(Request $request)
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        $promotionService = app(\App\Services\PromotionService::class);
        
        // Get all active students
        // Note: Ideally, this should be done in chunks or queued for large datasets.
        // For now, we process directly.
        $students = Siswa::where('status', 'aktif')->get();
        $count = 0;
        
        DB::beginTransaction();
        try {
            foreach ($students as $siswa) {
                // Execute promotion logic (service handles checks and updates)
                $promotionService->executeStudentPromotion($siswa, $activeYear->id, now());
                $count++;
            }
            DB::commit();
            
            return redirect()->route('waka.promotion.report') // Or back()
                ->with('success', "Proses kenaikan kelas berhasil dijalankan untuk {$count} siswa.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }
}
