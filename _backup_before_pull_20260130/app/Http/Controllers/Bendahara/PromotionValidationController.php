<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\PromotionService;

class PromotionValidationController extends Controller
{
    protected $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index(Request $request): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Find students who are Academic OK but Financial FAIL
        // This process might be heavy if we iterate all students.
        // Optimization: Filter students who have unpaid bills first?
        // Or just iterate active students.
        
        $candidates = [];
        $students = Siswa::where('status', 'aktif')->get();
        
        foreach ($students as $siswa) {
            $eligibility = $this->promotionService->checkEligibility($siswa, $activeYear->id);
            
            // Criteria: Academic Tuntas AND Payment Not Lunas AND No approved dispensation yet
            if ($eligibility['academic']['is_tuntas'] && 
                $eligibility['financial']['status'] !== 'LUNAS' && 
                !$eligibility['financial']['is_dispensasi']) {
                
                // Check if already requested (MENUNGGU)
                $pendingRequest = DB::table('izin_naik_kelas_khusus')
                    ->where('siswa_id', $siswa->id)
                    ->where('tahun_ajaran_id', $activeYear->id)
                    ->where('status', 'MENUNGGU')
                    ->first();

                $candidates[] = [
                    'siswa' => $siswa,
                    'academic' => $eligibility['academic'],
                    'financial' => $eligibility['financial'],
                    'pending_request' => $pendingRequest
                ];
            }
        }

        return view('bendahara.promotion.validation', [
            'candidates' => $candidates,
            'tahun' => $activeYear
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'alasan' => 'required|string|max:500',
        ]);

        // Get unpaid amount
        $unpaid = 0; // Ideally fetch from Tagihan again or pass it. 
        // Let's simple fetch default unpaid sum logic
        $siswa = Siswa::find($validated['siswa_id']);
        // Re-use logic or straightforward query
        // Assuming Logic in Service is accurate check.
        
        DB::table('izin_naik_kelas_khusus')->insert([
            'siswa_id' => $validated['siswa_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'diajukan_oleh' => auth()->id(),
            'tanggal_pengajuan' => now(),
            'alasan_pengajuan' => $validated['alasan'],
            'total_tunggakan' => 0, // Placeholder or calculate real sum
            'status' => 'MENUNGGU',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('bendahara.promotion.validation.index')
            ->with('success', 'Pengajuan izin khusus berhasil dikirim ke Ketua PKBM');
    }
}
