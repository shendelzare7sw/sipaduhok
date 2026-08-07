<?php

namespace App\Http\Controllers\Admin;

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
     * Display a listing of guru pengajar.
     */
    public function index(Request $request)
    {
        // Get current or selected tahun ajaran
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        // Get guru pengajar (tenaga pendidik dengan role guru_pengajar SAJA, tidak termasuk wali_kelas)
        $query = TenagaPendidik::whereHas('user', function($q) {
            $q->where('role', 'guru_pengajar');
        })->with(['user', 'guruKelas' => function($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
            }
            $q->with(['kelas.cabang', 'mataPelajaran']);
        }]);

        // Filter by status
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

        $guruList = $query->orderBy('nama_lengkap')->paginate(15);

        // Data untuk filter
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        // Statistics
        $totalGuru = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->count();

        $guruWithAssignment = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->whereHas('guruKelas', function($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
            }
        })->count();

        $totalPenugasan = GuruPengajarKelas::when($tahunAjaranId, function($q) use ($tahunAjaranId) {
            $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
        })->count();

        $totalMataPelajaran = MataPelajaran::where('is_active', true)->count();

        $stats = compact('totalGuru', 'guruWithAssignment', 'totalPenugasan', 'totalMataPelajaran');

        return view('admin.guru-pengajar.index', compact(
            'guruList', 'tahunAjarans', 'currentTahunAjaran', 'stats'
        ));
    }

    /**
     * Show detail of guru pengajar (read-only dashboard).
     */
    public function show(Request $request, TenagaPendidik $guruPengajar)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $guruPengajar->load(['user', 'guruKelas' => function($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
            }
            $q->with(['kelas.cabang', 'kelas.tahunAjaran', 'mataPelajaran']);
        }]);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        // Get jadwal terkait guru ini untuk info tambahan
        $jadwalList = JadwalPelajaran::where('guru_id', $guruPengajar->id)
            ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Statistics
        $stats = [
            'totalKelas' => $guruPengajar->guruKelas->pluck('kelas_id')->unique()->count(),
            'totalMapel' => $guruPengajar->guruKelas->pluck('mata_pelajaran_id')->unique()->count(),
            'totalPenugasan' => $guruPengajar->guruKelas->count(),
        ];

        return view('admin.guru-pengajar.show', compact(
            'guruPengajar', 'tahunAjarans', 'currentTahunAjaran',
            'jadwalList', 'stats'
        ));
    }

    /**
     * Manage assignments for a specific class (read-only dashboard).
     */
    public function manageKelas(Request $request, Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'guruPengajar.tenagaPendidik', 'guruPengajar.mataPelajaran']);

        // Get jadwal terkait kelas ini
        $jadwalList = JadwalPelajaran::whereHas('kelas', fn($q) => $q->where('kelas.id', $kelas->id))
            ->with(['guru', 'mataPelajaran'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.guru-pengajar.manage-kelas', compact('kelas', 'jadwalList'));
    }

    /**
     * Rebuild guru_pengajar_kelas from jadwal_pelajaran.
     * Ensures the derived table is in sync with the source of truth (jadwal).
     */
    public function rebuildFromJadwal(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        if (!$tahunAjaranId) {
            $tahunAjaranId = TahunAjaran::where('is_active', true)->first()?->id;
        }

        if (!$tahunAjaranId) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Get all jadwal with guru for this tahun ajaran
        $jadwalList = JadwalPelajaran::where('tahun_ajaran_id', $tahunAjaranId)
            ->whereNotNull('guru_id')
            ->with('kelas')
            ->get();

        // Collect unique guru-kelas-mapel combinations
        $fromJadwal = collect();
        foreach ($jadwalList as $jadwal) {
            // Use pivot kelas if available, fallback to kelas_id for legacy records
            $kelasList = $jadwal->kelas->isNotEmpty()
                ? $jadwal->kelas
                : \App\Models\Kelas::where('id', $jadwal->kelas_id)->get();

            foreach ($kelasList as $kelas) {
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
            // Delete all existing entries for classes in this tahun ajaran
            $kelasIds = Kelas::where('tahun_ajaran_id', $tahunAjaranId)->pluck('id');

            // Catat pasangan lama agar hanya penugasan BARU yang dinotifikasi (hindari spam).
            $existingKeys = GuruPengajarKelas::whereIn('kelas_id', $kelasIds)
                ->get(['tenaga_pendidik_id', 'kelas_id', 'mata_pelajaran_id'])
                ->map(fn($g) => $g->tenaga_pendidik_id . '-' . $g->kelas_id . '-' . $g->mata_pelajaran_id)
                ->flip();

            GuruPengajarKelas::whereIn('kelas_id', $kelasIds)->delete();

            // Re-create from jadwal (firstOrCreate agar tahan terhadap entri yang
            // belum ikut terhapus, misal karena kelas sudah pindah tahun ajaran).
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
     * Print daftar guru pengajar.
     */
    public function print(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $guruList = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->with(['user', 'guruKelas' => function($q) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
            }
            $q->with(['kelas', 'mataPelajaran']);
        }])->orderBy('nama_lengkap')->get();

        $tahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('admin.guru-pengajar.print', compact('guruList', 'tahunAjaran'));
    }
}
