<?php

namespace App\Http\Controllers\Admin\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\PromotionService;
use App\Services\NotificationService;

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

        return view('admin.keuangan.promotion.validation', [
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

        DB::table('izin_naik_kelas_khusus')->insert([
            'siswa_id' => $validated['siswa_id'],
            'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
            'diajukan_oleh' => auth()->id(),
            'tanggal_pengajuan' => now(),
            'alasan_pengajuan' => $validated['alasan'],
            'total_tunggakan' => 0,
            'status' => 'MENUNGGU',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app(NotificationService::class)->notifyPromotionDispensasiDiajukan(1, auth()->user());

        return redirect()
            ->route('admin.keuangan.promotion.validation.index')
            ->with('success', 'Pengajuan izin khusus berhasil dikirim ke Ketua PKBM');
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'alasan' => 'required|string|max:500',
        ]);

        $created = 0;
        foreach ($validated['siswa_ids'] as $siswaId) {
            $existing = DB::table('izin_naik_kelas_khusus')
                ->where('siswa_id', $siswaId)
                ->where('tahun_ajaran_id', $validated['tahun_ajaran_id'])
                ->where('status', 'MENUNGGU')
                ->exists();

            if (!$existing) {
                DB::table('izin_naik_kelas_khusus')->insert([
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $validated['tahun_ajaran_id'],
                    'diajukan_oleh' => auth()->id(),
                    'tanggal_pengajuan' => now(),
                    'alasan_pengajuan' => $validated['alasan'],
                    'total_tunggakan' => 0,
                    'status' => 'MENUNGGU',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }

        if ($created > 0) {
            app(NotificationService::class)->notifyPromotionDispensasiDiajukan($created, auth()->user());
        }

        return redirect()
            ->route('admin.keuangan.promotion.validation.index')
            ->with('success', "Berhasil mengajukan dispensasi untuk {$created} siswa ke Ketua PKBM");
    }

    public function history(Request $request): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Filter Options
        $cabangs = \App\Models\Cabang::all();
        $kelasList = \App\Models\Kelas::where('tahun_ajaran_id', $activeYear->id)->get();

        $query = DB::table('izin_naik_kelas_khusus')
            ->join('siswa', 'izin_naik_kelas_khusus.siswa_id', '=', 'siswa.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->leftJoin('cabang', 'siswa.cabang_id', '=', 'cabang.id') // Join cabang
            ->join('users', 'izin_naik_kelas_khusus.diajukan_oleh', '=', 'users.id')
            ->leftJoin('users as approver', 'izin_naik_kelas_khusus.disetujui_oleh', '=', 'approver.id')
            ->where('izin_naik_kelas_khusus.tahun_ajaran_id', $activeYear->id)
            ->where('izin_naik_kelas_khusus.status', '!=', 'MENUNGGU')
            ->select(
                'izin_naik_kelas_khusus.*',
                'siswa.nama_lengkap as nama_siswa',
                'siswa.nis',
                'kelas.nama_kelas',
                'cabang.nama_cabang',
                'users.name as pengaju',
                'approver.name as penyetuju'
            );

        // Apply Filters
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('siswa.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('siswa.nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cabang')) {
            $query->where('siswa.cabang_id', $request->cabang);
        }

        if ($request->filled('kelas')) {
            $query->where('siswa.kelas_id', $request->kelas);
        }

        if ($request->filled('status')) {
            $query->where('izin_naik_kelas_khusus.status', $request->status);
        }

        $history = $query->orderBy('izin_naik_kelas_khusus.updated_at', 'desc')->get();

        return view('admin.keuangan.promotion.history', [
            'history' => $history,
            'tahun' => $activeYear,
            'cabangs' => $cabangs,
            'kelasList' => $kelasList,
            'filters' => $request->all()
        ]);
    }
}
