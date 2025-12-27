<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\Siswa;

class ValidasiAksesController extends Controller
{
    /**
     * Display halaman validasi akses
     */
    public function index(Request $request): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        if (!$kelas) {
            return view('wali-kelas.validasi-akses.index')->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Filter
        $filterStatus = $request->get('filter');

        if ($filterStatus == 'ujian_pending') {
            $siswaList = $siswaList->where('validasi_ujian_bendahara', true)
                ->where('validasi_ujian_wali', false);
        } elseif ($filterStatus == 'ujian_selesai') {
            $siswaList = $siswaList->where('validasi_ujian_wali', true);
        } elseif ($filterStatus == 'rapor_pending') {
            $siswaList = $siswaList->where('validasi_rapor_bendahara', true)
                ->where('validasi_rapor_wali', false);
        } elseif ($filterStatus == 'rapor_selesai') {
            $siswaList = $siswaList->where('validasi_rapor_wali', true);
        }

        // Count statistik
        $stats = [
            'ujian_pending' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_ujian_bendahara', true)
                ->where('validasi_ujian_wali', false)
                ->count(),
            'ujian_selesai' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_ujian_wali', true)
                ->count(),
            'rapor_pending' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_rapor_bendahara', true)
                ->where('validasi_rapor_wali', false)
                ->count(),
            'rapor_selesai' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_rapor_wali', true)
                ->count(),
        ];

        return view('wali-kelas.validasi-akses.index', [
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'stats' => $stats,
            'filterStatus' => $filterStatus,
        ]);
    }

    /**
     * Validasi akses ujian untuk satu siswa
     */
    public function validasiUjian($siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        // Cek apakah sudah divalidasi bendahara
        if (!$siswa->validasi_ujian_bendahara) {
            return back()->with('error', 'Akses ujian belum divalidasi oleh Bendahara!');
        }

        // Validasi oleh wali kelas
        $siswa->update([
            'validasi_ujian_wali' => true,
            'tanggal_validasi_ujian_wali' => now(),
            'validasi_ujian_oleh' => auth()->id(),
        ]);

        return back()->with('success', "Akses ujian untuk {$siswa->nama_lengkap} berhasil divalidasi!");
    }

    /**
     * Batalkan validasi akses ujian
     */
    public function batalkanUjian($siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        $siswa->update([
            'validasi_ujian_wali' => false,
            'tanggal_validasi_ujian_wali' => null,
            'validasi_ujian_oleh' => null,
        ]);

        return back()->with('success', "Validasi akses ujian untuk {$siswa->nama_lengkap} dibatalkan!");
    }

    /**
     * Validasi akses rapor untuk satu siswa
     */
    public function validasiRapor($siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        // Cek apakah sudah divalidasi bendahara
        if (!$siswa->validasi_rapor_bendahara) {
            return back()->with('error', 'Akses rapor belum divalidasi oleh Bendahara!');
        }

        // Validasi oleh wali kelas
        $siswa->update([
            'validasi_rapor_wali' => true,
            'tanggal_validasi_rapor_wali' => now(),
            'validasi_rapor_oleh' => auth()->id(),
        ]);

        return back()->with('success', "Akses rapor untuk {$siswa->nama_lengkap} berhasil divalidasi!");
    }

    /**
     * Batalkan validasi akses rapor
     */
    public function batalkanRapor($siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        $siswa->update([
            'validasi_rapor_wali' => false,
            'tanggal_validasi_rapor_wali' => null,
            'validasi_rapor_oleh' => null,
        ]);

        return back()->with('success', "Validasi akses rapor untuk {$siswa->nama_lengkap} dibatalkan!");
    }

    /**
     * Bulk validasi akses ujian
     */
    public function bulkValidasiUjian(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $validated = 0;
        foreach ($request->siswa_ids as $siswaId) {
            $siswa = Siswa::find($siswaId);
            
            // Cek apakah sudah divalidasi bendahara
            if ($siswa && $siswa->validasi_ujian_bendahara && !$siswa->validasi_ujian_wali) {
                $siswa->update([
                    'validasi_ujian_wali' => true,
                    'tanggal_validasi_ujian_wali' => now(),
                    'validasi_ujian_oleh' => auth()->id(),
                ]);
                $validated++;
            }
        }

        return back()->with('success', "Berhasil validasi akses ujian untuk {$validated} siswa!");
    }

    /**
     * Bulk validasi akses rapor
     */
    public function bulkValidasiRapor(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $validated = 0;
        foreach ($request->siswa_ids as $siswaId) {
            $siswa = Siswa::find($siswaId);
            
            // Cek apakah sudah divalidasi bendahara
            if ($siswa && $siswa->validasi_rapor_bendahara && !$siswa->validasi_rapor_wali) {
                $siswa->update([
                    'validasi_rapor_wali' => true,
                    'tanggal_validasi_rapor_wali' => now(),
                    'validasi_rapor_oleh' => auth()->id(),
                ]);
                $validated++;
            }
        }

        return back()->with('success', "Berhasil validasi akses rapor untuk {$validated} siswa!");
    }

    /**
     * Validasi semua siswa yang pending
     */
    public function validasiSemuaUjian(): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->where('validasi_ujian_bendahara', true)
            ->where('validasi_ujian_wali', false)
            ->get();

        foreach ($siswaList as $siswa) {
            $siswa->update([
                'validasi_ujian_wali' => true,
                'tanggal_validasi_ujian_wali' => now(),
                'validasi_ujian_oleh' => auth()->id(),
            ]);
        }

        return back()->with('success', "Berhasil validasi akses ujian untuk {$siswaList->count()} siswa!");
    }

    /**
     * Validasi semua rapor yang pending
     */
    public function validasiSemuaRapor(): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->where('validasi_rapor_bendahara', true)
            ->where('validasi_rapor_wali', false)
            ->get();

        foreach ($siswaList as $siswa) {
            $siswa->update([
                'validasi_rapor_wali' => true,
                'tanggal_validasi_rapor_wali' => now(),
                'validasi_rapor_oleh' => auth()->id(),
            ]);
        }

        return back()->with('success', "Berhasil validasi akses rapor untuk {$siswaList->count()} siswa!");
    }
}