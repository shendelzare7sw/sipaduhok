<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\Siswa;

class ValidasiAksesController extends Controller
{
    use WaliKelasHelper;

    /**
     * Display halaman validasi akses
     */
    public function index(Request $request): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return view('wali-kelas.validasi-akses.index')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'kelas' => null,
                'kelasList' => collect(),
                'siswaList' => collect(),
                'stats' => [
                    'ujianPending' => 0,
                    'ujianValid' => 0,
                    'raporPending' => 0,
                    'raporValid' => 0,
                ],
                'filterStatus' => null,
            ]);
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.validasi-akses.index')->with([
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'kelasList' => collect(),
                'siswaList' => collect(),
                'stats' => [
                    'ujianPending' => 0,
                    'ujianValid' => 0,
                    'raporPending' => 0,
                    'raporValid' => 0,
                ],
                'filterStatus' => null,
            ]);
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        // Get siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Search
        $search = $request->get('search');
        if ($search) {
            $siswaList = $siswaList->filter(function ($item) use ($search) {
                return false !== stristr($item->nama_lengkap, $search) || 
                       false !== stristr($item->nis, $search);
            });
        }

        // Filter
        $filterStatus = $request->get('filter');

        if ($filterStatus == 'ujian_pending') {
            $siswaList = $siswaList->where('validasi_ujian_bendahara', true)
                ->where('validasi_ujian_wali', false);
        } elseif ($filterStatus == 'ujian_selesai') {
            $siswaList = $siswaList->where('validasi_ujian_wali', true);
        } elseif ($filterStatus == 'rapor_pending') {
            $siswaList = $siswaList->where('validasi_rapor_wali', false);
        } elseif ($filterStatus == 'rapor_selesai') {
            $siswaList = $siswaList->where('validasi_rapor_wali', true);
        }

        // Count statistik (keys harus match dengan view: ujianValid, raporValid, ujianPending, raporPending)
        $stats = [
            'ujianPending' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_ujian_bendahara', true)
                ->where('validasi_ujian_wali', false)
                ->count(),
            'ujianValid' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_ujian_wali', true)
                ->count(),
            'raporPending' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_rapor_wali', false)
                ->count(),
            'raporValid' => Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->where('validasi_rapor_wali', true)
                ->count(),
        ];

        return view('wali-kelas.validasi-akses.index', [
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'siswaList' => $siswaList,
            'stats' => $stats,
            'filterStatus' => $filterStatus,
            'search' => $search,
        ]);
    }

    /**
     * Validasi akses ujian untuk satu siswa
     */
    public function validasiUjian($siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        if (!$siswa->validasi_ujian_bendahara) {
            return back()->with('error', 'Akses ujian belum divalidasi oleh Bendahara!');
        }

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

        $siswa->update([
            'validasi_rapor_wali' => true,
            'tanggal_validasi_rapor_wali' => now(),
            'validasi_rapor_oleh' => auth()->id(),
        ]);

        return back()->with('success', "Rapor {$siswa->nama_lengkap} berhasil dikirim ke Ketua PKBM!");
    }

    /**
     * Batalkan validasi akses rapor
     * CASCADE: Reset Ketua PKBM validation
     */
    public function batalkanRapor($siswaId): RedirectResponse
    {
        $siswa = Siswa::findOrFail($siswaId);

        $siswa->update([
            'validasi_rapor_wali' => false,
            'tanggal_validasi_rapor_wali' => null,
            'validasi_rapor_oleh' => null,
            // CASCADE: Reset juga validasi ketua PKBM
            'validasi_rapor_ketua' => false,
            'tanggal_validasi_rapor_ketua' => null,
            'validasi_rapor_ketua_oleh' => null,
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
            
            if ($siswa && !$siswa->validasi_rapor_wali) {
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
        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

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
        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->where('validasi_rapor_wali', false)
            ->get();

        foreach ($siswaList as $siswa) {
            $siswa->update([
                'validasi_rapor_wali' => true,
                'tanggal_validasi_rapor_wali' => now(),
                'validasi_rapor_oleh' => auth()->id(),
            ]);
        }

        return back()->with('success', "Berhasil mengirim {$siswaList->count()} rapor ke Ketua PKBM!");
    }
}