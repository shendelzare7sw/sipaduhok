<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\GuruPengajarKelas;
use App\Models\JadwalPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruPengajarController extends Controller
{
    /**
     * Pastikan waka punya cabang, lalu return cabang_id-nya.
     */
    private function getUserCabangId()
    {
        $cabangId = auth()->user()->cabang_id;
        if (!$cabangId) {
            abort(403, 'Akun Anda belum memiliki cabang yang ditetapkan. Hubungi administrator.');
        }
        return $cabangId;
    }

    /**
     * Display a listing of guru pengajar — hanya dari cabang waka.
     */
    public function index(Request $request)
    {
        $cabangId = $this->getUserCabangId();

        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        // Guru pengajar yang punya assignment di kelas dari cabang waka ini
        $query = TenagaPendidik::whereHas('user', function($q) {
            $q->where('role', 'guru_pengajar');
        })->with(['user', 'guruKelas' => function($q) use ($tahunAjaranId, $cabangId) {
            $q->whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
                ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId)));
            $q->with(['kelas.cabang', 'mataPelajaran']);
        }]);

        // Filter status
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->whereHas('user', fn($q) => $q->where('is_active', true));
            } else {
                $query->whereHas('user', fn($q) => $q->where('is_active', false));
            }
        } else {
            $query->whereHas('user', fn($q) => $q->where('is_active', true));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        // Hanya tampilkan guru yang punya penugasan di cabang waka (atau semua guru aktif jika ingin)
        $query->whereHas('guruKelas', function($q) use ($cabangId, $tahunAjaranId) {
            $q->whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
                ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId)));
        });

        $guruList = $query->orderBy('nama_lengkap')->paginate(15);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        // Statistics — hanya untuk cabang waka
        $totalGuru = TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true)->where('role', 'guru_pengajar'))
            ->whereHas('guruKelas', fn($q) => $q->whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
                ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId))))
            ->count();

        $guruWithAssignment = $totalGuru; // sudah difilter memiliki assignment

        $totalPenugasan = GuruPengajarKelas::whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
            ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId)))->count();

        $totalMataPelajaran = GuruPengajarKelas::whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
            ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId)))
            ->distinct('mata_pelajaran_id')->count('mata_pelajaran_id');

        $stats = compact('totalGuru', 'guruWithAssignment', 'totalPenugasan', 'totalMataPelajaran');

        return view('waka.guru-pengajar.index', compact(
            'guruList', 'tahunAjarans', 'currentTahunAjaran', 'stats'
        ));
    }

    /**
     * Show detail guru — penugasan difilter ke cabang waka.
     */
    public function show(Request $request, TenagaPendidik $guruPengajar)
    {
        $cabangId = $this->getUserCabangId();

        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $guruPengajar->load(['user', 'guruKelas' => function($q) use ($tahunAjaranId, $cabangId) {
            $q->whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
                ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId)));
            $q->with(['kelas.cabang', 'kelas.tahunAjaran', 'mataPelajaran']);
        }]);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        // Jadwal mengajar guru di kelas cabang waka
        $jadwalList = JadwalPelajaran::where('guru_id', $guruPengajar->id)
            ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId))
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $stats = [
            'totalKelas' => $guruPengajar->guruKelas->pluck('kelas_id')->unique()->count(),
            'totalMapel' => $guruPengajar->guruKelas->pluck('mata_pelajaran_id')->unique()->count(),
            'totalPenugasan' => $guruPengajar->guruKelas->count(),
        ];

        return view('waka.guru-pengajar.show', compact(
            'guruPengajar', 'tahunAjarans', 'currentTahunAjaran',
            'jadwalList', 'stats'
        ));
    }

    /**
     * Lihat guru pengajar pada kelas tertentu — hanya kelas dari cabang waka.
     */
    public function manageKelas(Request $request, Kelas $kelas)
    {
        $cabangId = $this->getUserCabangId();

        // Authorization: hanya boleh lihat kelas dari cabang waka
        if ($kelas->cabang_id != $cabangId) {
            abort(403, 'Anda tidak berhak mengakses kelas ini.');
        }

        $kelas->load(['cabang', 'tahunAjaran', 'guruPengajar.tenagaPendidik', 'guruPengajar.mataPelajaran']);

        $jadwalList = JadwalPelajaran::whereHas('kelas', fn($q) => $q->where('kelas.id', $kelas->id))
            ->with(['guru', 'mataPelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('waka.guru-pengajar.manage-kelas', compact('kelas', 'jadwalList'));
    }

    /**
     * Sinkronkan guru_pengajar_kelas dari jadwal_pelajaran — hanya untuk kelas cabang waka.
     */
    public function rebuildFromJadwal(Request $request)
    {
        $cabangId = $this->getUserCabangId();

        $tahunAjaranId = $request->tahun_ajaran_id;
        if (!$tahunAjaranId) {
            $tahunAjaranId = TahunAjaran::where('is_active', true)->first()?->id;
        }

        if (!$tahunAjaranId) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Hanya jadwal yang kelasnya ada di cabang waka
        $jadwalList = JadwalPelajaran::where('tahun_ajaran_id', $tahunAjaranId)
            ->whereNotNull('guru_id')
            ->whereHas('kelas', fn($q) => $q->where('cabang_id', $cabangId))
            ->with('kelas')
            ->get();

        $fromJadwal = collect();
        foreach ($jadwalList as $jadwal) {
            // Use pivot kelas if available, fallback to kelas_id for legacy records
            $baseKelas = $jadwal->kelas->isNotEmpty()
                ? $jadwal->kelas
                : \App\Models\Kelas::where('id', $jadwal->kelas_id)->get();

            foreach ($baseKelas->where('cabang_id', $cabangId) as $kelas) {
                $key = $jadwal->guru_id . '-' . $kelas->id . '-' . $jadwal->mata_pelajaran_id;
                if (!$fromJadwal->has($key)) {
                    $fromJadwal->put($key, [
                        'tenaga_pendidik_id' => $jadwal->guru_id,
                        'kelas_id' => $kelas->id,
                        'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                    ]);
                }
            }
        }

        DB::beginTransaction();
        try {
            // Hapus hanya entri untuk kelas di cabang waka ini
            $kelasIds = Kelas::where('tahun_ajaran_id', $tahunAjaranId)
                ->where('cabang_id', $cabangId)
                ->pluck('id');

            // Catat pasangan lama agar hanya penugasan BARU yang dinotifikasi (hindari spam).
            $existingKeys = GuruPengajarKelas::whereIn('kelas_id', $kelasIds)
                ->get(['tenaga_pendidik_id', 'kelas_id', 'mata_pelajaran_id'])
                ->map(fn($g) => $g->tenaga_pendidik_id . '-' . $g->kelas_id . '-' . $g->mata_pelajaran_id)
                ->flip();

            GuruPengajarKelas::whereIn('kelas_id', $kelasIds)->delete();

            // firstOrCreate agar tahan terhadap entri yang belum ikut terhapus,
            // misal karena kelas sudah pindah tahun ajaran.
            foreach ($fromJadwal as $entry) {
                GuruPengajarKelas::firstOrCreate($entry);
            }

            DB::commit();

            // Notif guru hanya untuk penugasan yang benar-benar baru.
            $newAssignments = $fromJadwal->reject(fn($e, $key) => $existingKeys->has($key))->values();
            if ($newAssignments->isNotEmpty()) {
                app(\App\Services\NotificationService::class)->notifyGuruPengajarAssignments($newAssignments);
            }

            return back()->with('success', "Berhasil menyinkronkan {$fromJadwal->count()} penugasan dari jadwal pelajaran.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyinkronkan: ' . $e->getMessage());
        }
    }

    /**
     * Print daftar guru pengajar — hanya cabang waka.
     */
    public function print(Request $request)
    {
        $cabangId = $this->getUserCabangId();

        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $guruList = TenagaPendidik::whereHas('user', fn($q) => $q->where('is_active', true)->where('role', 'guru_pengajar'))
            ->with(['user', 'guruKelas' => function($q) use ($tahunAjaranId, $cabangId) {
                $q->whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
                    ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId)));
                $q->with(['kelas', 'mataPelajaran']);
            }])
            ->whereHas('guruKelas', fn($q) => $q->whereHas('kelas', fn($k) => $k->where('cabang_id', $cabangId)
                ->when($tahunAjaranId, fn($k2) => $k2->where('tahun_ajaran_id', $tahunAjaranId))))
            ->orderBy('nama_lengkap')
            ->get();

        $tahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;
        $cabang = auth()->user()->cabang;

        return view('waka.guru-pengajar.print', compact('guruList', 'tahunAjaran', 'cabang'));
    }
}
