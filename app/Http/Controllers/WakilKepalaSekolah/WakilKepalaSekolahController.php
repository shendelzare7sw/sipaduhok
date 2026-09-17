<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Catatan;
use App\Models\CatatanMonitoring;
use App\Models\Kelas;
use App\Models\LmsMeeting;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Nilai;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Tugas;
use App\Models\TugasSiswa;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\User;
use App\Services\LmsMonitoringService;
use Illuminate\Http\Request;

class WakilKepalaSekolahController extends Controller
{
    // ============================================
    // DASHBOARD
    // ============================================

    public function dashboard()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $userCabangId = auth()->user()->cabang_id;

        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->count(),
            'totalGuru' => TenagaPendidik::whereHas('user', fn ($q) => $q->where('is_active', true)->where('cabang_id', $userCabangId))->count(),
            'totalKelas' => Kelas::where('cabang_id', $userCabangId)
                ->when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))->count(),
            'totalMapel' => MataPelajaran::count(),
            'kelasWithWali' => Kelas::where('cabang_id', $userCabangId)
                ->when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
                ->whereNotNull('wali_kelas_id')->count(),
            'kelasWithoutWali' => Kelas::where('cabang_id', $userCabangId)
                ->when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
                ->whereNull('wali_kelas_id')->count(),
        ];

        // Recent classes without wali kelas (user's cabang only)
        $kelasWithoutWali = Kelas::with('cabang')
            ->where('cabang_id', $userCabangId)
            ->when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->whereNull('wali_kelas_id')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->take(5)
            ->get();

        // Recent students (user's cabang only)
        $recentSiswa = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif')
            ->where('cabang_id', $userCabangId)
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
    // MONITORING TENAGA PENDIDIK
    // ============================================

    public function monitoringGuruPengajar(Request $request)
    {
        $userCabangId = auth()->user()->cabang_id;

        // Scope ke tahun ajaran (default TA aktif, bisa lihat TA lama lewat
        // ?tahun_ajaran_id=). Tanpa ini progres guru dihitung LINTAS tahun ajaran.
        $taFilterId = $request->tahun_ajaran_id ?: TahunAjaran::where('is_active', true)->value('id');
        $scopeKelas = function ($q) use ($userCabangId, $taFilterId) {
            $q->where('cabang_id', $userCabangId)
                ->when($taFilterId, fn ($k) => $k->where('tahun_ajaran_id', $taFilterId));
        };

        $query = TenagaPendidik::with([
            'guruKelas' => fn ($q) => $q->whereHas('kelas', $scopeKelas),
            'guruKelas.kelas',
            'guruKelas.mataPelajaran',
        ])->whereHas('guruKelas.kelas', $scopeKelas);

        // Search
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%'.$request->search.'%');
        }

        $guruPengajar = $query->paginate(15);

        $guruPengajar->getCollection()->transform(function ($tp) use ($userCabangId, $taFilterId) {
            $visibleAssignments = $tp->guruKelas
                ->filter(fn ($assignment) => (int) optional($assignment->kelas)->cabang_id === (int) $userCabangId)
                ->values();

            $kelasIds = $visibleAssignments->pluck('kelas_id')->filter()->unique()->values();

            // Tanpa penugasan di TA terpilih -> progres HARUS nol, bukan jatuh ke
            // "tanpa filter kelas" yang menarik data TA lama.
            $nilaiQuery = Nilai::where('guru_id', $tp->id)
                ->when($taFilterId, fn ($q) => $q->where('tahun_ajaran_id', $taFilterId))
                ->whereIn('kelas_id', $kelasIds->isNotEmpty() ? $kelasIds->all() : [0]);

            $totalNilaiHarusDiisi = (clone $nilaiQuery)->whereNull('nilai_akhir')->count();
            $nilaiSudahDiisi = (clone $nilaiQuery)->whereNotNull('nilai_akhir')->count();

            $lmsBase = fn ($query) => $query->where('guru_id', $tp->id)
                ->whereIn('kelas_id', $kelasIds->isNotEmpty() ? $kelasIds->all() : [0]);

            $materiDibuat = $lmsBase(Materi::query())->count();
            $tugasDibuat = $lmsBase(Tugas::query())->count();
            $ujianDibuat = $lmsBase(Ujian::query())->count();
            $meetingDibuat = $lmsBase(LmsMeeting::query())->count();
            $tugasPerluKoreksi = TugasSiswa::whereIn('status', ['dikerjakan', 'terlambat'])
                ->whereHas('tugas', function ($q) use ($tp, $kelasIds) {
                    $q->where('guru_id', $tp->id)
                        ->when($kelasIds->isNotEmpty(), fn ($query) => $query->whereIn('kelas_id', $kelasIds));
                })->count();

            $tp->visible_guru_kelas = $visibleAssignments;
            $tp->total_kelas_mapel = $visibleAssignments->count();
            $tp->total_nilai_harus_diisi = $totalNilaiHarusDiisi;
            $tp->nilai_sudah_diisi = $nilaiSudahDiisi;
            $tp->materi_dibuat = $materiDibuat;
            $tp->tugas_dibuat = $tugasDibuat;
            $tp->ujian_dibuat = $ujianDibuat;
            $tp->meeting_dibuat = $meetingDibuat;
            $tp->tugas_perlu_koreksi = $tugasPerluKoreksi;
            $tp->soal_ujian_dibuat = $ujianDibuat;
            $tp->progress_nilai = ($totalNilaiHarusDiisi + $nilaiSudahDiisi) > 0
                ? round(($nilaiSudahDiisi / ($totalNilaiHarusDiisi + $nilaiSudahDiisi)) * 100, 2)
                : 0;

            return $tp;
        });

        $tahunAjarans = TahunAjaran::orderByDesc('tanggal_mulai')->get();

        return view('admin.monitoring.guru-pengajar', compact('guruPengajar', 'tahunAjarans', 'taFilterId'));
    }

    public function monitoringWaliKelas(Request $request)
    {
        $userCabangId = auth()->user()->cabang_id;

        // Scope ke tahun ajaran: default TA aktif, tetap bisa melihat TA lama lewat
        // ?tahun_ajaran_id=. Tanpa ini daftar wali kelas menampilkan penugasan
        // tahun lalu yang sudah tidak berlaku.
        $taFilterId = $request->tahun_ajaran_id ?: TahunAjaran::where('is_active', true)->value('id');
        $scopeKelas = function ($q) use ($userCabangId, $taFilterId) {
            $q->where('cabang_id', $userCabangId)
                ->when($taFilterId, fn ($k) => $k->where('tahun_ajaran_id', $taFilterId));
        };

        $query = TenagaPendidik::with([
            'kelasWali' => $scopeKelas,
            'kelasWali.siswa',
            'kelasWali.tahunAjaran',
            'kelasWali.cabang',
            'kelasWaliMultiple' => $scopeKelas,
            'kelasWaliMultiple.siswa',
            'kelasWaliMultiple.tahunAjaran',
            'kelasWaliMultiple.cabang',
        ])->where(function ($q) use ($scopeKelas) {
            $q->whereHas('kelasWali', $scopeKelas)
                ->orWhereHas('kelasWaliMultiple', $scopeKelas);
        });

        // Search
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%'.$request->search.'%');
        }

        $waliKelas = $query->paginate(15);

        // Map data
        $waliKelas->getCollection()->transform(function ($tp) use ($userCabangId) {
            $kelasCollection = $tp->kelasWali
                ->merge($tp->kelasWaliMultiple)
                ->unique('id')
                ->filter(fn ($kelas) => (int) $kelas->cabang_id === (int) $userCabangId)
                ->values();

            $kelasIds = $kelasCollection->pluck('id')->filter()->values();
            $totalSiswa = $kelasCollection->sum(fn ($kelas) => $kelas->siswa->count());
            $raporSelesai = $kelasIds->isNotEmpty()
                ? Rapor::whereIn('kelas_id', $kelasIds)->where('status', 'diterbitkan')->count()
                : 0;

            $tp->kelas_info = $kelasCollection->first();
            $tp->kelas_collection = $kelasCollection;
            $tp->kelas_count = $kelasCollection->count();
            $tp->kelas_names = $kelasCollection->pluck('nama_kelas')->filter()->unique()->implode(', ');
            $tp->cabang_names = $kelasCollection->map(fn ($kelas) => optional($kelas->cabang)->nama_cabang)->filter()->unique()->implode(', ');
            $tp->tahun_ajaran_names = $kelasCollection->map(fn ($kelas) => optional($kelas->tahunAjaran)->nama_tahun_ajaran)->filter()->unique()->implode(', ');
            $tp->total_siswa = $totalSiswa;
            $tp->rapor_selesai = $raporSelesai;
            $tp->progress_rapor = $totalSiswa > 0 ? round(($raporSelesai / $totalSiswa) * 100, 2) : 0;

            return $tp;
        });

        $tahunAjarans = TahunAjaran::orderByDesc('tanggal_mulai')->get();

        return view('admin.monitoring.wali-kelas', compact('waliKelas', 'tahunAjarans', 'taFilterId'));
    }

    public function monitoringSiswa(Request $request)
    {
        $userCabangId = auth()->user()->cabang_id;

        $query = Siswa::with(['kelas', 'tagihan', 'pembayaran', 'tugasSiswa'])
            ->where('status', 'aktif')
            ->where('cabang_id', $userCabangId);

        // Search
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%'.$request->search.'%');
        }

        // Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Get Pagination
        $siswa = $query->paginate(20);

        // Map data for display (only for current page)
        $siswa->getCollection()->transform(function ($s) {
            $tugasKelas = $s->kelas_id ? Tugas::where('kelas_id', $s->kelas_id)->count() : 0;
            $tugasSiswaQuery = TugasSiswa::where('siswa_id', $s->id)
                ->when($s->kelas_id, function ($q) use ($s) {
                    $q->whereHas('tugas', fn ($query) => $query->where('kelas_id', $s->kelas_id));
                });

            $totalTugas = max($tugasKelas, (clone $tugasSiswaQuery)->count());
            $tugasDikumpulkan = (clone $tugasSiswaQuery)
                ->whereIn('status', ['dikerjakan', 'terlambat', 'dinilai'])
                ->count();
            $tugasSelesai = (clone $tugasSiswaQuery)->where('status', 'dinilai')->count();

            $totalUjian = $s->kelas_id ? Ujian::where('kelas_id', $s->kelas_id)->count() : 0;
            $ujianSelesai = UjianSiswa::where('siswa_id', $s->id)
                ->whereIn('status', ['selesai', 'dinilai'])
                ->when($s->kelas_id, function ($q) use ($s) {
                    $q->whereHas('ujian', fn ($query) => $query->where('kelas_id', $s->kelas_id));
                })
                ->count();

            $totalTagihan = $s->tagihan()->sum('jumlah');
            $sisaTagihan = $s->tagihan()->where('status', '!=', 'sudah_bayar')->sum('jumlah');
            $totalBayar = $totalTagihan - $sisaTagihan;

            $s->total_tugas = $totalTugas;
            $s->tugas_dikumpulkan = $tugasDikumpulkan;
            $s->tugas_selesai = $tugasSelesai;
            $s->progress_tugas = $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 2) : 0;
            $s->total_ujian = $totalUjian;
            $s->ujian_selesai = $ujianSelesai;
            $s->progress_ujian = $totalUjian > 0 ? round(($ujianSelesai / $totalUjian) * 100, 2) : 0;
            $s->total_tagihan = $totalTagihan;
            $s->total_bayar = $totalBayar;
            $s->sisa_tagihan = $sisaTagihan;
            $s->status_bayar = $sisaTagihan <= 0 ? 'lunas' : 'belum_lunas';

            return $s;
        });

        // Data for Filters (kelas only from user's cabang, TA aktif saja)
        // Tanpa scoping TA, pasca ganti tahun ajaran dropdown ini berisi kelas dari
        // semua TA sehingga nama kelas muncul dobel dan tidak bisa dibedakan.
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::where('cabang_id', $userCabangId)
            ->when($tahunAjaranAktif, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->orderBy('jenjang')->orderBy('nama_kelas')->get();

        return view('admin.monitoring.siswa', compact('siswa', 'kelasList'));
    }

    // ============================================
    // CATATAN / TEGURAN
    // ============================================

    public function catatanIndex()
    {
        $catatan = $this->catatanVisibleToCurrentUserQuery()
            ->with(['pengirim', 'penerima', 'pembaca'])
            ->orderByDesc('tanggal_kirim')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.catatan.index', compact('catatan'));
    }

    public function catatanCreate()
    {
        $userCabangId = auth()->user()->cabang_id;

        $roles = [
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
            'siswa' => 'Siswa',
        ];

        $tenagaPendidik = TenagaPendidik::with('user')
            ->whereHas('user', fn ($q) => $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])
                ->where('cabang_id', $userCabangId))
            ->get();

        $siswaList = Siswa::with('user')->where('status', 'aktif')
            ->where('cabang_id', $userCabangId)->get();

        return view('admin.catatan.create', [
            'roles' => $roles,
            'tenagaPendidik' => $tenagaPendidik,
            'siswaList' => $siswaList,
            'recipientMode' => 'single',
        ]);
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

        return redirect_to_previous('waka.catatan.index')->with('success', 'Catatan berhasil dikirim!');
    }

    public function catatanShow($id)
    {
        $catatan = $this->catatanVisibleToCurrentUserQuery()
            ->with(['pengirim', 'penerima', 'pembaca'])
            ->findOrFail($id);

        if ((int) $catatan->pengirim_id !== (int) auth()->id()) {
            $catatan->markAsRead(auth()->id());
        }

        return view('admin.catatan.show', compact('catatan'));
    }

    public function catatanDestroy($id)
    {
        $catatan = Catatan::where('pengirim_id', auth()->id())->findOrFail($id);
        $catatan->delete();

        return back()->with('success', 'Catatan berhasil dihapus dari riwayat.');
    }

    private function catatanVisibleToCurrentUserQuery()
    {
        $user = auth()->user();

        return Catatan::query()->where(function ($query) use ($user) {
            $query->where('pengirim_id', $user->id)
                ->orWhere('penerima_id', $user->id)
                ->orWhere(function ($roleQuery) use ($user) {
                    $roleQuery->where('tipe_penerima', 'role')
                        ->where('role_penerima', $user->role);
                })
                ->orWhere('tipe_penerima', 'semua');
        });
    }

    // ============================================
    // MONITORING LMS (cabang-scoped)
    // ============================================

    private function lmsViewContext(): array
    {
        return [
            'rolePartial' => 'waka.partials.sidebar',
            'baseRoute' => 'waka.monitoring.lms',
            'cabangScope' => (int) auth()->user()->cabang_id,
        ];
    }

    public function lmsIndex(Request $request)
    {
        $ctx = $this->lmsViewContext();
        $service = app(LmsMonitoringService::class);

        // Default ke TA AKTIF, bukan 0 (= semua tahun ajaran). Dengan default lama,
        // Monitoring LMS menampilkan kelas dari SELURUH TA sekaligus - terukur 90
        // kelas padahal tahun berjalan cuma punya 45, jadi angka pemantauan
        // menggelembung dua kali lipat. Memilih "Semua TA" tetap bisa lewat
        // ?tahun_ajaran_id=0 pada dropdown filter.
        $taFilter = $request->has('tahun_ajaran_id')
            ? (int) $request->input('tahun_ajaran_id')
            : (int) (TahunAjaran::where('is_active', true)->value('id') ?? 0);
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
