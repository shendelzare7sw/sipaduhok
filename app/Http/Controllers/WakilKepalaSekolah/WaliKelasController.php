<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\TahunAjaran;
use App\Models\WaliKelasAssignment;
use Illuminate\Http\Request;

class WaliKelasController extends Controller
{
    private function getUserCabangId(): int
    {
        $cabangId = auth()->user()->cabang_id;

        if (!$cabangId) {
            abort(403, 'Akun Anda belum memiliki cabang yang ditetapkan. Hubungi administrator.');
        }

        return (int) $cabangId;
    }

    private function ensureKelasInUserCabang(Kelas $kelas): void
    {
        if ((int) $kelas->cabang_id !== $this->getUserCabangId()) {
            abort(403, 'Anda tidak berhak mengakses kelas dari cabang lain.');
        }
    }

    private function scopeActiveWaliKelasUser($query, int $userCabangId): void
    {
        $query->where('cabang_id', $userCabangId)
            ->where('is_active', true)
            ->where(function ($roleQuery) {
                $roleQuery->where('role', 'wali_kelas')
                    ->orWhereHas('roleRelation', fn($relationQuery) => $relationQuery->where('name', 'wali_kelas'));
            });
    }

    private function ensureWaliKelasInUserCabang(?int $waliKelasId): void
    {
        if (!$waliKelasId) {
            return;
        }

        $userCabangId = $this->getUserCabangId();

        $exists = TenagaPendidik::where('id', $waliKelasId)
            ->whereHas('user', function ($query) use ($userCabangId) {
                $this->scopeActiveWaliKelasUser($query, $userCabangId);
            })
            ->exists();

        if (!$exists) {
            abort(403, 'Wali kelas harus berasal dari cabang Anda.');
        }
    }

    private function getWaliKelasOptions(int $userCabangId)
    {
        return TenagaPendidik::with([
            'user.cabang',
            'waliKelasAssignments' => function ($query) use ($userCabangId) {
                $query->whereHas('kelas', fn($kelasQuery) => $kelasQuery->where('cabang_id', $userCabangId))
                    ->with(['kelas.cabang']);
            },
        ])
            ->whereHas('user', function ($query) use ($userCabangId) {
                $this->scopeActiveWaliKelasUser($query, $userCabangId);
            })
            ->orderBy('nama_lengkap')
            ->get();
    }

    public function index(Request $request)
    {
        $userCabangId = $this->getUserCabangId();

        $query = Kelas::with(['tahunAjaran', 'cabang', 'waliKelasAssignments.tenagaPendidik.user'])->withCount('siswa');

        // Filter by tahun ajaran
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('kode_kelas', 'like', "%{$search}%")
                    ->orWhereHas('waliKelasAssignments.tenagaPendidik', fn($wq) => $wq->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        // Filter by jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Mandatory filter by user's assigned cabang
        $query->where('cabang_id', $userCabangId);

        // Filter by status (assigned/unassigned)
        if ($request->filled('status')) {
            if ($request->status == 'assigned') {
                $query->whereHas('waliKelasAssignments');
            } elseif ($request->status == 'unassigned') {
                $query->whereDoesntHave('waliKelasAssignments');
            }
        }

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->paginate(15);

        // Data untuk filter dan assignment
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get current tahun ajaran
        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        // Available wali kelas options, scoped to this Waka cabang.
        $waliKelasOptions = $this->getWaliKelasOptions($userCabangId);

        // Statistics (filtered by user's cabang)
        $stats = [
            'totalKelas' => Kelas::where('cabang_id', $userCabangId)
                ->when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count(),
            'kelasWithWali' => Kelas::where('cabang_id', $userCabangId)
                ->when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereHas('waliKelasAssignments')->count(),
            'kelasWithoutWali' => Kelas::where('cabang_id', $userCabangId)
                ->when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereDoesntHave('waliKelasAssignments')->count(),
            'totalWaliKelas' => TenagaPendidik::whereHas('user', function ($query) use ($userCabangId) {
                $query->where('cabang_id', $userCabangId)
                    ->where('is_active', true)
                    ->where(function ($roleQuery) {
                        $roleQuery->whereIn('role', ['wali_kelas', 'guru_pengajar'])
                            ->orWhereHas('roleRelation', fn($relationQuery) => $relationQuery->whereIn('name', ['wali_kelas', 'guru_pengajar']));
                    });
            })->count(),
        ];

        return view('waka.wali-kelas.index', compact(
            'kelasList',
            'tahunAjarans',
            'jenjangs',
            'currentTahunAjaran',
            'waliKelasOptions',
            'stats'
        ));
    }

    public function assign(Request $request, $kelasId)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id'
        ]);

        $kelas = Kelas::findOrFail($kelasId);
        $this->ensureKelasInUserCabang($kelas);

        $waliKelasId = $validated['wali_kelas_id'] ?? null;
        $this->ensureWaliKelasInUserCabang($waliKelasId ? (int) $waliKelasId : null);

        if ($waliKelasId) {
            // Check if assignment already exists
            $exists = WaliKelasAssignment::where('kelas_id', $kelas->id)
                ->where('tenaga_pendidik_id', $waliKelasId)
                ->exists();

            if ($exists) {
                return back()
                    ->with('info', 'Wali kelas ini sudah ditugaskan ke kelas ' . $kelas->nama_kelas);
            }

            // Create new assignment (keeping existing assignments - multi-class support)
            WaliKelasAssignment::create([
                'tenaga_pendidik_id' => $waliKelasId,
                'kelas_id' => $kelas->id,
                'assigned_at' => now(),
            ]);

            // Update legacy field for backward compatibility
            $kelas->wali_kelas_id = $waliKelasId;
            $message = 'Wali kelas berhasil ditugaskan';
        } else {
            // Remove all wali kelas assignments
            WaliKelasAssignment::where('kelas_id', $kelas->id)->delete();
            $kelas->wali_kelas_id = null;
            $message = 'Wali kelas berhasil dihapus dari kelas';
        }

        $kelas->save();

        return back()->with('success', $message);
    }

    public function show(Kelas $kelas)
    {
        $this->ensureKelasInUserCabang($kelas);

        $userCabangId = $this->getUserCabangId();

        $kelas->load(['waliKelas.user', 'waliKelasAssignments.tenagaPendidik.user.cabang', 'siswa', 'tahunAjaran', 'cabang']);

        // Available wali kelas options, scoped to this Waka cabang.
        $waliKelasOptions = $this->getWaliKelasOptions($userCabangId);

        // Statistics for this class
        $stats = [
            'totalSiswa' => $kelas->siswa->count(),
            'siswaLaki' => $kelas->siswa->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => $kelas->siswa->where('jenis_kelamin', 'P')->count(),
        ];

        return view('waka.wali-kelas.show', compact('kelas', 'waliKelasOptions', 'stats'));
    }

    public function print(Request $request)
    {
        $userCabangId = $this->getUserCabangId();
        $query = Kelas::with(['tahunAjaran', 'cabang', 'waliKelasAssignments.tenagaPendidik'])->withCount('siswa');

        // Apply same filters
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } else {
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            }
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Mandatory filter by user's assigned cabang
        $query->where('cabang_id', $userCabangId);

        if ($request->filled('status')) {
            if ($request->status == 'assigned') {
                $query->whereHas('waliKelasAssignments');
            } elseif ($request->status == 'unassigned') {
                $query->whereDoesntHave('waliKelasAssignments');
            }
        }

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();

        $tahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $tahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        return view('waka.wali-kelas.print', compact('kelasList', 'tahunAjaran'));
    }
}
