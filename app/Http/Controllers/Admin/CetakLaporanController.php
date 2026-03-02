<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\TahunAjaran;
use App\Models\GuruPengajarKelas;
use Illuminate\Http\Request;

class CetakLaporanController extends Controller
{
    /**
     * Display laporan index page.
     */
    public function index()
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $cabangs = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::with('cabang')
            ->when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        // Quick stats
        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))->count(),
            'totalCabang' => Cabang::where('is_active', true)->count(),
        ];

        return view('admin.cetak-laporan.index', compact(
            'tahunAjarans', 'tahunAjaranAktif', 'cabangs', 'kelasList', 'stats'
        ));
    }

    /**
     * Cetak Daftar Siswa.
     */
    public function siswa(Request $request)
    {
        $query = Siswa::with(['cabang', 'kelas.tahunAjaran']);

        // Filter
        if ($request->filled('kelas_id')) {
            $query->where('siswa.kelas_id', $request->kelas_id);
        }
        if ($request->filled('cabang_id')) {
            $query->where('siswa.cabang_id', $request->cabang_id);
        }
        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }
        if ($request->filled('status')) {
            $query->where('siswa.status', $request->status);
        } else {
            $query->where('siswa.status', 'aktif');
        }

        // Sort
        $sortBy = $request->sort_by ?? 'nama';
        switch ($sortBy) {
            case 'kelas':
                $query->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                      ->orderBy('kelas.jenjang')
                      ->orderBy('kelas.nama_kelas')
                      ->orderBy('siswa.nama_lengkap')
                      ->select('siswa.*');
                break;
            case 'cabang':
                $query->orderBy('siswa.cabang_id')->orderBy('siswa.nama_lengkap');
                break;
            default:
                $query->orderBy('siswa.nama_lengkap');
        }

        $siswaList = $query->get();
        $kelas = $request->kelas_id ? Kelas::find($request->kelas_id) : null;
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        return view('admin.cetak-laporan.print-siswa', compact('siswaList', 'kelas', 'cabang', 'sortBy', 'tahunAjaran'));
    }

    /**
     * Cetak Daftar Tenaga Pendidik.
     */
    public function tenagaPendidik(Request $request)
    {
        $query = TenagaPendidik::with('user');

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->whereHas('user', fn($q) => $q->where('is_active', $request->status == 'aktif'));
        } else {
            $query->whereHas('user', fn($q) => $q->where('is_active', true));
        }

        // Sort
        $sortBy = $request->sort_by ?? 'nama';
        if ($sortBy == 'nip') {
            $query->orderBy('nip');
        } else {
            $query->orderBy('nama_lengkap');
        }

        $guruList = $query->get();
        $roleFilter = $request->role;

        return view('admin.cetak-laporan.print-guru', compact('guruList', 'roleFilter', 'sortBy'));
    }

    /**
     * Cetak Daftar Kelas.
     */
    public function kelas(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])
            ->withCount('siswa');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;

        return view('admin.cetak-laporan.print-kelas', compact('kelasList', 'tahunAjaran', 'cabang'));
    }

    /**
     * Cetak Daftar Wali Kelas.
     */
    public function waliKelas(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])
            ->withCount('siswa')
            ->whereNotNull('wali_kelas_id');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;

        return view('admin.cetak-laporan.print-wali-kelas', compact('kelasList', 'tahunAjaran', 'cabang'));
    }

    /**
     * Cetak Daftar Guru Pengajar.
     */
    public function guruPengajar(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;

        $query = TenagaPendidik::with(['user', 'guruKelas.kelas', 'guruKelas.mataPelajaran'])
            ->whereHas('user', fn($q) => $q->whereIn('role', ['guru_pengajar', 'wali_kelas'])->where('is_active', true));

        if ($tahunAjaranId) {
            $query->whereHas('guruKelas.kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId));
        }

        $guruList = $query->orderBy('nama_lengkap')->get();
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        return view('admin.cetak-laporan.print-guru-pengajar', compact('guruList', 'tahunAjaran'));
    }

    /**
     * Cetak Rekap Statistik.
     */
    public function rekap(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        // Rekap per cabang
        $cabangs = Cabang::where('is_active', true)->get()->map(function($cabang) use ($tahunAjaranId) {
            $cabang->total_siswa = Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->count();
            $cabang->total_kelas = Kelas::where('cabang_id', $cabang->id)
                ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
                ->count();
            $cabang->siswa_l = Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
            $cabang->siswa_p = Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->where('jenis_kelamin', 'P')->count();
            return $cabang;
        });

        // Rekap per jenjang
        $jenjangStats = [];
        foreach (['PAUD', 'SD', 'SMP', 'SMA'] as $jenjang) {
            $kelasIds = Kelas::where('jenjang', $jenjang)
                ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
                ->pluck('id');
            
            $jenjangStats[$jenjang] = [
                'total_kelas' => $kelasIds->count(),
                'total_siswa' => Siswa::whereIn('kelas_id', $kelasIds)->where('status', 'aktif')->count(),
                'siswa_l' => Siswa::whereIn('kelas_id', $kelasIds)->where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
                'siswa_p' => Siswa::whereIn('kelas_id', $kelasIds)->where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
            ];
        }

        // Summary
        $summary = [
            'total_siswa' => Siswa::where('status', 'aktif')->count(),
            'total_guru' => TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'total_kelas' => Kelas::when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))->count(),
            'total_cabang' => Cabang::where('is_active', true)->count(),
            'siswa_l' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'siswa_p' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
        ];

        return view('admin.cetak-laporan.print-rekap', compact('cabangs', 'jenjangStats', 'summary', 'tahunAjaran'));
    }
}
