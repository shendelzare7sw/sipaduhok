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
use App\Models\CatatanMonitoring;
use App\Models\TahunAjaran;
use App\Models\Cabang;
use App\Services\LmsMonitoringService;
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
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $taId = $request->integer('tahun_ajaran_id') ?: ($tahunAjaranAktif?->id);
        $tahunAjaran = $taId ? TahunAjaran::find($taId) : $tahunAjaranAktif;
        $isTaAktif = $tahunAjaran && $tahunAjaranAktif && $tahunAjaran->id === $tahunAjaranAktif->id;

        if (!$isTaAktif && $tahunAjaran) {
            // Mode HISTORIS: snapshot
            $snapshots = \App\Models\StatusNaikKelasSiswa::where('tahun_ajaran_id', $tahunAjaran->id)
                ->with(['siswa.cabang', 'siswa.kelas', 'originalKelas.cabang'])
                ->get();

            $snapshots = $snapshots->filter(function ($s) use ($request) {
                $siswa = $s->siswa;
                if (!$siswa) return false;
                if ($request->filled('cabang_id') && (int) $siswa->cabang_id !== (int) $request->cabang_id) return false;
                if ($request->filled('kelas_id') && (int) $s->original_kelas_id !== (int) $request->kelas_id) return false;
                if ($request->filled('jenjang')) {
                    if (($s->originalKelas?->jenjang) !== $request->jenjang) return false;
                }
                return true;
            });

            $siswaList = $snapshots->map(function ($s) {
                $siswa = $s->siswa;
                $siswa->kelas_snapshot_nama = $s->kelas_asal;
                $siswa->kelas_snapshot = $s->originalKelas;
                $siswa->status_kelulusan_snapshot = $s->status_kelulusan;
                return $siswa;
            });

            $sortBy = $request->sort_by ?? 'nama';
            $siswaList = match ($sortBy) {
                'kelas' => $siswaList->sortBy(fn($s) => ($s->kelas_snapshot?->jenjang ?? '') . '-' . ($s->kelas_snapshot_nama ?? '')),
                'cabang' => $siswaList->sortBy(fn($s) => $s->cabang_id . '-' . $s->nama_lengkap),
                default => $siswaList->sortBy('nama_lengkap'),
            };
            $siswaList = $siswaList->values();
        } else {
            $query = Siswa::with(['cabang', 'kelas.tahunAjaran']);

            if ($request->filled('kelas_id'))
                $query->where('siswa.kelas_id', $request->kelas_id);
            if ($request->filled('cabang_id'))
                $query->where('siswa.cabang_id', $request->cabang_id);
            if ($request->filled('jenjang'))
                $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));

            $query->where('siswa.status', $request->status ?? 'aktif');

            $sortBy = $request->sort_by ?? 'nama';
            if ($sortBy == 'kelas') {
                $query->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                    ->orderBy('kelas.jenjang')
                    ->orderBy('kelas.nama_kelas')
                    ->orderBy('siswa.nama_lengkap')
                    ->select('siswa.*');
            } elseif ($sortBy == 'cabang') {
                $query->orderBy('siswa.cabang_id')->orderBy('siswa.nama_lengkap');
            } else {
                $query->orderBy('siswa.nama_lengkap');
            }

            $siswaList = $query->get();
        }

        $kelas = $request->kelas_id ? Kelas::find($request->kelas_id) : null;
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;
        $isHistorical = !$isTaAktif;

        return view('ketua.laporan.print-siswa', compact(
            'siswaList', 'kelas', 'cabang', 'sortBy', 'tahunAjaran', 'isHistorical'
        ));
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

    /**
     * Rekap Akademik per TA — snapshot dari status_naik_kelas_siswa.
     */
    public function rekapAkademik(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $taId = $request->integer('tahun_ajaran_id') ?: ($tahunAjaranAktif?->id);
        $tahunAjaran = $taId ? TahunAjaran::find($taId) : null;

        if (!$tahunAjaran) {
            return redirect()->route('ketua.laporan.index')
                ->with('error', 'Tahun ajaran tidak valid.');
        }

        $snapshots = \App\Models\StatusNaikKelasSiswa::where('tahun_ajaran_id', $tahunAjaran->id)
            ->with(['siswa.cabang', 'originalKelas.cabang'])
            ->get();

        if ($request->filled('cabang_id')) {
            $snapshots = $snapshots->filter(fn($s) => (int) ($s->siswa?->cabang_id) === (int) $request->cabang_id);
        }

        $byStatus = $snapshots->groupBy('status_kelulusan');

        $stats = [
            'total' => $snapshots->count(),
            'naik' => ($byStatus->get('NAIK_KELAS')?->count() ?? 0) + ($byStatus->get('NAIK_KELAS_TUNGGAKAN')?->count() ?? 0),
            'tidak_naik' => $byStatus->get('TIDAK_NAIK_KELAS')?->count() ?? 0,
            'lulus' => ($byStatus->get('LULUS')?->count() ?? 0) + ($byStatus->get('LULUS_TUNGGAKAN')?->count() ?? 0),
            'dispensasi' => ($byStatus->get('NAIK_KELAS_TUNGGAKAN')?->count() ?? 0) + ($byStatus->get('LULUS_TUNGGAKAN')?->count() ?? 0),
        ];

        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;

        return view('ketua.laporan.print-rekap-akademik', compact('tahunAjaran', 'byStatus', 'stats', 'cabang'));
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

    // ============================================
    // MONITORING LMS (Materi / Tugas / Latihan / Ujian dari Guru)
    // ============================================

    /**
     * Konteks per-role untuk view bersama monitoring-lms.
     * Override di MonitoringController (admin) dan WakilKepalaSekolahController (waka).
     */
    protected function lmsViewContext(): array
    {
        return [
            'rolePartial' => 'ketua.partials.sneat-sidebar-menu',
            'baseRoute' => 'ketua.monitoring.lms',
            'cabangScope' => null,
        ];
    }

    public function lmsIndex(Request $request)
    {
        $ctx = $this->lmsViewContext();
        $service = app(LmsMonitoringService::class);

        $taFilter = $request->has('tahun_ajaran_id') ? (int) $request->input('tahun_ajaran_id') : 0;
        $onlyWithContent = $request->boolean('only_with_content');

        $kelas = $service->getKelasList(
            $ctx['cabangScope'],
            $request->input('search'),
            $taFilter,
            $onlyWithContent
        );

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $tahunAjarans = TahunAjaran::orderByDesc('tanggal_mulai')->get();

        return view('monitoring-lms.index', array_merge($ctx, compact('kelas', 'tahunAjaranAktif', 'tahunAjarans', 'taFilter', 'onlyWithContent')));
    }

    public function lmsKelas(Request $request, $kelasId)
    {
        $ctx = $this->lmsViewContext();
        $service = app(LmsMonitoringService::class);

        $kelas = $service->findKelasOrFail((int) $kelasId, $ctx['cabangScope']);

        $filters = [
            'mapel_id' => $request->input('mapel_id') ? (int) $request->input('mapel_id') : null,
            'search' => $request->input('search'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'tab' => $request->input('tab', 'materi'),
        ];

        $konten = $service->getKontenByKelas(
            (int) $kelasId,
            $filters['mapel_id'],
            $filters['search'],
            $filters['date_from'],
            $filters['date_to']
        );

        return view('monitoring-lms.kelas-detail', array_merge($ctx, compact('kelas', 'konten', 'filters')));
    }

    public function lmsPreview(Request $request, $type, $id)
    {
        $ctx = $this->lmsViewContext();
        $service = app(LmsMonitoringService::class);

        $previewView = match ($type) {
            CatatanMonitoring::KONTEN_MATERI => 'monitoring-lms.preview.materi',
            CatatanMonitoring::KONTEN_TUGAS => 'monitoring-lms.preview.tugas',
            CatatanMonitoring::KONTEN_UJIAN, 'latihan' => 'monitoring-lms.preview.ujian',
            default => abort(404),
        };

        $konten = match ($type) {
            CatatanMonitoring::KONTEN_MATERI => $service->findMateriOrFail((int) $id, $ctx['cabangScope']),
            CatatanMonitoring::KONTEN_TUGAS => $service->findTugasOrFail((int) $id, $ctx['cabangScope']),
            CatatanMonitoring::KONTEN_UJIAN, 'latihan' => $service->findUjianOrFail((int) $id, $ctx['cabangScope']),
        };

        return view($previewView, array_merge($ctx, [
            'konten' => $konten,
            'kontenType' => $type,
            'kontenId' => (int) $id,
        ]));
    }

    public function lmsKirimCatatan(Request $request)
    {
        $validated = $request->validate([
            'konten_type' => 'required|in:materi,tugas,ujian',
            'konten_id' => 'required|integer',
            'isi_catatan' => 'required|string|min:5|max:5000',
        ]);

        $ctx = $this->lmsViewContext();
        $service = app(LmsMonitoringService::class);

        $catatan = $service->kirimCatatan(
            auth()->user(),
            $validated['konten_type'],
            (int) $validated['konten_id'],
            $validated['isi_catatan'],
            $ctx['cabangScope']
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil dikirim ke guru.',
                'catatan_id' => $catatan->id,
            ]);
        }

        return redirect()->back()->with('success', 'Catatan berhasil dikirim ke guru.');
    }
}