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
use App\Models\RequestDownloadRapor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        // Get semua siswa aktif di kelas
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
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
            return back()->with('info', 'Tidak ada siswa aktif di kelas ini.');
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
        // Hitung presensi menggunakan periode semester dari TahunAjaran
        $tahunAjaran = TahunAjaran::find($kelas->tahun_ajaran_id);
        $jumlahSakit = 0;
        $jumlahIzin = 0;
        $jumlahAlpha = 0;

        if ($tahunAjaran) {
            // Pakai getRaporPeriod (PTS=3 bulan pertama, PAS=full semester)
            $period = $tahunAjaran->getRaporPeriod($semester, $jenisRapor);

            if ($period) {
                $presensi = Presensi::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $kelas->id)
                    ->whereBetween('tanggal', [$period['start'], $period['end']])
                    ->selectRaw('
                        SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                        SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as total_izin,
                        SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as total_alpha
                    ')
                    ->first();

                $jumlahSakit = $presensi->total_sakit ?? 0;
                $jumlahIzin = $presensi->total_izin ?? 0;
                $jumlahAlpha = $presensi->total_alpha ?? 0;
            }
        }

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

        $rapor = Rapor::with(['siswa', 'kelas', 'raporNilai.mataPelajaran', 'raporNilai.nilai', 'kegiatanEkstra'])->findOrFail($raporId);

        // Pastikan rapor ini milik kelas wali kelas
        if ($rapor->kelas_id != $kelas->id) {
            return redirect()->route('wali.rapor.index')
                ->with('error', 'Rapor tidak ditemukan.');
        }

        // Auto-fill kehadiran dari presensi agar langsung tampil data terbaru
        $rapor->hitungKehadiranOtomatis();

        // If no kegiatan ekstra yet, use defaults
        $kegiatanEkstra = $rapor->kegiatanEkstra->isNotEmpty()
            ? $rapor->kegiatanEkstra
            : collect(RaporKegiatanEkstra::getDefaultKegiatan())->map(fn($nama) => new RaporKegiatanEkstra(['kegiatan_nama' => $nama, 'predikat' => null, 'keterangan' => null]));

        return view('wali-kelas.rapor.edit', [
            'rapor' => $rapor,
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'kegiatanEkstra' => $kegiatanEkstra,
        ]);
    }

    /**
     * Update rapor
     */
    public function update(Request $request, $raporId): RedirectResponse
    {
        $request->validate([
            'catatan_wali_kelas' => 'nullable|string',
            'catatan_alignment' => 'nullable|in:left,center,right,justify',
            'deskripsi_alignment' => 'nullable|in:left,center,right,justify',
            'keterangan_ekstra_alignment' => 'nullable|in:left,center,right,justify',
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
            'catatan_alignment' => $request->input('catatan_alignment', 'center'),
            'deskripsi_alignment' => $request->input('deskripsi_alignment', 'left'),
            'keterangan_ekstra_alignment' => $request->input('keterangan_ekstra_alignment', 'left'),
            'jumlah_sakit' => $request->jumlah_sakit,
            'jumlah_izin' => $request->jumlah_izin,
            'jumlah_alpha' => $request->jumlah_alpha,
            'allow_download' => $request->has('allow_download') ? true : false,
        ]);

        // Update nilai rapor jika ada
        if ($request->has('deskripsi')) {
            foreach ($request->deskripsi as $raporNilaiId => $deskripsi) {
                RaporNilai::where('id', $raporNilaiId)->update([
                    'deskripsi' => $deskripsi,
                ]);
            }
        }

        // Update visibility per mata pelajaran
        if ($request->has('visible')) {
            $visibleIds = array_keys(array_filter($request->visible));
            RaporNilai::where('rapor_id', $rapor->id)->update(['is_visible' => false]);
            RaporNilai::where('rapor_id', $rapor->id)->whereIn('id', $visibleIds)->update(['is_visible' => true]);
        }

        // Update kelompok override
        if ($request->has('kelompok_override')) {
            foreach ($request->kelompok_override as $raporNilaiId => $kelompok) {
                RaporNilai::where('id', $raporNilaiId)->update([
                    'kelompok_override' => $kelompok ?: null,
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
     * Salin format rapor saat ini ke rapor lain yang masih draft.
     */
    public function applyFormat(Request $request, $raporId): RedirectResponse
    {
        $request->validate([
            'scope' => 'required|in:kelas_ini,semua_kelas_wali',
            'include_order' => 'nullable|boolean',
            'include_deskripsi' => 'nullable|boolean',
            'include_display' => 'nullable|boolean',
            'include_kegiatan' => 'nullable|boolean',
            'include_catatan' => 'nullable|boolean',
            'include_alignment' => 'nullable|boolean',
            'overwrite_filled' => 'nullable|boolean',
        ]);

        $selectedParts = collect([
            'include_order',
            'include_deskripsi',
            'include_display',
            'include_kegiatan',
            'include_catatan',
            'include_alignment',
        ])->filter(fn($key) => $request->boolean($key));

        if ($selectedParts->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu bagian format rapor yang ingin diterapkan.');
        }

        $tenagaPendidik = $this->getTenagaPendidik();
        if (!$tenagaPendidik) {
            return back()->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);
        $accessibleClassIds = $kelasList->pluck('id');

        $sourceRapor = Rapor::with(['siswa', 'kelas', 'raporNilai', 'kegiatanEkstra'])->findOrFail($raporId);

        if (!$accessibleClassIds->contains($sourceRapor->kelas_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke rapor ini.');
        }

        $targetClassIds = $request->scope === 'semua_kelas_wali'
            ? $accessibleClassIds
            : collect([$sourceRapor->kelas_id]);

        $targetRapors = Rapor::with(['siswa', 'raporNilai', 'kegiatanEkstra'])
            ->whereIn('kelas_id', $targetClassIds)
            ->where('tahun_ajaran_id', $sourceRapor->tahun_ajaran_id)
            ->where('semester', $sourceRapor->semester)
            ->where('jenis_rapor', $sourceRapor->jenis_rapor)
            ->where('id', '!=', $sourceRapor->id)
            ->where('status', 'draft')
            ->whereHas('siswa', fn($query) => $query->where('validasi_rapor_wali', false))
            ->get();

        if ($targetRapors->isEmpty()) {
            return back()->with('info', 'Tidak ada rapor target yang bisa diperbarui. Target harus draft dan belum dikirim ke Ketua PKBM.');
        }

        $overwriteFilled = $request->has('overwrite_filled');
        $sourceNilaiByMapel = $sourceRapor->raporNilai
            ->values()
            ->mapWithKeys(fn($nilai, $index) => [
                $nilai->mata_pelajaran_id => [
                    'urutan' => $index,
                    'deskripsi' => $nilai->deskripsi,
                    'is_visible' => $nilai->is_visible,
                    'kelompok_override' => $nilai->kelompok_override,
                ],
            ]);

        $updatedRapors = 0;
        $updatedNilai = 0;

        DB::transaction(function () use (
            $request,
            $targetRapors,
            $sourceRapor,
            $sourceNilaiByMapel,
            $overwriteFilled,
            &$updatedRapors,
            &$updatedNilai
        ) {
            foreach ($targetRapors as $targetRapor) {
                $raporUpdates = [];

                if ($request->boolean('include_catatan')) {
                    $currentCatatan = trim((string) $targetRapor->catatan_wali_kelas);
                    if ($overwriteFilled || $currentCatatan === '') {
                        $raporUpdates['catatan_wali_kelas'] = $sourceRapor->catatan_wali_kelas;
                    }
                }

                if ($request->boolean('include_alignment')) {
                    $raporUpdates['catatan_alignment'] = $sourceRapor->catatan_alignment ?: 'center';
                    $raporUpdates['deskripsi_alignment'] = $sourceRapor->deskripsi_alignment ?: 'left';
                    $raporUpdates['keterangan_ekstra_alignment'] = $sourceRapor->keterangan_ekstra_alignment ?: 'left';
                }

                if (!empty($raporUpdates)) {
                    $targetRapor->update($raporUpdates);
                }

                if ($request->boolean('include_order') || $request->boolean('include_deskripsi') || $request->boolean('include_display')) {
                    foreach ($targetRapor->raporNilai as $targetNilai) {
                        $sourceNilai = $sourceNilaiByMapel->get($targetNilai->mata_pelajaran_id);
                        if (!$sourceNilai) {
                            continue;
                        }

                        $nilaiUpdates = [];

                        if ($request->boolean('include_order')) {
                            $nilaiUpdates['urutan'] = $sourceNilai['urutan'];
                        }

                        if ($request->boolean('include_deskripsi')) {
                            $currentDeskripsi = trim((string) $targetNilai->deskripsi);
                            if ($overwriteFilled || $currentDeskripsi === '') {
                                $nilaiUpdates['deskripsi'] = $sourceNilai['deskripsi'];
                            }
                        }

                        if ($request->boolean('include_display')) {
                            $nilaiUpdates['is_visible'] = $sourceNilai['is_visible'];
                            $nilaiUpdates['kelompok_override'] = $sourceNilai['kelompok_override'];
                        }

                        if (!empty($nilaiUpdates)) {
                            $targetNilai->update($nilaiUpdates);
                            $updatedNilai++;
                        }
                    }
                }

                if ($request->boolean('include_kegiatan')) {
                    if ($overwriteFilled || $targetRapor->kegiatanEkstra->isEmpty()) {
                        $targetRapor->kegiatanEkstra()->delete();

                        foreach ($sourceRapor->kegiatanEkstra as $kegiatan) {
                            RaporKegiatanEkstra::create([
                                'rapor_id' => $targetRapor->id,
                                'kegiatan_nama' => $kegiatan->kegiatan_nama,
                                'predikat' => $kegiatan->predikat,
                                'keterangan' => $kegiatan->keterangan,
                            ]);
                        }
                    }
                }

                $updatedRapors++;
            }
        });

        return back()->with(
            'success',
            "Format rapor berhasil diterapkan ke {$updatedRapors} rapor. Detail nilai tersentuh: {$updatedNilai} baris."
        );
    }

    /**
     * Terbitkan rapor (dengan optional tanggal_rilis)
     */
    public function terbitkan(Request $request, $raporId): RedirectResponse
    {
        $rapor = Rapor::findOrFail($raporId);

        // Set tanggal_rilis: dari input atau default hari ini
        $tanggalRilis = $request->input('tanggal_rilis') ? $request->input('tanggal_rilis') : now()->toDateString();
        $rapor->tanggal_rilis = $tanggalRilis;
        $rapor->save();

        $rapor->terbitkan();

        // Notify siswa and orang tua about published rapor
        $rapor->load('siswa.orangTua');
        app(\App\Services\NotificationService::class)->notifyRaporTerbit($rapor);

        return back()->with('success', 'Rapor berhasil diterbitkan! Tanggal rilis: ' . \Carbon\Carbon::parse($tanggalRilis)->format('d/m/Y'));
    }

    /**
     * Reset nilai rapor dari data nilai terbaru
     */
    public function resetNilai($raporId): RedirectResponse
    {
        $rapor = Rapor::findOrFail($raporId);

        if ($rapor->status === 'diterbitkan') {
            return back()->with('error', 'Tidak bisa reset nilai rapor yang sudah diterbitkan. Tarik kembali dulu.');
        }

        $rapor->generateFromNilai();

        return back()->with('success', 'Nilai rapor berhasil direset dari data nilai terbaru.');
    }

    /**
     * Reorder mata pelajaran (JSON endpoint)
     */
    public function reorderNilai(Request $request, $raporId)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:rapor_nilai,id',
        ]);

        $rapor = Rapor::findOrFail($raporId);

        foreach ($request->order as $index => $raporNilaiId) {
            RaporNilai::where('id', $raporNilaiId)
                ->where('rapor_id', $rapor->id)
                ->update(['urutan' => $index]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan berhasil disimpan.']);
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

        // Auto-fill kehadiran dari presensi agar selalu data terbaru
        $rapor->hitungKehadiranOtomatis();

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
    public function autoFillKehadiran(Request $request, $raporId)
    {
        $rapor = Rapor::findOrFail($raporId);
        $rapor->hitungKehadiranOtomatis();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sakit' => $rapor->jumlah_sakit,
                'izin' => $rapor->jumlah_izin,
                'alpha' => $rapor->jumlah_alpha,
            ]);
        }

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
     * Kirim rapor ke Ketua PKBM untuk divalidasi (sets validasi_rapor_wali = true on siswa)
     */
    public function kirimValidasi($raporId): RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);

        $rapor = Rapor::findOrFail($raporId);

        if ($kelas && $rapor->kelas_id != $kelas->id) {
            return back()->with('error', 'Rapor tidak ditemukan di kelas ini.');
        }

        $siswa = $rapor->siswa;

        if ($siswa->validasi_rapor_wali) {
            return back()->with('info', 'Rapor sudah pernah dikirim ke Ketua PKBM.');
        }

        $siswa->update([
            'validasi_rapor_wali'              => true,
            'tanggal_validasi_rapor_wali'      => now(),
            'validasi_rapor_oleh'              => auth()->id(),
        ]);

        // Clear revisi status if previously returned for revision
        if ($rapor->status_review_ketua === 'perlu_revisi') {
            $rapor->update([
                'status_review_ketua' => 'pending',
                'catatan_revisi_ketua' => null,
            ]);
        }

        // Notify Ketua PKBM
        app(\App\Services\NotificationService::class)->notifyRaporDikirimKeKetua($siswa, auth()->user());

        return back()->with('success', "Rapor {$siswa->nama_lengkap} berhasil dikirim ke Ketua PKBM untuk divalidasi.");
    }

    /**
     * Batalkan kiriman validasi ke Ketua PKBM (cascade reset ketua & bendahara)
     */
    public function batalkanKirimValidasi($raporId): RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);

        $rapor = Rapor::findOrFail($raporId);

        if ($kelas && $rapor->kelas_id != $kelas->id) {
            return back()->with('error', 'Rapor tidak ditemukan di kelas ini.');
        }

        $siswa = $rapor->siswa;

        $siswa->update([
            'validasi_rapor_wali'              => false,
            'tanggal_validasi_rapor_wali'      => null,
            'validasi_rapor_oleh'              => null,
            // CASCADE: reset ketua & bendahara
            'validasi_rapor_ketua'             => false,
            'tanggal_validasi_rapor_ketua'     => null,
            'validasi_rapor_ketua_oleh'        => null,
            'validasi_rapor_bendahara'         => false,
            'tanggal_validasi_rapor_bendahara' => null,
        ]);

        return back()->with('success', "Kiriman validasi rapor {$siswa->nama_lengkap} berhasil dibatalkan.");
    }

    /**
     * Kirim validasi semua rapor di kelas sekaligus
     */
    public function kirimValidasiSemua(Request $request): RedirectResponse
    {
        $request->validate([
            'semester'   => 'required|in:ganjil,genap',
            'jenis_rapor' => 'required|in:tengah_semester,akhir_semester',
        ]);

        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return back()->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get semua siswa yang punya rapor draft dan belum dikirim
        $raporList = Rapor::where('kelas_id', $kelas->id)
            ->where('semester', $request->semester)
            ->where('jenis_rapor', $request->jenis_rapor)
            ->with('siswa')
            ->get();

        $sent = 0;
        foreach ($raporList as $rapor) {
            $siswa = $rapor->siswa;
            if ($siswa && !$siswa->validasi_rapor_wali) {
                $siswa->update([
                    'validasi_rapor_wali'         => true,
                    'tanggal_validasi_rapor_wali' => now(),
                    'validasi_rapor_oleh'         => auth()->id(),
                ]);
                $sent++;
            }
        }

        if ($sent === 0) {
            return back()->with('info', 'Semua rapor sudah pernah dikirim ke Ketua PKBM.');
        }

        // Notify Ketua PKBM (bulk)
        app(\App\Services\NotificationService::class)->notifyRaporBulkDikirimKeKetua($sent, $kelas->nama_kelas, auth()->user());

        return back()->with('success', "Berhasil mengirim {$sent} rapor ke Ketua PKBM untuk divalidasi.");
    }

    /**
     * NEW: Export rapor to Excel (juga jadi TEMPLATE untuk import balik).
     */
    public function exportExcel($raporId)
    {
        $rapor = Rapor::with(['siswa', 'kelas', 'tahunAjaran', 'raporNilai.mataPelajaran', 'raporNilai.nilai', 'kegiatanEkstra'])
            ->findOrFail($raporId);

        $filename = "Rapor_" . Str::slug($rapor->siswa->nama_lengkap) . "_{$rapor->getPeriodeLabel()}.xlsx";

        return Excel::download(new RaporExport($rapor), $filename);
    }

    /**
     * Import data rapor dari file Excel hasil export (template-then-import).
     * Hanya diperbolehkan saat rapor masih draft dan belum dikirim ke Ketua PKBM.
     */
    public function importExcel(Request $request, $raporId): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $rapor = Rapor::with('siswa', 'kelas')->findOrFail($raporId);

        // Verify ownership: rapor harus di kelas yang sedang diwalikan
        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);
        if (!$kelas || $rapor->kelas_id !== $kelas->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke rapor ini.');
        }

        // Gate: status harus draft AND belum dikirim ke Ketua
        if ($rapor->status !== 'draft') {
            return back()->with('error', 'Rapor sudah diterbitkan. Tarik kembali ke draft dulu sebelum import.');
        }
        if ($rapor->siswa->validasi_rapor_wali) {
            return back()->with('error', 'Rapor sudah dikirim ke Ketua PKBM. Batalkan kiriman dulu sebelum import.');
        }

        $importer = new \App\Imports\WaliKelas\RaporImport($rapor);
        $result = $importer->import($request->file('file'));

        if (!$result['success']) {
            return back()->with('error', implode(' | ', $result['errors']));
        }

        $msg = "Import berhasil: {$result['updated_count']} mata pelajaran ter-update.";
        if (!empty($result['errors'])) {
            $msg .= ' Peringatan: ' . implode(' | ', array_slice($result['errors'], 0, 5));
            return back()->with('warning', $msg);
        }
        return back()->with('success', $msg);
    }

    /**
     * Daftar request download rapor dari orang tua.
     */
    public function requestDownloadIndex(): View
    {
        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $this->getSelectedKelas($tenagaPendidik);

        $query = RequestDownloadRapor::with(['rapor.siswa', 'user', 'siswa.kelas'])
            ->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'ditolak')")
            ->latest('tanggal_request');

        if ($kelas) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas_id', $kelas->id));
        }

        $requests = $query->paginate(25);

        return view('wali-kelas.rapor.request-download', compact('requests', 'kelas'));
    }

    /**
     * Approve request download rapor.
     */
    public function approveDownload(Request $request, $id): RedirectResponse
    {
        $downloadRequest = RequestDownloadRapor::findOrFail($id);

        $downloadRequest->update([
            'status' => 'disetujui',
            'diputuskan_oleh' => auth()->id(),
            'catatan_admin' => $request->catatan_admin,
            'tanggal_keputusan' => now(),
        ]);

        $downloadRequest->generateDownloadToken(24);

        // Notify Orang Tua
        $downloadRequest->load('siswa');
        app(\App\Services\NotificationService::class)->notifyKeputusanDownloadRapor($downloadRequest);

        return back()->with('success', "Request download dari {$downloadRequest->user->name} berhasil disetujui. Link berlaku 24 jam.");
    }

    /**
     * Reject request download rapor.
     */
    public function rejectDownload(Request $request, $id): RedirectResponse
    {
        $downloadRequest = RequestDownloadRapor::findOrFail($id);

        $downloadRequest->update([
            'status' => 'ditolak',
            'diputuskan_oleh' => auth()->id(),
            'catatan_admin' => $request->catatan_admin,
            'tanggal_keputusan' => now(),
        ]);

        // Notify Orang Tua
        $downloadRequest->load('siswa');
        app(\App\Services\NotificationService::class)->notifyKeputusanDownloadRapor($downloadRequest);

        return back()->with('success', "Request download berhasil ditolak.");
    }
}
