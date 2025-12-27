<?php
// app/Http/Controllers/Ketua/KetuaController.php

namespace App\Http\Controllers\Ketua;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenagaPendidik;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Nilai;
use App\Models\Ujian;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Catatan;
use App\Models\TahunAjaran; // Pastikan Model ini di-import
use App\Models\Cabang;      // Pastikan Model ini di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KetuaController extends Controller
{
    /**
     * Monitoring Data Pengguna (FIXED)
     */
    public function monitoringPengguna()
    {
        // Ambil Tenaga Pendidik dengan user
        $tenagaPendidik = TenagaPendidik::with('user')->paginate(15);

        // Ambil Siswa dengan user
        $siswa = Siswa::with('user')->paginate(15);

        // Stats
        $stats = [
            'totalTenagaPendidik' => TenagaPendidik::count(),
            'totalSiswa' => Siswa::count(),
            'totalUsers' => User::count(),
            'userAktif' => User::where('is_active', true)->count(),
        ];

        return view('ketua.monitoring.pengguna', compact('tenagaPendidik', 'siswa', 'stats'));
    }

    /**
     * Monitoring Data Wali Kelas
     */
    public function monitoringWaliKelas()
    {
        $waliKelas = TenagaPendidik::with(['kelasWali.siswa', 'kelasWali.tahunAjaran'])
            ->whereHas('kelasWali')
            ->get()
            ->map(function($tp) {
                $kelas = $tp->kelasWali->first();
                if ($kelas) {
                    // Hitung progress rapor
                    $totalSiswa = $kelas->siswa->count();
                    $raporSelesai = Rapor::where('kelas_id', $kelas->id)
                        ->where('status', 'diterbitkan')
                        ->count();
                    
                    $tp->kelas_info = $kelas;
                    $tp->total_siswa = $totalSiswa;
                    $tp->rapor_selesai = $raporSelesai;
                    $tp->progress_rapor = $totalSiswa > 0 ? round(($raporSelesai / $totalSiswa) * 100, 2) : 0;
                }
                return $tp;
            });

        return view('ketua.monitoring.wali-kelas', compact('waliKelas'));
    }

    /**
     * Monitoring Data Guru Pengajar
     */
    public function monitoringGuruPengajar()
    {
        $guruPengajar = TenagaPendidik::with(['guruKelas.kelas', 'guruKelas.mataPelajaran'])
            ->whereHas('guruKelas')
            ->get()
            ->map(function($tp) {
                // Hitung progress nilai
                $totalNilaiHarusDiisi = Nilai::where('guru_id', $tp->id)
                    ->whereNull('nilai_akhir')
                    ->count();
                
                $nilaiSudahDiisi = Nilai::where('guru_id', $tp->id)
                    ->whereNotNull('nilai_akhir')
                    ->count();

                // Hitung soal ujian yang sudah dibuat
                $soalUjianDibuat = Ujian::where('guru_id', $tp->id)->count();

                $tp->total_nilai_harus_diisi = $totalNilaiHarusDiisi;
                $tp->nilai_sudah_diisi = $nilaiSudahDiisi;
                $tp->soal_ujian_dibuat = $soalUjianDibuat;
                $tp->progress_nilai = ($totalNilaiHarusDiisi + $nilaiSudahDiisi) > 0 
                    ? round(($nilaiSudahDiisi / ($totalNilaiHarusDiisi + $nilaiSudahDiisi)) * 100, 2) 
                    : 0;

                return $tp;
            });

        return view('ketua.monitoring.guru-pengajar', compact('guruPengajar'));
    }

    /**
     * Monitoring Data Siswa
     */
    public function monitoringSiswa()
    {
        $siswa = Siswa::with(['kelas', 'tagihan', 'pembayaran', 'tugasSiswa'])
            ->where('status', 'aktif')
            ->get()
            ->map(function($s) {
                // Hitung status tugas
                $totalTugas = $s->tugasSiswa()->count();
                $tugasSelesai = $s->tugasSiswa()->where('status', 'dinilai')->count();
                
                // Hitung status tagihan
                $totalTagihan = $s->tagihan()->sum('jumlah');
                $totalBayar = $s->pembayaran()
                    ->where('status_validasi', 'disetujui')
                    ->sum('jumlah_bayar');

                $s->total_tugas = $totalTugas;
                $s->tugas_selesai = $tugasSelesai;
                $s->progress_tugas = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 2) : 0;
                $s->total_tagihan = $totalTagihan;
                $s->total_bayar = $totalBayar;
                $s->sisa_tagihan = $totalTagihan - $totalBayar;
                $s->status_bayar = $s->sisa_tagihan <= 0 ? 'lunas' : 'belum_lunas';

                return $s;
            });

        return view('ketua.monitoring.siswa', compact('siswa'));
    }

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

        // Quick stats (Ini perbaikan untuk error "Undefined variable $stats")
        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))->count(),
            'totalCabang' => Cabang::where('is_active', true)->count(),
        ];

        // Ubah path view sesuai lokasi file kamu: views/ketua/laporan/index.blade.php
        return view('ketua.laporan.index', compact(
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
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }
        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'aktif');
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
                $query->orderBy('cabang_id')->orderBy('nama_lengkap');
                break;
            default:
                $query->orderBy('nama_lengkap');
        }

        $siswaList = $query->get();
        $kelas = $request->kelas_id ? Kelas::find($request->kelas_id) : null;
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        // Ubah path view
        return view('ketua.laporan.print-siswa', compact('siswaList', 'kelas', 'cabang', 'sortBy', 'tahunAjaran'));
    }

    /**
     * Cetak Daftar Tenaga Pendidik.
     */
    public function tenagaPendidik(Request $request)
    {
        $query = TenagaPendidik::with('user');

        if ($request->filled('role')) {
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));
        }

        if ($request->filled('status')) {
            $query->whereHas('user', fn($q) => $q->where('is_active', $request->status == 'aktif'));
        } else {
            $query->whereHas('user', fn($q) => $q->where('is_active', true));
        }

        $sortBy = $request->sort_by ?? 'nama';
        if ($sortBy == 'nip') {
            $query->orderBy('nip');
        } else {
            $query->orderBy('nama_lengkap');
        }

        $guruList = $query->get();
        $roleFilter = $request->role;

        // Ubah path view
        return view('ketua.laporan.print-guru', compact('guruList', 'roleFilter', 'sortBy'));
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

        // Ubah path view
        return view('ketua.laporan.print-kelas', compact('kelasList', 'tahunAjaran', 'cabang'));
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

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        // Ubah path view
        return view('ketua.laporan.print-wali-kelas', compact('kelasList', 'tahunAjaran'));
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

        // Ubah path view
        return view('ketua.laporan.print-guru-pengajar', compact('guruList', 'tahunAjaran'));
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

        // Ubah path view
        return view('ketua.laporan.print-rekap', compact('cabangs', 'jenjangStats', 'summary', 'tahunAjaran'));
    }

    // ... (Method Monitoring & Catatan Lainnya biarkan sesuai aslinya jika tidak ada perubahan view) ...
    public function monitoringPengguna()
    {
        $tenagaPendidik = TenagaPendidik::with('user')->paginate(15);
        $siswa = Siswa::with('user')->paginate(15);
        $stats = [
            'totalTenagaPendidik' => TenagaPendidik::count(),
            'totalSiswa' => Siswa::count(),
            'totalUsers' => User::count(),
            'userAktif' => User::where('is_active', true)->count(),
        ];
        return view('ketua.monitoring.pengguna', compact('tenagaPendidik', 'siswa', 'stats'));
    }

    /**
     * Halaman Index Catatan
     */
    public function catatanIndex()
    {
        $catatan = Catatan::with('pengirim')
            ->where('pengirim_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('ketua.catatan.index', compact('catatan'));
    }

    /**
     * Form Create Catatan
     */
    public function catatanCreate()
    {
        $roles = [
            'admin' => 'Admin',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
            'siswa' => 'Siswa',
        ];

        return view('ketua.catatan.create', compact('roles'));
    }

    /**
     * Store Catatan
     */
    public function catatanStore(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi_catatan' => 'required|string',
            'tipe_penerima' => 'required|in:semua,role,individu',
            'role_penerima' => 'required_if:tipe_penerima,role',
            'penerima_id' => 'required_if:tipe_penerima,individu|exists:users,id',
            'prioritas' => 'required|in:biasa,penting,mendesak',
        ]);

        $validated['pengirim_id'] = auth()->id();
        $validated['tanggal_kirim'] = now();

        Catatan::create($validated);

        return redirect()->route('ketua.catatan.index')
            ->with('success', 'Catatan berhasil dikirim!');
    }

    /**
     * Show Detail Catatan
     */
    public function catatanShow($id)
    {
        $catatan = Catatan::with(['pengirim', 'pembaca'])->findOrFail($id);

        return view('ketua.catatan.show', compact('catatan'));
    }
}