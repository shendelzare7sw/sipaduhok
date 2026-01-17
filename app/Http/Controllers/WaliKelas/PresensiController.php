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
use Carbon\Carbon;

class PresensiController extends Controller
{
    use WaliKelasHelper;

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

        // Rekap presensi bulan ini
        $rekapBulan = [];
        foreach ($siswaList as $siswa) {
            $rekapBulan[$siswa->id] = [
                'hadir' => Presensi::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $kelas->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'hadir')
                    ->count(),
                'sakit' => Presensi::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $kelas->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'sakit')
                    ->count(),
                'izin' => Presensi::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $kelas->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'izin')
                    ->count(),
                'alpha' => Presensi::where('siswa_id', $siswa->id)
                    ->where('kelas_id', $kelas->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'alpha')
                    ->count(),
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

        Presensi::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'kelas_id' => $request->kelas_id,
                'tanggal' => $request->tanggal,
            ],
            [
                'status' => $request->status,
                'keterangan' => $request->keterangan,
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

        // Get pengajuan izin yang belum divalidasi
        // Query sederhana: cari presensi dengan keterangan "Diajukan oleh orang tua" yang belum divalidasi
        $pengajuanIzin = Presensi::where('kelas_id', $kelas->id)
            ->whereIn('status', ['sakit', 'izin'])
            ->where('keterangan', 'LIKE', '%Diajukan oleh orang tua%')
            ->where('keterangan', 'NOT LIKE', '%Divalidasi%')
            ->whereNotNull('diinput_oleh')
            ->whereHas('inputBy', function($query) {
                // Check via role field (string) atau roleRelation
                $query->where(function($q) {
                    $q->where('role', 'orang_tua')
                      ->orWhereHas('roleRelation', function($rq) {
                          $rq->where('name', 'orang_tua');
                      });
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

        $presensi = Presensi::findOrFail($presensiId);

        if ($request->status == 'setuju') {
            $keteranganBaru = $presensi->keterangan . ' - Divalidasi dan disetujui oleh wali kelas';
            if ($request->keterangan) {
                $keteranganBaru .= ' (Catatan: ' . $request->keterangan . ')';
            }

            $presensi->update([
                'keterangan' => $keteranganBaru,
            ]);

            return back()->with('success', 'Pengajuan izin disetujui!');
        } else {
            $keteranganBaru = 'Pengajuan izin ditolak oleh wali kelas';
            if ($request->keterangan) {
                $keteranganBaru .= '. Alasan: ' . $request->keterangan;
            }

            $presensi->update([
                'status' => 'alpha',
                'keterangan' => $keteranganBaru,
            ]);

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

        foreach ($request->presensi as $data) {
            Presensi::updateOrCreate(
                [
                    'siswa_id' => $data['siswa_id'],
                    'kelas_id' => $request->kelas_id,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'diinput_oleh' => auth()->id(),
                ]
            );
        }

        return back()->with('success', 'Presensi harian berhasil disimpan!');
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

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $rekapPresensi = [];
        foreach ($siswaList as $siswa) {
            $rekapPresensi[$siswa->id] = [
                'siswa' => $siswa,
                'hadir' => Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'hadir')
                    ->count(),
                'sakit' => Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'sakit')
                    ->count(),
                'izin' => Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'izin')
                    ->count(),
                'alpha' => Presensi::where('siswa_id', $siswa->id)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status', 'alpha')
                    ->count(),
            ];
        }

        return view('wali-kelas.presensi.print-rekap', [
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'rekapBulan' => $rekapPresensi,
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);
    }
}