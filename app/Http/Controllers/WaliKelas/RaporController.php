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
use App\Models\RaporKegiatanEkstra;
use App\Models\TemplateCapaianKompetensi;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RaporExport;

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

        // Filter semester & jenis rapor
        $semester = $request->get('semester', 'ganjil');
        $jenisRapor = $request->get('jenis_rapor', 'akhir_semester');

        // Get ALL active students in this class (not just those with rapor)
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // For each student, check if rapor exists
        $raporList = $siswaList->map(function($siswa) use ($kelas, $semester, $jenisRapor) {
            $rapor = Rapor::where('siswa_id', $siswa->id)
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->where('semester', $semester)
                ->where('jenis_rapor', $jenisRapor)
                ->first();

            return [
                'siswa' => $siswa,
                'rapor' => $rapor, // Will be null if not exists
            ];
        });

        // Count status
        $statusCount = [
            'draft' => $raporList->whereNotNull('rapor')->pluck('rapor')->where('status', 'draft')->count(),
            'diterbitkan' => $raporList->whereNotNull('rapor')->pluck('rapor')->where('status', 'diterbitkan')->count(),
            'belum_dibuat' => $raporList->whereNull('rapor')->count(),
        ];

        return view('wali-kelas.rapor.index', [
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'raporList' => $raporList,
            'semester' => $semester,
            'jenisRapor' => $jenisRapor,
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
            'jenis_rapor' => 'required|in:tengah_semester,akhir_semester',
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

        // Get semua siswa di kelas YANG SUDAH DIVALIDASI 3 LEVEL
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->where('validasi_rapor_bendahara', true)
            ->where('validasi_rapor_wali', true)
            ->where('validasi_rapor_ketua', true)
            ->get();

        $generated = 0;
        $skipped = 0;
        foreach ($siswaList as $siswa) {
            // Cek apakah rapor sudah ada
            $raporExists = Rapor::where('siswa_id', $siswa->id)
                ->where('kelas_id', $kelas->id)
                ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
                ->where('semester', $request->semester)
                ->where('jenis_rapor', $request->jenis_rapor)
                ->exists();

            if (!$raporExists) {
                $this->generateRaporSiswa($siswa, $kelas, $request->semester, $request->jenis_rapor);
                $generated++;
            } else {
                $skipped++;
            }
        }

        if ($generated == 0 && $skipped == 0) {
            return back()->with('info', 'Tidak ada siswa dengan validasi lengkap (Bendahara + Wali + Ketua PKBM) untuk generate rapor.');
        }

        return back()->with('success', "Berhasil generate {$generated} rapor! " . ($skipped > 0 ? "({$skipped} sudah ada)" : ""));
    }

    /**
     * Generate rapor untuk satu siswa (per individual)
     */
    public function generateSingle(Request $request, $siswaId): RedirectResponse
    {
        $request->validate([
            'semester' => 'required|in:ganjil,genap',
            'jenis_rapor' => 'required|in:tengah_semester,akhir_semester',
        ]);

        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return back()->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return back()->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $siswa = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->first();

        if (!$siswa) {
            return back()->with('error', 'Siswa tidak ditemukan di kelas ini.');
        }

        // Cek validasi 3 level
        if (!$siswa->hasFullRaporAccess()) {
            return back()->with('error', 'Siswa belum divalidasi lengkap oleh Bendahara, Wali Kelas, dan Ketua PKBM.');
        }

        // Cek apakah rapor sudah ada
        $raporExists = Rapor::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('semester', $request->semester)
            ->where('jenis_rapor', $request->jenis_rapor)
            ->exists();

        if ($raporExists) {
            return back()->with('info', 'Rapor siswa ini sudah pernah di-generate.');
        }

        $this->generateRaporSiswa($siswa, $kelas, $request->semester, $request->jenis_rapor);

        return back()->with('success', "Berhasil generate rapor untuk {$siswa->nama_lengkap}!");
    }

    /**
     * Generate rapor untuk satu siswa (helper method)
     */
    private function generateRaporSiswa($siswa, $kelas, $semester, $jenisRapor)
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
            'jenis_rapor' => $jenisRapor,
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
            'kegiatan_ekstra' => 'nullable|array',
            'kegiatan_ekstra.*.kegiatan_nama' => 'nullable|string|max:100',
            'kegiatan_ekstra.*.predikat' => 'nullable|in:A,B,C',
            'kegiatan_ekstra.*.keterangan' => 'nullable|string',
            'allow_download' => 'nullable|boolean',
        ]);

        $rapor = Rapor::findOrFail($raporId);

        // Update rapor
        $rapor->update([
            'catatan_wali_kelas' => $request->catatan_wali_kelas,
            'jumlah_sakit' => $request->jumlah_sakit,
            'jumlah_izin' => $request->jumlah_izin,
            'jumlah_alpha' => $request->jumlah_alpha,
            'allow_download' => $request->has('allow_download') ? true : false,
        ]);

        // Update nilai rapor jika ada
        if ($request->has('nilai')) {
            foreach ($request->nilai as $raporNilaiId => $data) {
                RaporNilai::where('id', $raporNilaiId)->update([
                    'deskripsi' => $data['deskripsi'] ?? null,
                ]);
            }
        }

        // Update kegiatan ekstrakurikuler
        if ($request->has('kegiatan_ekstra')) {
            // Delete existing
            $rapor->kegiatanEkstra()->delete();

            // Create new from request
            foreach ($request->kegiatan_ekstra as $kegiatan) {
                if (!empty($kegiatan['kegiatan_nama'])) {
                    RaporKegiatanEkstra::create([
                        'rapor_id' => $rapor->id,
                        'kegiatan_nama' => $kegiatan['kegiatan_nama'],
                        'predikat' => $kegiatan['predikat'] ?? null,
                        'keterangan' => $kegiatan['keterangan'] ?? null,
                    ]);
                }
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
     * Tarik kembali rapor yang sudah diterbitkan
     */
    public function tarikKembali($raporId): RedirectResponse
    {
        $rapor = Rapor::findOrFail($raporId);
        
        // Only allow retract if currently published
        if ($rapor->status !== 'diterbitkan') {
            return back()->with('error', 'Hanya rapor yang sudah diterbitkan yang bisa ditarik kembali.');
        }
        
        $rapor->tarikKembali();
        
        return back()->with('success', 'Rapor berhasil ditarik kembali. Status kembali ke draft dan tidak terlihat oleh orang tua.');
    }

    /**
     * Delete/Batalkan rapor (only for draft)
     */
    public function destroy($raporId): RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return back()->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return back()->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $rapor = Rapor::findOrFail($raporId);

        // Pastikan rapor ini milik kelas wali kelas
        if ($rapor->kelas_id != $kelas->id) {
            return back()->with('error', 'Rapor tidak ditemukan.');
        }

        // Hanya bisa hapus rapor dengan status draft
        if ($rapor->status !== 'draft') {
            return back()->with('error', 'Hanya rapor dengan status draft yang bisa dihapus. Rapor yang sudah diterbitkan tidak bisa dihapus.');
        }

        $siswaName = $rapor->siswa->nama_lengkap ?? 'Unknown';

        // Delete rapor (cascade akan otomatis hapus rapor_nilai dan kegiatan_ekstra)
        $rapor->delete();

        return back()->with('success', "Rapor {$siswaName} berhasil dihapus. Anda bisa generate ulang dengan nilai yang sudah diperbaiki.");
    }

    /**
     * Preview rapor
     */
    public function preview($raporId): View
    {
        $rapor = Rapor::with([
            'siswa',
            'kelas.waliKelas',
            'tahunAjaran',
            'raporNilai.mataPelajaran',
            'raporNilai.nilai', // For grade components: rata_tugas, rata_latihan, rata_uh, pts
            'kegiatanEkstra', // For ekstrakurikuler activities
        ])->findOrFail($raporId);

        // Route to different templates based on jenis_rapor
        if ($rapor->jenis_rapor === 'tengah_semester') {
            return view('wali-kelas.rapor.preview-pts', compact('rapor'));
        } else {
            return view('wali-kelas.rapor.preview-pas', compact('rapor'));
        }
    }

    /**
     * Print rapor (serve uploaded PDF or generate from template)
     */
    public function print($raporId)
    {
        $rapor = Rapor::with(['siswa', 'kelas.tahunAjaran', 'raporNilai.mataPelajaran', 'kegiatanEkstra'])->findOrFail($raporId);

        // If upload mode, serve the uploaded PDF
        if ($rapor->input_mode === 'upload_pdf' && $rapor->uploaded_pdf_path) {
            if (Storage::exists($rapor->uploaded_pdf_path)) {
                return Storage::download($rapor->uploaded_pdf_path);
            } else {
                return back()->with('error', 'File PDF tidak ditemukan.');
            }
        }

        // Auto-generate mode: choose template based on jenis_rapor
        $viewName = $rapor->jenis_rapor === 'tengah_semester'
            ? 'wali-kelas.rapor.print-pts'
            : 'wali-kelas.rapor.print-pas';

        return view($viewName, [
            'rapor' => $rapor,
        ]);
    }

    /**
     * NEW: Create rapor with mode selection (auto-generate or upload PDF)
     */
    public function createWithMode(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'semester' => 'required|in:ganjil,genap',
            'jenis_rapor' => 'required|in:tengah_semester,akhir_semester',
            'mode' => 'required|in:auto_generate,upload_pdf',
            'uploaded_pdf' => 'required_if:mode,upload_pdf|file|mimes:pdf|max:10240', // 10MB max
        ]);

        $siswa = Siswa::findOrFail($request->siswa_id);
        $kelas = $siswa->kelas;

        if (!$kelas) {
            return back()->with('error', 'Siswa tidak terdaftar di kelas manapun.');
        }

        // Check if rapor already exists
        $exists = Rapor::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->where('semester', $request->semester)
            ->where('jenis_rapor', $request->jenis_rapor)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Rapor untuk siswa ini sudah ada.');
        }

        // Handle upload PDF mode
        if ($request->mode === 'upload_pdf' && $request->hasFile('uploaded_pdf')) {
            $path = $request->file('uploaded_pdf')->store('rapor/uploaded', 'local');

            $rapor = Rapor::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->id,
                'tahun_ajaran_id' => $kelas->tahun_ajaran_id,
                'semester' => $request->semester,
                'jenis_rapor' => $request->jenis_rapor,
                'input_mode' => 'upload_pdf',
                'uploaded_pdf_path' => $path,
                'status' => 'draft',
            ]);

            return redirect()->route('wali.rapor.edit', $rapor->id)
                ->with('success', 'Rapor dengan PDF upload berhasil dibuat!');
        }

        // Auto-generate mode
        $rapor = $this->generateRaporSiswa($siswa, $kelas, $request->semester, $request->jenis_rapor);

        return redirect()->route('wali.rapor.edit', $rapor->id)
            ->with('success', 'Rapor berhasil di-generate!');
    }

    /**
     * NEW: Auto-fill kehadiran from presensi table
     */
    public function autoFillKehadiran($raporId): RedirectResponse
    {
        $rapor = Rapor::findOrFail($raporId);
        $rapor->hitungKehadiranOtomatis();

        return back()->with('success', 'Kehadiran berhasil di-auto-fill dari data presensi!');
    }

    /**
     * NEW: Apply template to single rapor nilai
     */
    public function applyTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'rapor_id' => 'required|exists:rapor,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'template_id' => 'required|exists:template_capaian_kompetensi,id',
        ]);

        $template = TemplateCapaianKompetensi::findOrFail($request->template_id);

        // Find rapor_nilai
        $raporNilai = RaporNilai::where('rapor_id', $request->rapor_id)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->first();

        if (!$raporNilai) {
            return back()->with('error', 'Rapor nilai tidak ditemukan.');
        }

        // Only fill if deskripsi is empty (tidak overwrite yang sudah ada)
        if (empty($raporNilai->deskripsi)) {
            $raporNilai->update([
                'deskripsi' => $template->template_text,
            ]);
            return back()->with('success', 'Template berhasil diterapkan!');
        } else {
            return back()->with('info', 'Deskripsi sudah ada, tidak di-overwrite. Kosongkan dulu jika mau ganti.');
        }
    }

    /**
     * NEW: Apply template to all students in class for specific mapel
     */
    public function applyTemplateToAll(Request $request): RedirectResponse
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:ganjil,genap',
            'jenis_rapor' => 'required|in:tengah_semester,akhir_semester',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'template_id' => 'required|exists:template_capaian_kompetensi,id',
        ]);

        $template = TemplateCapaianKompetensi::findOrFail($request->template_id);

        // Get all rapor for this class, semester, jenis
        $raporList = Rapor::where('kelas_id', $request->kelas_id)
            ->where('semester', $request->semester)
            ->where('jenis_rapor', $request->jenis_rapor)
            ->pluck('id');

        // Update all rapor_nilai for this mapel (ONLY empty deskripsi)
        $updated = RaporNilai::whereIn('rapor_id', $raporList)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where(function($q) {
                $q->whereNull('deskripsi')
                  ->orWhere('deskripsi', '');
            })
            ->update([
                'deskripsi' => $template->template_text,
            ]);

        return back()->with('success', "Template berhasil diterapkan ke {$updated} siswa (yang deskripsinya masih kosong)!");
    }

    /**
     * NEW: Export rapor to Excel
     */
    public function exportExcel($raporId)
    {
        $rapor = Rapor::with(['siswa', 'kelas.tahunAjaran', 'raporNilai.mataPelajaran', 'kegiatanEkstra'])
            ->findOrFail($raporId);

        $filename = "Rapor_{$rapor->siswa->nama_lengkap}_{$rapor->getPeriodeLabel()}.xlsx";

        return Excel::download(new RaporExport($rapor), $filename);
    }
}