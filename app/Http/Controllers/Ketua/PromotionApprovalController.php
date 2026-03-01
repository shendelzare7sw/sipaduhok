<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaran;
use App\Services\NotificationService;

class PromotionApprovalController extends Controller
{
    public function index(): View
    {
        $requests = DB::table('izin_naik_kelas_khusus')
            ->join('siswa', 'izin_naik_kelas_khusus.siswa_id', '=', 'siswa.id')
            ->join('users', 'izin_naik_kelas_khusus.diajukan_oleh', '=', 'users.id')
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select(
                'izin_naik_kelas_khusus.*',
                'siswa.nama_lengkap as nama_siswa',
                'kelas.nama_kelas',
                'users.name as pengaju'
            )
            ->where('izin_naik_kelas_khusus.status', 'MENUNGGU')
            ->get();

        return view('ketua.promotion.approval', [
            'requests' => $requests
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan' => 'nullable|string'
        ]);

        $status = $validated['action'] === 'approve' ? 'DISETUJUI' : 'DITOLAK';

        DB::table('izin_naik_kelas_khusus')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'disetujui_oleh' => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_ketua' => $validated['catatan'] ?? null,
                'updated_at' => now()
            ]);

        app(NotificationService::class)->notifyPromotionDispensasiKeputusan([$id], $status, auth()->user()->name);

        return redirect()
            ->route('ketua.promotion.approval.index')
            ->with('success', 'Status pengajuan berhasil diperbarui: ' . $status);
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
            'action' => 'required|in:approve,reject',
            'catatan' => 'nullable|string',
        ]);

        $status = $validated['action'] === 'approve' ? 'DISETUJUI' : 'DITOLAK';

        DB::table('izin_naik_kelas_khusus')
            ->whereIn('id', $validated['ids'])
            ->where('status', 'MENUNGGU')
            ->update([
                'status' => $status,
                'disetujui_oleh' => auth()->id(),
                'tanggal_persetujuan' => now(),
                'catatan_ketua' => $validated['catatan'] ?? null,
                'updated_at' => now(),
            ]);

        app(NotificationService::class)->notifyPromotionDispensasiKeputusan($validated['ids'], $status, auth()->user()->name);

        $count = count($validated['ids']);
        $label = $status === 'DISETUJUI' ? 'disetujui' : 'ditolak';

        return redirect()
            ->route('ketua.promotion.approval.index')
            ->with('success', "{$count} pengajuan dispensasi berhasil {$label}");
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
            ->leftJoin('cabang', 'siswa.cabang_id', '=', 'cabang.id')
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

        return view('ketua.promotion.history', [
            'history' => $history,
            'tahun' => $activeYear,
            'cabangs' => $cabangs,
            'kelasList' => $kelasList,
            'filters' => $request->all()
        ]);
    }
}
