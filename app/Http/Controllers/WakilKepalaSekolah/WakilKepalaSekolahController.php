<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenagaPendidik;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Cabang;
use App\Models\Catatan;
use App\Models\GuruPengajarKelas;
use App\Models\Rapor;
use App\Models\Nilai;
use App\Models\Ujian;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WakilKepalaSekolahController extends Controller
{
    // ============================================
    // DASHBOARD
    // ============================================

    public function dashboard()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'totalKelas' => Kelas::when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))->count(),
            'totalMapel' => MataPelajaran::count(),
            'kelasWithWali' => Kelas::when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
                ->whereNotNull('wali_kelas_id')
                ->count(),
            'kelasWithoutWali' => Kelas::when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
                ->whereNull('wali_kelas_id')
                ->count(),
        ];

        // Recent classes without wali kelas
        $kelasWithoutWali = Kelas::with('cabang')
            ->when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->whereNull('wali_kelas_id')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->take(5)
            ->get();

        // Recent students
        $recentSiswa = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('waka.dashboard', compact(
            'tahunAjaranAktif',
            'stats',
            'kelasWithoutWali',
            'recentSiswa'
        ));
    }

    // ============================================
    // TAHUN AJARAN
    // ============================================

    public function tahunAjaranIndex(Request $request)
    {
        $query = TahunAjaran::query();

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $tahunAjarans = $query->orderBy('tanggal_mulai', 'desc')->paginate(10);

        return view('waka.tahun-ajaran.index', compact('tahunAjarans'));
    }

    public function tahunAjaranToggleActive($id)
    {
        DB::beginTransaction();
        try {
            // Deactivate all
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);

            // Activate selected
            $tahunAjaran = TahunAjaran::findOrFail($id);
            $tahunAjaran->is_active = true;
            $tahunAjaran->save();

            DB::commit();
            return redirect()->route('waka.tahun-ajaran.index')
                ->with('success', 'Tahun Ajaran ' . $tahunAjaran->nama_tahun_ajaran . ' berhasil diaktifkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengaktifkan tahun ajaran');
        }
    }

    // ============================================
    // MATA PELAJARAN
    // ============================================

    public function mataPelajaranIndex(Request $request)
    {
        $query = MataPelajaran::query();

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        $mataPelajaran = $query->orderBy('nama_mapel')->paginate(15);
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('waka.mata-pelajaran.index', compact('mataPelajaran', 'jenjangs'));
    }

    // ============================================
    // KELAS
    // ============================================

    public function kelasIndex(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $query = Kelas::with(['tahunAjaran', 'cabang', 'waliKelas'])->withCount('siswa');

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } elseif ($tahunAjaranAktif) {
            $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        $kelas = $query->orderBy('jenjang')->orderBy('nama_kelas')->paginate(15);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('waka.kelas.index', compact('kelas', 'tahunAjarans', 'tahunAjaranAktif', 'cabangs', 'jenjangs'));
    }

    // ============================================
    // MANAJEMEN SISWA
    // ============================================

    public function manajemenSiswaIndex(Request $request)
    {
        $query = Siswa::with(['kelas.tahunAjaran', 'cabang', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $siswa = $query->orderBy('nama_lengkap')->paginate(15);

        $kelasList = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();
        $cabangList = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('waka.manajemen-siswa.index', compact('siswa', 'kelasList', 'cabangList', 'jenjangs'));
    }

    // ============================================
    // WALI KELAS
    // ============================================

    public function waliKelasIndex(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $query = Kelas::with(['tahunAjaran', 'cabang', 'waliKelas'])->withCount('siswa');

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } elseif ($tahunAjaranAktif) {
            $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('status_wali')) {
            if ($request->status_wali == 'ada') {
                $query->whereNotNull('wali_kelas_id');
            } else {
                $query->whereNull('wali_kelas_id');
            }
        }

        $kelas = $query->orderBy('jenjang')->orderBy('nama_kelas')->paginate(15);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Available wali kelas
        $availableWaliKelas = TenagaPendidik::with('user')
            ->whereHas('user', fn($q) => $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();

        return view('waka.wali-kelas.index', compact('kelas', 'tahunAjarans', 'tahunAjaranAktif', 'jenjangs', 'availableWaliKelas'));
    }

    public function waliKelasAssign(Request $request, $kelasId)
    {
        $request->validate([
            'wali_kelas_id' => 'required|exists:tenaga_pendidik,id'
        ]);

        $kelas = Kelas::findOrFail($kelasId);
        $kelas->wali_kelas_id = $request->wali_kelas_id;
        $kelas->save();

        return redirect()->route('waka.wali-kelas.index')
            ->with('success', 'Wali kelas berhasil ditugaskan');
    }

    public function waliKelasRemove($kelasId)
    {
        $kelas = Kelas::findOrFail($kelasId);
        $kelas->wali_kelas_id = null;
        $kelas->save();

        return redirect()->route('waka.wali-kelas.index')
            ->with('success', 'Wali kelas berhasil dihapus dari kelas');
    }

    // ============================================
    // JADWAL GURU PENGAJAR
    // ============================================

    public function guruPengajarIndex(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $query = GuruPengajarKelas::with(['tenagaPendidik', 'kelas.tahunAjaran', 'mataPelajaran']);

        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id));
        } elseif ($tahunAjaranAktif) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id));
        }

        if ($request->filled('guru_id')) {
            $query->where('tenaga_pendidik_id', $request->guru_id);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $jadwalGuru = $query->orderBy('kelas_id')->paginate(20);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $guruList = TenagaPendidik::with('user')
            ->whereHas('user', fn($q) => $q->whereIn('role', ['guru_pengajar', 'wali_kelas'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();
        $kelasList = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();

        return view('waka.guru-pengajar.index', compact('jadwalGuru', 'tahunAjarans', 'tahunAjaranAktif', 'guruList', 'kelasList'));
    }

    // ============================================
    // MONITORING TENAGA PENDIDIK
    // ============================================

    public function monitoringGuruPengajar(Request $request)
    {
        $query = TenagaPendidik::with(['guruKelas.kelas', 'guruKelas.mataPelajaran'])
            ->whereHas('guruKelas');

        // Search
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Filter Cabang (via Guru Kelas -> Kelas)
        if ($request->filled('cabang_id')) {
            $query->whereHas('guruKelas.kelas', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang_id);
            });
        }

        $guruPengajar = $query->paginate(15);

        $guruPengajar->getCollection()->transform(function ($tp) {
            $totalNilaiHarusDiisi = Nilai::where('guru_id', $tp->id)->whereNull('nilai_akhir')->count();
            $nilaiSudahDiisi = Nilai::where('guru_id', $tp->id)->whereNotNull('nilai_akhir')->count();
            $soalUjianDibuat = Ujian::where('guru_id', $tp->id)->count();

            $tp->total_nilai_harus_diisi = $totalNilaiHarusDiisi;
            $tp->nilai_sudah_diisi = $nilaiSudahDiisi;
            $tp->soal_ujian_dibuat = $soalUjianDibuat;
            $tp->progress_nilai = ($totalNilaiHarusDiisi + $nilaiSudahDiisi) > 0
                ? round(($nilaiSudahDiisi / ($totalNilaiHarusDiisi + $nilaiSudahDiisi)) * 100, 2)
                : 0;

            return $tp;
        });

        $cabangs = Cabang::all();

        return view('waka.monitoring.guru-pengajar', compact('guruPengajar', 'cabangs'));
    }

    public function monitoringWaliKelas(Request $request)
    {
        $query = TenagaPendidik::with(['kelasWali.siswa', 'kelasWali.tahunAjaran'])
            ->whereHas('kelasWali');

        // Search
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Filter Cabang (via Kelas Wali)
        if ($request->filled('cabang_id')) {
            $query->whereHas('kelasWali', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang_id);
            });
        }

        $waliKelas = $query->paginate(15);

        // Map data
        $waliKelas->getCollection()->transform(function ($tp) {
            $kelas = $tp->kelasWali->first();
            if ($kelas) {
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

        $cabangs = Cabang::all();

        return view('waka.monitoring.wali-kelas', compact('waliKelas', 'cabangs'));
    }

    public function monitoringSiswa(Request $request)
    {
        $query = Siswa::with(['kelas', 'tagihan', 'pembayaran', 'tugasSiswa'])
            ->where('status', 'aktif');

        // Search
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Filter Cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Get Pagination
        $siswa = $query->paginate(20);

        // Map data for display (only for current page)
        $siswa->getCollection()->transform(function ($s) {
            $totalTugas = $s->tugasSiswa()->count();
            $tugasSelesai = $s->tugasSiswa()->where('status', 'dinilai')->count();

            $totalTagihan = $s->tagihan()->sum('jumlah');
            $sisaTagihan = $s->tagihan()->where('status', '!=', 'sudah_bayar')->sum('jumlah');
            $totalBayar = $totalTagihan - $sisaTagihan;

            $s->total_tugas = $totalTugas;
            $s->tugas_selesai = $tugasSelesai;
            $s->progress_tugas = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 2) : 0;
            $s->total_tagihan = $totalTagihan;
            $s->total_bayar = $totalBayar;
            $s->sisa_tagihan = $sisaTagihan;
            $s->status_bayar = $sisaTagihan <= 0 ? 'lunas' : 'belum_lunas';

            return $s;
        });

        // Data for Filters
        $cabangs = Cabang::all();
        $kelasList = Kelas::query();
        if ($request->filled('cabang_id')) {
            $kelasList->where('cabang_id', $request->cabang_id);
        }
        $kelasList = $kelasList->orderBy('jenjang')->orderBy('nama_kelas')->get();

        return view('waka.monitoring.siswa', compact('siswa', 'cabangs', 'kelasList'));
    }

    // ============================================
    // CATATAN / TEGURAN
    // ============================================

    public function catatanIndex()
    {
        $catatan = Catatan::with(['pengirim', 'pembaca'])
            ->where('pengirim_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('waka.catatan.index', compact('catatan'));
    }

    public function catatanCreate()
    {
        $roles = [
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
            'siswa' => 'Siswa',
        ];

        $tenagaPendidik = TenagaPendidik::with('user')
            ->whereHas('user', fn($q) => $q->whereIn('role', ['wali_kelas', 'guru_pengajar']))
            ->get();

        $siswaList = Siswa::with('user')->where('status', 'aktif')->get();

        return view('waka.catatan.create', compact('roles', 'tenagaPendidik', 'siswaList'));
    }

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

        $catatan = Catatan::create($validated);

        // Send notifications to recipients
        $catatan->load('pengirim');
        app(\App\Services\NotificationService::class)->notifyCatatan($catatan);

        return redirect()->route('waka.catatan.index')->with('success', 'Catatan berhasil dikirim!');
    }

    public function catatanShow($id)
    {
        $catatan = Catatan::with(['pengirim', 'pembaca'])->findOrFail($id);
        return view('waka.catatan.show', compact('catatan'));
    }
}
