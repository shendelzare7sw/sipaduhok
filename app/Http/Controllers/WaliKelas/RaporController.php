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
use App\Models\Rapor;
use App\Models\RaporNilai;
use App\Models\Nilai;
use App\Models\Presensi;
use App\Models\MataPelajaran;

class RaporController extends Controller
{
    use WaliKelasHelper;

    /**
     * Display daftar rapor
     */
    public function index(Request $request): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return view('wali-kelas.rapor.index')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'kelas' => null,
                'kelasList' => collect(),
                'raporList' => collect(),
                'semester' => 'ganjil',
                'statusCount' => ['draft' => 0, 'diterbitkan' => 0],
            ]);
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.rapor.index')->with([
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'kelasList' => collect(),
                'raporList' => collect(),
                'semester' => 'ganjil',
                'statusCount' => ['draft' => 0, 'diterbitkan' => 0],
            ]);
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        // Filter semester
        $semester = $request->get('semester', 'ganjil');

        // Get rapor siswa
        $raporList = Rapor::where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('semester', $semester)
            ->with('siswa')
            ->orderBy('created_at', 'desc')
            ->get();

        // Count status
        $statusCount = [
            'draft' => $raporList->where('status', 'draft')->count(),
            'diterbitkan' => $raporList->where('status', 'diterbitkan')->count(),
        ];

        return view('wali-kelas.rapor.index', [
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'raporList' => $raporList,
            'semester' => $semester,
            'statusCount' => $statusCount,
        ]);
    }

    /**
     * Generate rapor untuk semua siswa
     */
    public function generateAll(Request $request): RedirectResponse
    {
        $request->validate([
            'semester' => 'required|in:ganjil,genap',
        ]);

        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return back()->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return back()->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get semua siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get();

        $generated = 0;
        foreach ($siswaList as $siswa) {
            // Cek apakah rapor sudah ada
            $raporExists = Rapor::where('siswa_id', $siswa->id)
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->where('semester', $request->semester)
                ->exists();

            if (!$raporExists) {
                $this->generateRaporSiswa($siswa, $kelas, $request->semester);
                $generated++;
            }
        }

        return back()->with('success', "Berhasil generate {$generated} rapor!");
    }

    /**
     * Generate rapor untuk satu siswa
     */
    private function generateRaporSiswa($siswa, $kelas, $semester)
    {
        // Hitung presensi
        $bulanAwal = $semester == 'ganjil' ? 7 : 1;
        $bulanAkhir = $semester == 'ganjil' ? 12 : 6;

        $jumlahSakit = Presensi::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->whereMonth('tanggal', '>=', $bulanAwal)
            ->whereMonth('tanggal', '<=', $bulanAkhir)
            ->where('status', 'sakit')
            ->count();

        $jumlahIzin = Presensi::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->whereMonth('tanggal', '>=', $bulanAwal)
            ->whereMonth('tanggal', '<=', $bulanAkhir)
            ->where('status', 'izin')
            ->count();

        $jumlahAlpha = Presensi::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->whereMonth('tanggal', '>=', $bulanAwal)
            ->whereMonth('tanggal', '<=', $bulanAkhir)
            ->where('status', 'alpha')
            ->count();

        // Buat rapor
        $rapor = Rapor::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
            'semester' => $semester,
            'jumlah_sakit' => $jumlahSakit,
            'jumlah_izin' => $jumlahIzin,
            'jumlah_alpha' => $jumlahAlpha,
            'status' => 'draft',
        ]);

        // Generate nilai rapor dari data nilai
        $rapor->generateFromNilai();

        return $rapor;
    }

    /**
     * Show/edit rapor
     */
    public function edit($raporId): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);
        $kelasList = $this->getKelasWali($tenagaPendidik);

        $rapor = Rapor::with(['siswa', 'kelas', 'raporNilai.mataPelajaran'])->findOrFail($raporId);

        // Pastikan rapor ini milik kelas wali kelas
        if ($rapor->kelas_id != $kelas->id) {
            return redirect()->route('wali.rapor.index')
                ->with('error', 'Rapor tidak ditemukan.');
        }

        return view('wali-kelas.rapor.edit', [
            'rapor' => $rapor,
            'kelas' => $kelas,
            'kelasList' => $kelasList,
        ]);
    }

    /**
     * Update rapor
     */
    public function update(Request $request, $raporId): RedirectResponse
    {
        $request->validate([
            'catatan_wali_kelas' => 'nullable|string',
            'jumlah_sakit' => 'required|integer|min:0',
            'jumlah_izin' => 'required|integer|min:0',
            'jumlah_alpha' => 'required|integer|min:0',
            'nilai' => 'nullable|array',
            'nilai.*.deskripsi' => 'nullable|string',
        ]);

        $rapor = Rapor::findOrFail($raporId);

        // Update rapor
        $rapor->update([
            'catatan_wali_kelas' => $request->catatan_wali_kelas,
            'jumlah_sakit' => $request->jumlah_sakit,
            'jumlah_izin' => $request->jumlah_izin,
            'jumlah_alpha' => $request->jumlah_alpha,
        ]);

        // Update nilai rapor jika ada
        if ($request->has('nilai')) {
            foreach ($request->nilai as $raporNilaiId => $data) {
                RaporNilai::where('id', $raporNilaiId)->update([
                    'deskripsi' => $data['deskripsi'] ?? null,
                ]);
            }
        }

        return back()->with('success', 'Rapor berhasil diperbarui!');
    }

    /**
     * Terbitkan rapor
     */
    public function terbitkan($raporId): RedirectResponse
    {
        $rapor = Rapor::findOrFail($raporId);
        $rapor->terbitkan();

        // Notify siswa and orang tua about published rapor
        $rapor->load('siswa.orangTua');
        app(\App\Services\NotificationService::class)->notifyRaporTerbit($rapor);

        return back()->with('success', 'Rapor berhasil diterbitkan!');
    }

    /**
     * Preview rapor
     */
    public function preview($raporId): View
    {
        $rapor = Rapor::with(['siswa', 'kelas', 'raporNilai.mataPelajaran'])->findOrFail($raporId);

        return view('wali-kelas.rapor.preview', [
            'rapor' => $rapor,
        ]);
    }

    /**
     * Print rapor
     */
    public function print($raporId)
    {
        $rapor = Rapor::with(['siswa', 'kelas.tahunAjaran', 'raporNilai.mataPelajaran'])->findOrFail($raporId);

        return view('wali-kelas.rapor.print', [
            'rapor' => $rapor,
        ]);
    }
}