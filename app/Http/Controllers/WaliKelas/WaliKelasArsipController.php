<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Nilai;
use App\Models\WaliKelasAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Arsip Wali Kelas — akses read-only ke data kelas yang PERNAH diwalikan (lintas TA).
 *
 * Setelah promosi & TA aktif berganti:
 * - assignment wali kelas bisa berubah (re-assign ke kelas TA aktif yang baru)
 * - tapi rapor / presensi / nilai TA lama tetap utuh di DB, terikat ke kelas_id + tahun_ajaran_id
 * - wali yang dulu pegang kelas itu kehilangan akses via menu reguler (yang scope ke TA aktif)
 *
 * Modul ini menyediakan akses historis (read-only) untuk:
 * - Daftar siswa kelas itu (snapshot)
 * - Rapor (semua status: draft / diterbitkan / revisi)
 * - Presensi historis
 * - Nilai per mata pelajaran
 */
class WaliKelasArsipController extends Controller
{
    use WaliKelasHelper;

    /**
     * List kelas yang PERNAH diwalikan (lintas TA) — group per TA.
     */
    public function index()
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return view('wali-kelas.arsip.index', [
                'tenagaPendidik' => null,
                'kelasGrouped' => collect(),
                'totalKelas' => 0,
            ]);
        }

        $kelasIds = WaliKelasAssignment::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->pluck('kelas_id')->unique()->values();

        $kelasList = Kelas::with(['cabang', 'tahunAjaran'])
            ->whereIn('id', $kelasIds)
            ->withCount(['siswa as siswa_count'])
            ->get();

        // Hitung counts per kelas (rapor & presensi & nilai) untuk preview
        $raporCounts = Rapor::whereIn('kelas_id', $kelasIds)
            ->selectRaw('kelas_id, count(*) as total, sum(case when status = "diterbitkan" then 1 else 0 end) as terbit')
            ->groupBy('kelas_id')->get()->keyBy('kelas_id');

        $kelasList->each(function ($k) use ($raporCounts) {
            $row = $raporCounts->get($k->id);
            $k->rapor_total = $row?->total ?? 0;
            $k->rapor_terbit = $row?->terbit ?? 0;
        });

        // Group by TA, sort TA terbaru dulu
        $kelasGrouped = $kelasList->sortByDesc(fn($k) => $k->tahunAjaran?->tanggal_mulai)
            ->groupBy(fn($k) => $k->tahunAjaran?->nama_tahun_ajaran ?? '-');

        return view('wali-kelas.arsip.index', [
            'tenagaPendidik' => $tenagaPendidik,
            'kelasGrouped' => $kelasGrouped,
            'totalKelas' => $kelasList->count(),
        ]);
    }

    /**
     * Detail kelas arsip — header + tabs (siswa/rapor/presensi/nilai).
     * Default tab: siswa.
     */
    public function show(Request $request, Kelas $kelas)
    {
        $tenagaPendidik = $this->guardAccess($kelas);
        if (!is_object($tenagaPendidik)) return $tenagaPendidik;

        $kelas->load(['cabang', 'tahunAjaran']);

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->orWhereHas('statusNaikKelas', function ($q) use ($kelas) {
                $q->where('original_kelas_id', $kelas->id);
            })
            ->with(['statusNaikKelas' => fn($q) => $q->where('original_kelas_id', $kelas->id)])
            ->orderBy('nama_lengkap')
            ->get();

        return view('wali-kelas.arsip.show', [
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'activeTab' => 'siswa',
        ] + $this->summaryStats($kelas));
    }

    /**
     * Tab Rapor: list semua rapor di kelas ini.
     */
    public function rapor(Kelas $kelas)
    {
        $check = $this->guardAccess($kelas);
        if (!is_object($check)) return $check;

        $kelas->load(['cabang', 'tahunAjaran']);

        $raporList = Rapor::where('kelas_id', $kelas->id)
            ->with(['siswa'])
            ->orderBy('siswa_id')
            ->orderBy('semester')
            ->get();

        return view('wali-kelas.arsip.show', [
            'kelas' => $kelas,
            'raporList' => $raporList,
            'activeTab' => 'rapor',
        ] + $this->summaryStats($kelas));
    }

    /**
     * Tab Presensi: rekap kehadiran historis.
     */
    public function presensi(Request $request, Kelas $kelas)
    {
        $check = $this->guardAccess($kelas);
        if (!is_object($check)) return $check;

        $kelas->load(['cabang', 'tahunAjaran']);

        $bulanFilter = $request->input('bulan'); // YYYY-MM (nullable)

        $presensiQuery = Presensi::where('kelas_id', $kelas->id);
        if ($bulanFilter && preg_match('/^\d{4}-\d{2}$/', $bulanFilter)) {
            $presensiQuery->whereYear('tanggal', substr($bulanFilter, 0, 4))
                ->whereMonth('tanggal', substr($bulanFilter, 5, 2));
        }

        // Rekap per siswa (lintas tanggal)
        $rekapPresensi = (clone $presensiQuery)
            ->select('siswa_id',
                DB::raw('SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit'),
                DB::raw('SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin'),
                DB::raw('SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('siswa_id')
            ->with('siswa')
            ->get()
            ->sortBy(fn($r) => $r->siswa->nama_lengkap ?? '');

        // List bulan tersedia untuk dropdown
        $bulanTersedia = Presensi::where('kelas_id', $kelas->id)
            ->select(DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as bulan"))
            ->groupBy('bulan')
            ->orderByDesc('bulan')
            ->pluck('bulan');

        return view('wali-kelas.arsip.show', [
            'kelas' => $kelas,
            'rekapPresensi' => $rekapPresensi,
            'bulanTersedia' => $bulanTersedia,
            'bulanFilter' => $bulanFilter,
            'activeTab' => 'presensi',
        ] + $this->summaryStats($kelas));
    }

    /**
     * Tab Nilai: ringkasan nilai akhir per mata pelajaran.
     */
    public function nilai(Request $request, Kelas $kelas)
    {
        $check = $this->guardAccess($kelas);
        if (!is_object($check)) return $check;

        $kelas->load(['cabang', 'tahunAjaran']);

        $semesterFilter = $request->input('semester'); // nullable

        $nilaiQuery = Nilai::where('kelas_id', $kelas->id);
        if ($semesterFilter) {
            $nilaiQuery->where('semester', $semesterFilter);
        }

        // Pivot: per siswa per mapel, ambil nilai_akhir
        $rows = (clone $nilaiQuery)
            ->with(['siswa', 'mataPelajaran'])
            ->get()
            ->groupBy('siswa_id');

        $semesterTersedia = Nilai::where('kelas_id', $kelas->id)
            ->select('semester')->distinct()->orderBy('semester')->pluck('semester');

        return view('wali-kelas.arsip.show', [
            'kelas' => $kelas,
            'nilaiBySiswa' => $rows,
            'semesterTersedia' => $semesterTersedia,
            'semesterFilter' => $semesterFilter,
            'activeTab' => 'nilai',
        ] + $this->summaryStats($kelas));
    }

    /**
     * Validasi: wali yang sedang login HARUS pernah diassign ke kelas ini.
     * Return TenagaPendidik object kalau lolos, RedirectResponse kalau ditolak.
     */
    private function guardAccess(Kelas $kelas)
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return redirect()->route('wali.arsip.index')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        $assigned = WaliKelasAssignment::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('kelas_id', $kelas->id)
            ->exists();

        if (!$assigned) {
            return redirect()->route('wali.arsip.index')
                ->with('error', 'Anda tidak memiliki akses ke arsip kelas ini (tidak pernah diassign sebagai wali).');
        }

        return $tenagaPendidik;
    }

    /**
     * Hitung counter ringkas untuk header card (siswa/rapor/presensi/nilai count).
     */
    private function summaryStats(Kelas $kelas): array
    {
        return [
            'totalSiswa' => Siswa::where('kelas_id', $kelas->id)->count() +
                            Siswa::whereHas('statusNaikKelas', fn($q) => $q->where('original_kelas_id', $kelas->id))
                                ->where('kelas_id', '!=', $kelas->id)
                                ->count(),
            'totalRapor' => Rapor::where('kelas_id', $kelas->id)->count(),
            'totalRaporTerbit' => Rapor::where('kelas_id', $kelas->id)->where('status', 'diterbitkan')->count(),
            'totalPresensi' => Presensi::where('kelas_id', $kelas->id)->count(),
            'totalNilai' => Nilai::where('kelas_id', $kelas->id)->count(),
        ];
    }
}
