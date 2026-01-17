<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PilihKelasController extends Controller
{
    use WaliKelasHelper;

    /**
     * Display list of kelas to choose from.
     */
    public function index(): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // If only one kelas, auto-select and redirect to dashboard
        if ($kelasList->count() === 1) {
            session(['wali_kelas_selected' => $kelasList->first()->id]);
            return redirect()->route('wali.dashboard');
        }

        // Load additional data for each kelas
        $kelasList->load(['siswa' => function($q) {
            $q->where('status', 'aktif');
        }]);

        $currentSelectedId = session('wali_kelas_selected');

        return view('wali-kelas.pilih-kelas.index', [
            'waliKelas' => $tenagaPendidik,
            'kelasList' => $kelasList,
            'currentSelectedId' => $currentSelectedId,
        ]);
    }

    /**
     * Select a kelas and store in session.
     */
    public function select(Kelas $kelas): RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return redirect()->route('wali.dashboard')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        // Verify this kelas belongs to the wali kelas
        $kelasList = $this->getKelasWali($tenagaPendidik);
        
        if (!$kelasList->contains('id', $kelas->id)) {
            return redirect()->route('wali.pilih-kelas')
                ->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        // Store selection in session
        session(['wali_kelas_selected' => $kelas->id]);

        return redirect()->route('wali.dashboard')
            ->with('success', "Berhasil memilih kelas {$kelas->nama_kelas}!");
    }
}
