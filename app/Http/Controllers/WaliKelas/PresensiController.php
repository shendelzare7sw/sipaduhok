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
use App\Models\Presensi;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class PresensiController extends Controller
{
    use WaliKelasHelper;

    private function statusValidasiForWaliInput(string $status): ?string
    {
        return in_array($status, ['sakit', 'izin'], true) ? 'disetujui' : null;
    }

    /** Kumpulan id kelas yang diampu wali (dimemo per-request). */
    private ?\Illuminate\Support\Collection $kelasIdsWaliCache = null;

    private function kelasIdsWali(): \Illuminate\Support\Collection
    {
        if ($this->kelasIdsWaliCache === null) {
            $tenagaPendidik = $this->getTenagaPendidik();
            $this->kelasIdsWaliCache = $tenagaPendidik
                ? $this->getKelasWali($tenagaPendidik)->pluck('id')
                : collect();
        }

        return $this->kelasIdsWaliCache;
    }

    /**
     * Pastikan kelas yang dituju benar-benar diampu wali. Cegah wali mengubah presensi
     * kelas lain lewat kelas_id sembarang (IDOR lintas-kelas).
     */
    private function assertKelasMilikWali($kelasId): void
    {
        if (!$this->kelasIdsWali()->contains((int) $kelasId)) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }
    }

    /**
     * Pastikan siswa memang berada di kelas tujuan. Cegah penyisipan siswa kelas lain.
     */
    private function assertSiswaDiKelas($siswaId, $kelasId): void
    {
        $milik = Siswa::where('id', $siswaId)
            ->where('kelas_id', $kelasId)
            ->exists();

        if (!$milik) {
            abort(403, 'Siswa tidak berada di kelas ini.');
        }
    }

    /**
     * Pastikan record presensi berada di salah satu kelas yang diampu wali (cek via
     * kelas_id presensi maupun kelas siswa untuk data lama yang tidak sinkron).
     */
    private function assertPresensiMilikWali(Presensi $presensi): void
    {
        $kelasIds = $this->kelasIdsWali();
        if (!$kelasIds->contains($presensi->kelas_id)
            && !$kelasIds->contains(optional($presensi->siswa)->kelas_id)) {
            abort(403, 'Anda tidak memiliki akses ke data presensi ini.');
        }
    }

    /**
     * Display presensi siswa
     */
    public function index(Request $request): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return view('wali-kelas.presensi.index')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'kelas' => null,
                'kelasList' => collect(),
                'siswaList' => collect(),
                'presensiData' => [],
                'rekapBulan' => [],
                'tanggal' => now()->toDateString(),
                'bulan' => now()->month,
                'tahun' => now()->year,
            ]);
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.presensi.index')->with([
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'kelasList' => collect(),
                'siswaList' => collect(),
                'presensiData' => [],
                'rekapBulan' => [],
                'tanggal' => now()->toDateString(),
                'bulan' => now()->month,
                'tahun' => now()->year,
            ]);
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        // Filter tanggal
        $tanggal = $request->get('tanggal', now()->toDateString());
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $semester = $request->get('semester'); // ganjil, genap, or null

        // Get active tahun ajaran for semester periods
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        // Get siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Get presensi untuk tanggal yang dipilih
        $presensiData = [];
        foreach ($siswaList as $siswa) {
            $presensi = Presensi::where('siswa_id', $siswa->id)
                ->where('kelas_id', $kelas->id)
                ->whereDate('tanggal', $tanggal)
                ->first();

            $presensiData[$siswa->id] = $presensi;
        }

        // Rekap presensi: per semester atau per bulan
        $rekapBulan = [];
        $semesterPeriod = null;

        if ($semester && $tahunAjaran) {
            $periods = $tahunAjaran->getSemesterPeriods();
            $semesterPeriod = $periods[$semester] ?? null;
        }

        foreach ($siswaList as $siswa) {
            $query = Presensi::where('siswa_id', $siswa->id)
                ->where('kelas_id', $kelas->id);

            if ($semesterPeriod) {
                $query->whereBetween('tanggal', [$semesterPeriod['start'], $semesterPeriod['end']]);
            } else {
                $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            }

            $counts = (clone $query)->selectRaw('
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
            ')->first();

            $rekapBulan[$siswa->id] = [
                'hadir' => $counts->hadir ?? 0,
                'sakit' => $counts->sakit ?? 0,
                'izin'  => $counts->izin ?? 0,
                'alpha' => $counts->alpha ?? 0,
            ];
        }

        return view('wali-kelas.presensi.index', [
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'siswaList' => $siswaList,
            'presensiData' => $presensiData,
            'rekapBulan' => $rekapBulan,
            'tanggal' => $tanggal,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    /**
     * Update presensi siswa
     */
    public function updatePresensi(Request $request): RedirectResponse
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:500',
        ]);

        // IDOR guard: kelas harus diampu wali & siswa harus benar berada di kelas itu.
        $this->assertKelasMilikWali($request->kelas_id);
        $this->assertSiswaDiKelas($request->siswa_id, $request->kelas_id);

        Presensi::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'kelas_id' => $request->kelas_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'status_validasi' => $this->statusValidasiForWaliInput($request->status),
                'diinput_oleh' => auth()->id(),
            ]
        );

        return back()->with('success', 'Presensi berhasil diperbarui!');
    }

    /**
     * Display izin yang perlu divalidasi
     */
    public function validasiIzin(): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return view('wali-kelas.presensi.validasi-izin')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'kelas' => null,
                'kelasList' => collect(),
                'pengajuanPending' => collect(),
            ]);
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.presensi.validasi-izin')->with([
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'kelasList' => collect(),
                'pengajuanPending' => collect(),
            ]);
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        $siswaIds = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->pluck('id');

        // Get pengajuan izin yang status validasinya pending.
        // Cocokkan juga lewat siswa_id agar pengajuan tetap tampil jika kelas_id presensi lama tidak sinkron.
        $pengajuanIzin = Presensi::where(function ($query) use ($kelas, $siswaIds) {
                $query->where('kelas_id', $kelas->id)
                    ->orWhereIn('siswa_id', $siswaIds);
            })
            ->whereIn('status', ['sakit', 'izin'])
            ->where(function ($query) {
                $query->where('status_validasi', 'pending')
                    ->orWhere(function ($legacyQuery) {
                        $legacyQuery->whereNull('status_validasi')
                            ->where('keterangan', 'LIKE', '%Diajukan oleh wali siswa%');
                    });
            })
            ->with(['siswa', 'inputBy'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('wali-kelas.presensi.validasi-izin', [
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'pengajuanPending' => $pengajuanIzin,
        ]);
    }

    /**
     * Validasi pengajuan izin
     */
    public function prosesValidasiIzin(Request $request, $presensiId): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:setuju,tolak',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $presensi = Presensi::with('siswa')->findOrFail($presensiId);

        $tenagaPendidik = $this->getTenagaPendidik();
        $kelas = $tenagaPendidik ? $this->getSelectedKelas($tenagaPendidik) : null;

        if (!$kelas || ($presensi->kelas_id != $kelas->id && optional($presensi->siswa)->kelas_id != $kelas->id)) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan izin ini.');
        }

        if ($request->status == 'setuju') {
            $keteranganBaru = $presensi->keterangan . ' - Divalidasi dan disetujui oleh wali kelas';
            if ($request->keterangan) {
                $keteranganBaru .= ' (Catatan: ' . $request->keterangan . ')';
            }

            $presensi->update([
                'keterangan' => $keteranganBaru,
                'status_validasi' => 'disetujui',
            ]);

            // Notify wali siswa about izin approval
            $presensi->load('siswa.orangTua');
            app(\App\Services\NotificationService::class)->notifyIzinStatus($presensi);

            return back()->with('success', 'Pengajuan izin disetujui!');
        } else {
            $keteranganBaru = 'Pengajuan izin ditolak oleh wali kelas';
            if ($request->keterangan) {
                $keteranganBaru .= '. Alasan: ' . $request->keterangan;
            }

            $presensi->update([
                'status' => 'alpha',
                'keterangan' => $keteranganBaru,
                'status_validasi' => 'ditolak',
            ]);

            // Notify wali siswa about izin rejection
            $presensi->load('siswa.orangTua');
            app(\App\Services\NotificationService::class)->notifyIzinStatus($presensi);

            return back()->with('success', 'Pengajuan izin ditolak, status diubah menjadi Alpha.');
        }
    }

    /**
     * Input presensi harian (bulk)
     */
    public function inputHarian(Request $request): RedirectResponse
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.siswa_id' => 'required|exists:siswa,id',
            'presensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        // IDOR guard: kelas harus diampu wali; siswa di luar kelas ini dilewati.
        $this->assertKelasMilikWali($request->kelas_id);
        $validSiswaIds = Siswa::where('kelas_id', $request->kelas_id)->pluck('id')->all();

        foreach ($request->presensi as $data) {
            if (!in_array((int) $data['siswa_id'], $validSiswaIds, true)) {
                continue; // lewati siswa yang bukan anggota kelas (cegah tampering lintas-kelas)
            }

            Presensi::updateOrCreate(
                [
                    'siswa_id' => $data['siswa_id'],
                    'kelas_id' => $request->kelas_id,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'status_validasi' => $this->statusValidasiForWaliInput($data['status']),
                    'diinput_oleh' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Presensi harian berhasil disimpan!');
    }

    /**
     * Display riwayat presensi for editing
     */
    public function riwayat(Request $request): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            abort(403, 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            abort(403, 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get siswa list for filter
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Query Presensi
        $query = Presensi::with('siswa')
            ->where('kelas_id', $kelas->id);

        // Filter Siswa
        if ($request->filled('siswa_id')) {
            $query->where('siswa_id', $request->siswa_id);
        }

        // Filter Tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }
        
        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $riwayat = $query->orderBy('tanggal', 'desc')->paginate(20)->withQueryString();

        return view('wali-kelas.presensi.riwayat', [
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'riwayat' => $riwayat,
        ]);
    }

    /**
     * Update specific presensi record from history
     */
    public function updateRiwayat(Request $request, $id): RedirectResponse
    {
        $presensi = Presensi::with('siswa')->findOrFail($id);

        // Security check: presensi harus berada di salah satu kelas yang diampu wali.
        // (Pakai kelasIdsWali agar tidak null-deref saat belum ada kelas terpilih.)
        $this->assertPresensiMilikWali($presensi);

        $validated = $request->validate([
            'status' => 'required|in:hadir,sakit,izin,alpha',
            'keterangan' => 'nullable|string|max:500',
            'status_validasi' => 'nullable|in:pending,disetujui,ditolak',
        ]);

        $statusToSave = $validated['status'];
        $statusValidasi = $validated['status_validasi'] ?? null;

        // Auto-adjust status based on validasi jika frontend terlewat
        if ($statusValidasi === 'ditolak') {
            $statusToSave = 'alpha';
        } elseif ($statusValidasi === 'disetujui' && $statusToSave === 'alpha') {
            $statusToSave = 'izin';
        }

        if ($statusValidasi === 'pending' && !in_array($statusToSave, ['sakit', 'izin'], true)) {
            $statusValidasi = null;
        }

        $presensi->update([
            'status' => $statusToSave,
            'keterangan' => $validated['keterangan'],
            'status_validasi' => $statusValidasi,
            'diinput_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Data presensi berhasil diperbarui.');
    }

    /**
     * Print rekap presensi
     */
    public function printRekap(Request $request)
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            abort(403, 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            abort(403, 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $semester = $request->get('semester');

        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $semesterPeriod = null;
        if ($semester && $tahunAjaran) {
            $periods = $tahunAjaran->getSemesterPeriods();
            $semesterPeriod = $periods[$semester] ?? null;
        }

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $rekapPresensi = [];
        foreach ($siswaList as $siswa) {
            $query = Presensi::where('siswa_id', $siswa->id)
                ->where('kelas_id', $kelas->id);

            if ($semesterPeriod) {
                $query->whereBetween('tanggal', [$semesterPeriod['start'], $semesterPeriod['end']]);
            } else {
                $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            }

            $counts = (clone $query)->selectRaw('
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
            ')->first();

            $rekapPresensi[$siswa->id] = [
                'siswa' => $siswa,
                'hadir' => $counts->hadir ?? 0,
                'sakit' => $counts->sakit ?? 0,
                'izin'  => $counts->izin ?? 0,
                'alpha' => $counts->alpha ?? 0,
            ];
        }

        return view('wali-kelas.presensi.print-rekap', [
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'rekapBulan' => $rekapPresensi,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'semester' => $semester,
        ]);
    }
    /**
     * Rekap Harian - list all dates that have presensi entries, with detail preview.
     */
    public function rekapHarian(Request $request): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();
        if (!$tenagaPendidik) abort(403, 'Data tenaga pendidik tidak ditemukan.');

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);
        if (!$kelas) abort(403, 'Anda belum ditugaskan sebagai wali kelas.');

        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $semester = $request->get('semester');

        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $semesterPeriod = null;
        if ($semester && $tahunAjaran) {
            $periods = $tahunAjaran->getSemesterPeriods();
            $semesterPeriod = $periods[$semester] ?? null;
        }

        // Get distinct dates that have presensi records for this class
        $query = Presensi::where('kelas_id', $kelas->id);

        if ($semesterPeriod) {
            $query->whereBetween('tanggal', [$semesterPeriod['start'], $semesterPeriod['end']]);
        } else {
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        }

        $dates = $query->selectRaw('DATE(tanggal) as tanggal, COUNT(*) as total_siswa,
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha')
            ->groupByRaw('DATE(tanggal)')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('wali-kelas.presensi.rekap-harian', [
            'kelas' => $kelas,
            'dates' => $dates,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
        ]);
    }

    /**
     * Show/preview detail presensi for a specific date.
     */
    public function showHarian(Request $request): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();
        if (!$tenagaPendidik) abort(403);

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);
        if (!$kelas) abort(403);

        if (!$request->filled('tanggal')) {
            return redirect()
                ->route('wali.presensi.rekap-harian')
                ->with('error', 'Pilih tanggal presensi dari rekap harian terlebih dahulu.');
        }

        try {
            $tanggal = Carbon::parse($request->query('tanggal'))->toDateString();
        } catch (\Throwable $e) {
            return redirect()
                ->route('wali.presensi.rekap-harian')
                ->with('error', 'Format tanggal presensi tidak valid.');
        }

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $presensiData = Presensi::where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        $summary = [
            'hadir' => $presensiData->where('status', 'hadir')->count(),
            'sakit' => $presensiData->where('status', 'sakit')->count(),
            'izin'  => $presensiData->where('status', 'izin')->count(),
            'alpha' => $presensiData->where('status', 'alpha')->count(),
        ];

        return view('wali-kelas.presensi.show-harian', [
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'presensiData' => $presensiData,
            'tanggal' => $tanggal,
            'summary' => $summary,
        ]);
    }

    /**
     * Preview bukti file with inline disposition
     */
    public function previewBukti($id)
    {
        $presensi = Presensi::with('siswa')->findOrFail($id);

        // Otorisasi: hanya wali yang mengampu kelas siswa boleh melihat bukti izin
        // (dokumen pribadi/medis). Cegah wali membuka bukti siswa kelas lain via id.
        $this->assertPresensiMilikWali($presensi);

        if (!$presensi->bukti_file) {
            abort(404);
        }

        $path = storage_path('app/public/' . $presensi->bukti_file);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file($path);
    }

    /**
     * Download template Excel untuk import presensi
     */
    public function downloadTemplate(Request $request)
    {
        $tenagaPendidik = $this->getTenagaPendidik();
        if (!$tenagaPendidik) abort(403);

        $kelas = $this->getSelectedKelas($tenagaPendidik);
        if (!$kelas) abort(403, 'Kelas tidak ditemukan.');

        $tanggal = $request->get('tanggal', now()->toDateString());

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Build CSV (Excel-compatible) in memory
        $rows = [];
        // Header info rows
        $rows[] = ['TEMPLATE IMPORT PRESENSI'];
        $rows[] = ['Kelas', $kelas->nama_kelas];
        $rows[] = ['Tanggal', $tanggal];
        $rows[] = [''];
        // Column headers
        $rows[] = ['No', 'NIS', 'Nama Siswa', 'Status', 'Keterangan'];
        $rows[] = ['', '', '', '(hadir/sakit/izin/alpha)', '(opsional)'];

        foreach ($siswaList as $idx => $siswa) {
            $rows[] = [
                $idx + 1,
                $siswa->nis,
                $siswa->nama_lengkap,
                'hadir',   // default
                '',
            ];
        }

        // Stream as CSV (Excel opens it natively)
        $filename = 'template_presensi_' . $kelas->nama_kelas . '_' . $tanggal . '.csv';
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $filename);

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Import presensi dari file Excel / CSV
     */
    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
            'tanggal'    => 'required|date',
            'kelas_id'   => 'required|exists:kelas,id',
        ]);

        $tenagaPendidik = $this->getTenagaPendidik();
        if (!$tenagaPendidik) abort(403);

        $kelas = $this->getSelectedKelas($tenagaPendidik);
        if (!$kelas || $kelas->id != $request->kelas_id) {
            return back()->withErrors(['file_excel' => 'Kelas tidak valid.']);
        }

        // Build NIS → Siswa map for the class
        $siswaMap = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get()
            ->keyBy('nis');

        $validStatus = ['hadir', 'sakit', 'izin', 'alpha'];
        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        $file     = $request->file('file_excel');
        $ext      = strtolower($file->getClientOriginalExtension());

        // Parse file → array of rows
        $allRows = [];
        if (in_array($ext, ['csv', 'txt'])) {
            // UTF-8 BOM-safe CSV parsing
            $handle = fopen($file->getRealPath(), 'r');
            // Strip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }
            while (($row = fgetcsv($handle)) !== false) {
                $allRows[] = $row;
            }
            fclose($handle);
        } else {
            // xlsx/xls — use PhpSpreadsheet via maatwebsite/excel helper
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->toArray(null, true, true, false) as $row) {
                    $allRows[] = $row;
                }
            } catch (\Exception $e) {
                return back()->withErrors(['file_excel' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
            }
        }

        // Find data rows: skip until we find the header row (No | NIS | Nama Siswa | Status ...)
        $dataStarted = false;
        foreach ($allRows as $rowIdx => $row) {
            $row = array_map('trim', array_map('strval', $row));

            // Detect header row by NIS keyword
            if (!$dataStarted) {
                if (isset($row[1]) && strtolower($row[1]) === 'nis') {
                    $dataStarted = true;
                    // skip the next row (description row "(hadir/sakit/...)")
                    continue;
                }
                // Skip the description row directly after header
                if ($dataStarted) continue;
                continue;
            }

            // Skip "description" row that starts with empty No
            if (empty($row[0]) || !is_numeric($row[0])) continue;

            $nis         = $row[1] ?? '';
            $statusRaw   = strtolower($row[3] ?? 'hadir');
            $keterangan  = $row[4] ?? null;

            if (empty($nis)) { $skipped++; continue; }

            $siswa = $siswaMap->get($nis);
            if (!$siswa) {
                $skipped++;
                $errors[] = "NIS {$nis} tidak ditemukan di kelas ini.";
                continue;
            }

            if (!in_array($statusRaw, $validStatus)) {
                $skipped++;
                $errors[] = "NIS {$nis}: status '{$statusRaw}' tidak valid (hadir/sakit/izin/alpha).";
                continue;
            }

            Presensi::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelas->id,
                    'tanggal'  => $request->tanggal,
                ],
                [
                    'status'      => $statusRaw,
                    'keterangan'  => $keterangan ?: null,
                    'status_validasi' => $this->statusValidasiForWaliInput($statusRaw),
                    'diinput_oleh' => auth()->id(),
                ]
            );
            $imported++;
        }

        $msg = "Import selesai: {$imported} data berhasil diimpor.";
        if ($skipped) $msg .= " {$skipped} baris dilewati.";

        if (!empty($errors)) {
            return redirect()->route('wali.presensi.index', ['tanggal' => $request->tanggal])
                ->with('warning', $msg)
                ->with('import_errors', array_slice($errors, 0, 5));
        }

        return redirect()->route('wali.presensi.index', ['tanggal' => $request->tanggal])
            ->with('success', $msg);
    }
}
