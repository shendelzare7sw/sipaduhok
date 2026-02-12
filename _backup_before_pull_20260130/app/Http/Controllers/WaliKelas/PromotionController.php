<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\GuruPengajarKelas; // To verify wali kelas access? Or WaliKelas table?
// Usually WaliKelas roles are checked via middleware or Auth.
// Assuming Auth::user()->role == 'wali_kelas' and they manage a class.
// Need to find which class this user manages. 
// Looking at `WaliKelasController`, usually there is a relation.
use App\Services\PromotionService;
use App\Models\TenagaPendidik;
use App\Models\Kelas;

class PromotionController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index(): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Find Class for this Wali Kelas
        $guru = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        
        // Assuming relationship or table `wali_kelas_assignments` or simple `kelas.wali_kelas_id`
        // Recent migration `2026_01_17_200000_create_wali_kelas_assignments_table.php` suggests assignment table.
        // Let's assume we can get it.
        // Join with Kelas table to filter by Active Year
        $assignment = DB::table('wali_kelas_assignments')
            ->join('kelas', 'wali_kelas_assignments.kelas_id', '=', 'kelas.id')
            ->where('wali_kelas_assignments.tenaga_pendidik_id', $guru->id)
            ->where('kelas.tahun_ajaran_id', $activeYear->id)
            ->select('wali_kelas_assignments.*', 'kelas.id as kelas_real_id') // Avoid ambiguity
            ->first();
            
        $kelasId = $assignment ? $assignment->kelas_id : null;
        // Fallback: Check `kelas` table if `wali_kelas_id` exists there (older schema?)
        if (!$kelasId) {
             $kelas = Kelas::where('wali_kelas_id', $guru->id)->where('tahun_ajaran_id', $activeYear->id)->first();
             $kelasId = $kelas ? $kelas->id : null;
        }

        if (!$kelasId) {
            return view('wali-kelas.promotion.index', [
                'error' => 'Anda tidak tercatat sebagai Wali Kelas di tahun aktif saat ini.',
                'students' => []
            ]);
        }
        
        $kelas = Kelas::find($kelasId);
        $students = Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->get();
        
        $prediction = [];
        foreach ($students as $siswa) {
            // Check eligibility (Simulation)
            $result = $this->promotionService->checkEligibility($siswa, $activeYear->id);
            $prediction[] = [
                'siswa' => $siswa,
                'result' => $result
            ];
        }

        return view('wali-kelas.promotion.index', [
            'kelas' => $kelas,
            'prediction' => $prediction,
            'tahun' => $activeYear
        ]);
    }
}
