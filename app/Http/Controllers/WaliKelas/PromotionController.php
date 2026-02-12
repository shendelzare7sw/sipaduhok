<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Services\PromotionService;

class PromotionController extends Controller
{
    use WaliKelasHelper;

    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index(Request $request): View|RedirectResponse
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        
        $wali = $this->getTenagaPendidik();
        if (!$wali) {
            return redirect()->route('wali.dashboard')->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($wali)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($wali);
        
        if (!$kelas) {
            return view('wali-kelas.promotion.index', [
                'error' => 'Anda tidak tercatat sebagai Wali Kelas di tahun aktif saat ini.',
                'students' => []
            ]);
        }
        
        // Ensure $kelas relationship loaded if needed by view (usually nice to have)
        // Helper returns class with cabang and tahunAjaran.
        
        // Load data siswa with Name Search
        $query = Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif');
        
        if ($request->has('search') && $request->search != '') {
             $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }
        
        $students = $query->orderBy('nama_lengkap', 'asc')->get();
        
        $prediction = [];
        foreach ($students as $siswa) {
            // Check eligibility
            $result = $this->promotionService->checkEligibility($siswa, $activeYear->id);
            $prediction[] = [
                'siswa' => $siswa,
                'result' => $result
            ];
        }
        
        // Filter by Prediction Status (On-Memory)
        if ($request->has('status_filter') && $request->status_filter != '') {
            $status = $request->status_filter;
            $prediction = collect($prediction)->filter(function($item) use ($status) {
                if ($status == 'aman') return $item['result']['eligible'];
                if ($status == 'rawan') return !$item['result']['eligible'];
                return true;
            })->values(); // reset keys
        }

        return view('wali-kelas.promotion.index', [
            'kelas' => $kelas,
            'prediction' => $prediction,
            'tahun' => $activeYear
        ]);
    }
}
