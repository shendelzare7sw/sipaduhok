<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

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

        return redirect()
            ->route('ketua.promotion.approval.index')
            ->with('success', 'Status pengajuan berhasil diperbarui: ' . $status);
    }
}
