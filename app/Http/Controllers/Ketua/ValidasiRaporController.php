<?php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;

class ValidasiRaporController extends Controller
{
    /**
     * Display list of students awaiting Ketua PKBM validation.
     * Only show students where Bendahara AND Wali Kelas already validated.
     */
    public function index(Request $request): View
    {
        $activeYear = TahunAjaran::where('is_active', true)->first();

        // Base query: Students that Wali Kelas has submitted for Ketua review
        $query = Siswa::with(['kelas.cabang', 'kelas.tahunAjaran'])
            ->where('validasi_rapor_wali', true)
            ->where('status', 'aktif');

        // Filter by active year
        if ($activeYear) {
            $query->whereHas('kelas', function($q) use ($activeYear) {
                $q->where('tahun_ajaran_id', $activeYear->id);
            });
        }

        // Filter by kelas
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by Ketua validation status
        if ($request->has('status_ketua') && $request->status_ketua != '') {
            if ($request->status_ketua == 'pending') {
                $query->where('validasi_rapor_ketua', false);
            } elseif ($request->status_ketua == 'validated') {
                $query->where('validasi_rapor_ketua', true);
            }
        }

        // Search by name or NIS
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $siswaList = $query->orderBy('kelas_id')
                           ->orderBy('nama_lengkap')
                           ->paginate(50);

        // Stats for cards
        $stats = [
            'pendingTotal' => Siswa::where('validasi_rapor_wali', true)
                                   ->where('validasi_rapor_ketua', false)
                                   ->where('status', 'aktif')
                                   ->count(),
            'validatedToday' => Siswa::where('validasi_rapor_ketua', true)
                                     ->whereDate('tanggal_validasi_rapor_ketua', today())
                                     ->count(),
        ];

        // Get kelas list for filter dropdown
        $kelasList = Kelas::with('cabang')
            ->when($activeYear, function($q) use ($activeYear) {
                $q->where('tahun_ajaran_id', $activeYear->id);
            })
            ->orderBy('nama_kelas')
            ->get();

        return view('ketua.validasi-rapor.index', [
            'siswaList' => $siswaList,
            'stats' => $stats,
            'kelasList' => $kelasList,
            'activeYear' => $activeYear,
        ]);
    }

    /**
     * Validate rapor access for a student (Ketua PKBM approval).
     */
    public function validasiRapor(int $siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        // Ensure prerequisite: Wali Kelas must have submitted first
        if (!$siswa->validasi_rapor_wali) {
            return redirect()->back()->with('error', 'Wali Kelas belum mengirim rapor ini untuk divalidasi.');
        }

        $siswa->validasi_rapor_ketua = true;
        $siswa->tanggal_validasi_rapor_ketua = now();
        $siswa->validasi_rapor_ketua_oleh = auth()->id();
        $siswa->save();

        return redirect()->back()->with('success', "Akses rapor untuk {$siswa->nama_lengkap} berhasil divalidasi.");
    }

    /**
     * Cancel Ketua PKBM validation.
     */
    public function batalkanRapor(int $siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        $siswa->validasi_rapor_ketua = false;
        $siswa->tanggal_validasi_rapor_ketua = null;
        $siswa->validasi_rapor_ketua_oleh = null;
        $siswa->save();

        return redirect()->back()->with('success', "Validasi rapor untuk {$siswa->nama_lengkap} berhasil dibatalkan.");
    }

    /**
     * Bulk validate selected students.
     */
    public function bulkValidasi(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $siswaList = Siswa::whereIn('id', $request->siswa_ids)
            ->where('validasi_rapor_wali', true)
            ->get();

        $validated = 0;
        foreach ($siswaList as $siswa) {
            $siswa->validasi_rapor_ketua = true;
            $siswa->tanggal_validasi_rapor_ketua = now();
            $siswa->validasi_rapor_ketua_oleh = auth()->id();
            $siswa->save();
            $validated++;
        }

        return redirect()->back()->with('success', "Berhasil memvalidasi {$validated} siswa.");
    }

    /**
     * Validate all pending students (where Bendahara + Wali already approved).
     */
    public function validasiSemuaRapor(): RedirectResponse
    {
        $siswaList = Siswa::where('validasi_rapor_wali', true)
            ->where('validasi_rapor_ketua', false)
            ->where('status', 'aktif')
            ->get();

        if ($siswaList->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada siswa yang perlu divalidasi.');
        }

        foreach ($siswaList as $siswa) {
            $siswa->validasi_rapor_ketua = true;
            $siswa->tanggal_validasi_rapor_ketua = now();
            $siswa->validasi_rapor_ketua_oleh = auth()->id();
            $siswa->save();
        }

        return redirect()->back()->with('success', "Berhasil memvalidasi {$siswaList->count()} siswa.");
    }
}
