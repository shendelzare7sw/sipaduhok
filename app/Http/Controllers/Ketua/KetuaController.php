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
use App\Models\TahunAjaran;
use App\Models\Cabang;
use Illuminate\Http\Request;

class KetuaController extends Controller
{
    // ============================================
    // MONITORING & DASHBOARD
    // ============================================

    public function monitoringPengguna(Request $request)
    {
        // Query Tenaga Pendidik
        $queryTP = TenagaPendidik::with('user');

        if ($request->filled('search_tp')) {
            $queryTP->where('nama_lengkap', 'like', '%' . $request->search_tp . '%');
        }

        if ($request->filled('role_tp')) {
            $queryTP->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role_tp);
            });
        }

        if ($request->filled('status_tp')) {
            $isActive = $request->status_tp == 'aktif';
            $queryTP->whereHas('user', function ($q) use ($isActive) {
                $q->where('is_active', $isActive);
            });
        }

        $tenagaPendidik = $queryTP->paginate(10, ['*'], 'tp_page');

        // Query Siswa
        $querySiswa = Siswa::with(['user', 'kelas']);

        if ($request->filled('search_siswa')) {
            $querySiswa->where('nama_lengkap', 'like', '%' . $request->search_siswa . '%');
        }

        if ($request->filled('status_siswa')) {
            $isActive = $request->status_siswa == 'aktif';
            $querySiswa->where('status', $request->status_siswa);
        }

        $siswa = $querySiswa->paginate(10, ['*'], 'siswa_page');

        // Stats (Calculated once)
        $stats = [
            'totalTenagaPendidik' => TenagaPendidik::count(),
            'totalSiswa' => Siswa::count(),
            'totalUsers' => User::count(),
            'userAktif' => User::where('is_active', true)->count(),
        ];

        return view('ketua.monitoring.pengguna', compact('tenagaPendidik', 'siswa', 'stats'));
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
            $kelas = $tp->kelasWali->first(); // Assuming single wali kelas for now based on original code
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

        return view('ketua.monitoring.wali-kelas', compact('waliKelas', 'cabangs'));
    }

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

        return view('ketua.monitoring.guru-pengajar', compact('guruPengajar', 'cabangs'));
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

        return view('ketua.monitoring.siswa', compact('siswa', 'cabangs', 'kelasList'));
    }

    // ============================================
    // LAPORAN & CETAK
    // ============================================

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

        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))->count(),
            'totalCabang' => Cabang::where('is_active', true)->count(),
        ];

        return view('ketua.laporan.index', compact(
            'tahunAjarans',
            'tahunAjaranAktif',
            'cabangs',
            'kelasList',
            'stats'
        ));
    }

    public function siswa(Request $request)
    {
        $query = Siswa::with(['cabang', 'kelas.tahunAjaran']);

        if ($request->filled('kelas_id'))
            $query->where('kelas_id', $request->kelas_id);
        if ($request->filled('cabang_id'))
            $query->where('cabang_id', $request->cabang_id);
        if ($request->filled('jenjang'))
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));

        $query->where('status', $request->status ?? 'aktif');

        $sortBy = $request->sort_by ?? 'nama';
        if ($sortBy == 'kelas') {
            $query->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                ->orderBy('kelas.jenjang')
                ->orderBy('kelas.nama_kelas')
                ->orderBy('siswa.nama_lengkap')
                ->select('siswa.*');
        } elseif ($sortBy == 'cabang') {
            $query->orderBy('cabang_id')->orderBy('nama_lengkap');
        } else {
            $query->orderBy('nama_lengkap');
        }

        $siswaList = $query->get();
        $kelas = $request->kelas_id ? Kelas::find($request->kelas_id) : null;
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        return view('ketua.laporan.print-siswa', compact('siswaList', 'kelas', 'cabang', 'sortBy', 'tahunAjaran'));
    }

    public function tenagaPendidik(Request $request)
    {
        $query = TenagaPendidik::with('user');

        if ($request->filled('role'))
            $query->whereHas('user', fn($q) => $q->where('role', $request->role));

        $status = $request->status == 'aktif' ? true : ($request->status == 'nonaktif' ? false : true);
        $query->whereHas('user', fn($q) => $q->where('is_active', $status));

        $sortBy = $request->sort_by ?? 'nama';
        $query->orderBy($sortBy == 'nip' ? 'nip' : 'nama_lengkap');

        $guruList = $query->get();
        $roleFilter = $request->role;

        return view('ketua.laporan.print-guru', compact('guruList', 'roleFilter', 'sortBy'));
    }

    public function kelas(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])->withCount('siswa');

        if ($tahunAjaranId)
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        if ($request->filled('cabang_id'))
            $query->where('cabang_id', $request->cabang_id);
        if ($request->filled('jenjang'))
            $query->where('jenjang', $request->jenjang);

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;

        return view('ketua.laporan.print-kelas', compact('kelasList', 'tahunAjaran', 'cabang'));
    }

    public function waliKelas(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])
            ->withCount('siswa')
            ->whereNotNull('wali_kelas_id');

        if ($tahunAjaranId)
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        if ($request->filled('jenjang'))
            $query->where('jenjang', $request->jenjang);

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        return view('ketua.laporan.print-wali-kelas', compact('kelasList', 'tahunAjaran'));
    }

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

        return view('ketua.laporan.print-guru-pengajar', compact('guruList', 'tahunAjaran'));
    }

    public function rekap(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id ?? TahunAjaran::where('is_active', true)->first()?->id;
        $tahunAjaran = TahunAjaran::find($tahunAjaranId);

        $cabangs = Cabang::where('is_active', true)->get()->map(function ($cabang) use ($tahunAjaranId) {
            $cabang->total_siswa = Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->count();
            $cabang->total_kelas = Kelas::where('cabang_id', $cabang->id)
                ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
                ->count();
            $cabang->siswa_l = Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->where('jenis_kelamin', 'L')->count();
            $cabang->siswa_p = Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->where('jenis_kelamin', 'P')->count();
            return $cabang;
        });

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

        $summary = [
            'total_siswa' => Siswa::where('status', 'aktif')->count(),
            'total_guru' => TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'total_kelas' => Kelas::when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))->count(),
            'total_cabang' => Cabang::where('is_active', true)->count(),
            'siswa_l' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'siswa_p' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
        ];

        return view('ketua.laporan.print-rekap', compact('cabangs', 'jenjangStats', 'summary', 'tahunAjaran'));
    }

    // ============================================
    // CATATAN
    // ============================================

    public function catatanIndex()
    {
        $catatan = Catatan::with('pengirim')->where('pengirim_id', auth()->id())->latest()->paginate(15);
        return view('ketua.catatan.index', compact('catatan'));
    }

    public function catatanCreate()
    {
        $roles = [
            'admin' => 'Admin',
            'ketua_pkbm' => 'Ketua PKBM',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
            'siswa' => 'Siswa',
            'orang_tua' => 'Orang Tua',
        ];

        // Remove sender's own role (cannot send to self)
        unset($roles[auth()->user()->role]);

        // Load all users except the sender
        $users = \App\Models\User::whereIn('role', array_keys($roles))
            ->where('is_active', true)
            ->where('id', '!=', auth()->id())
            ->with([
                'cabang:id,nama_cabang',
                'siswa:id,user_id,kelas_id,nama_lengkap',
                'siswa.kelas:id,nama_kelas,cabang_id',
                'siswa.kelas.cabang:id,nama_cabang',
                'children:id,kelas_id',
                'children.kelas:id,cabang_id',
                'children.kelas.cabang:id,nama_cabang',
            ])
            ->orderBy('name')
            ->get(['id', 'name', 'role', 'cabang_id']);

        $usersForIndividu = $users->map(function ($u) use ($roles) {
            // Resolve cabang based on role
            $cabangId = $u->cabang_id;
            $cabangName = $u->cabang?->nama_cabang ?? '-';

            if (!$cabangId && $u->role === 'siswa' && $u->siswa && $u->siswa->kelas) {
                $cabangId = $u->siswa->kelas->cabang_id;
                $cabangName = $u->siswa->kelas->cabang->nama_cabang ?? '-';
            }

            if (!$cabangId && $u->role === 'orang_tua') {
                // For orang_tua, resolve via their children (eager loaded)
                $child = $u->children->first();
                if ($child && $child->kelas) {
                    $cabangId = $child->kelas->cabang_id;
                    $cabangName = $child->kelas->cabang->nama_cabang ?? '-';
                }
            }

            return [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->role,
                'role_label' => $roles[$u->role] ?? $u->role,
                'cabang_id' => $cabangId,
                'cabang_name' => $cabangName,
            ];
        });

        $cabangList = \App\Models\Cabang::orderBy('nama_cabang')->get(['id', 'nama_cabang']);

        return view('ketua.catatan.create', compact('roles', 'usersForIndividu', 'cabangList'));
    }

    public function catatanStore(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi_catatan' => 'required|string',
            'tipe_penerima' => 'required|in:semua,role,individu',
            'role_penerima' => 'required_if:tipe_penerima,role',
            'penerima_ids' => 'required_if:tipe_penerima,individu|array|min:1',
            'penerima_ids.*' => 'exists:users,id',
            'prioritas' => 'required|in:biasa,penting,mendesak',
        ]);

        $pengirimId = auth()->id();
        $tanggalKirim = now();
        $notificationService = app(\App\Services\NotificationService::class);

        if ($validated['tipe_penerima'] === 'individu') {
            // Create one catatan record per recipient
            foreach ($validated['penerima_ids'] as $penerimaId) {
                $catatan = Catatan::create([
                    'pengirim_id' => $pengirimId,
                    'judul' => $validated['judul'],
                    'isi_catatan' => $validated['isi_catatan'],
                    'tipe_penerima' => 'individu',
                    'penerima_id' => $penerimaId,
                    'prioritas' => $validated['prioritas'],
                    'tanggal_kirim' => $tanggalKirim,
                ]);
                $catatan->load('pengirim');
                $notificationService->notifyCatatan($catatan);
            }
        } else {
            $catatan = Catatan::create([
                'pengirim_id' => $pengirimId,
                'judul' => $validated['judul'],
                'isi_catatan' => $validated['isi_catatan'],
                'tipe_penerima' => $validated['tipe_penerima'],
                'role_penerima' => $validated['role_penerima'] ?? null,
                'prioritas' => $validated['prioritas'],
                'tanggal_kirim' => $tanggalKirim,
            ]);
            $catatan->load('pengirim');
            $notificationService->notifyCatatan($catatan);
        }

        return redirect()->route('ketua.catatan.index')->with('success', 'Catatan berhasil dikirim!');
    }

    public function catatanShow($id)
    {
        $catatan = Catatan::with(['pengirim', 'pembaca'])->findOrFail($id);
        return view('ketua.catatan.show', compact('catatan'));
    }

    public function catatanDestroy($id)
    {
        $catatan = Catatan::where('pengirim_id', auth()->id())->findOrFail($id);
        $catatan->delete();

        return redirect()->route('ketua.catatan.index')->with('success', 'Catatan berhasil dihapus dari riwayat.');
    }
}