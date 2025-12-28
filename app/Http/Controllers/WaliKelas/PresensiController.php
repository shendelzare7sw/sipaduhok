<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
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
    /**
     * Display presensi siswa
     */
    public function index(Request $request): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        if (!$kelas) {
            return view('wali-kelas.presensi.index')->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
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
    public function validasiIzin(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        if (!$kelas) {
            return view('wali-kelas.presensi.validasi-izin')->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        // Get pengajuan izin yang belum divalidasi
        // Cek apakah diinput oleh orang tua (role orang_tua) dan belum divalidasi wali kelas
        $pengajuanIzin = Presensi::where('kelas_id', $kelas->id)
            ->whereIn('status', ['sakit', 'izin'])
            ->where(function($query) {
                // Izin yang diajukan oleh orang tua (ada keterangan "Diajukan oleh orang tua")
                // Dan BELUM divalidasi (tidak ada kata "Divalidasi" di keterangan)
                $query->where('keterangan', 'LIKE', '%Diajukan oleh orang tua%')
                      ->where('keterangan', 'NOT LIKE', '%Divalidasi%')
                      ->whereNotNull('diinput_oleh');
            })
            ->with(['siswa', 'inputBy'])
            ->orderBy('tanggal', 'desc')
            ->get()
            ->filter(function($presensi) {
                // Filter: hanya tampilkan yang diinput oleh orang tua (role orang_tua)
                return $presensi->inputBy && $presensi->inputBy->roleRelation &&
                       $presensi->inputBy->roleRelation->name === 'orang_tua';
            });

        return view('wali-kelas.presensi.validasi-izin', [
            'kelas' => $kelas,
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
            // Setujui izin - tambahkan keterangan validasi wali kelas
            $keteranganBaru = $presensi->keterangan . ' - Divalidasi dan disetujui oleh wali kelas';
            if ($request->keterangan) {
                $keteranganBaru .= ' (Catatan: ' . $request->keterangan . ')';
            }

            $presensi->update([
                'keterangan' => $keteranganBaru,
            ]);

            return back()->with('success', 'Pengajuan izin disetujui!');
        } else {
            // Tolak izin -> ubah jadi Alpha
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
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

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