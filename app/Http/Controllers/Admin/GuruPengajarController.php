<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\TahunAjaran;
use App\Models\Cabang;
use App\Models\MataPelajaran;
use App\Models\GuruPengajarKelas;
use Illuminate\Http\Request;

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
     * Show detail of guru pengajar.
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

        // Data untuk form assign
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        $kelasList = Kelas::with('cabang')
            ->when($tahunAjaranId, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $mataPelajaranList = MataPelajaran::where('is_active', true)
            ->orderBy('jenjang')
            ->orderBy('nama_mapel')
            ->get();

        // Statistics
        $stats = [
            'totalKelas' => $guruPengajar->guruKelas->pluck('kelas_id')->unique()->count(),
            'totalMapel' => $guruPengajar->guruKelas->pluck('mata_pelajaran_id')->unique()->count(),
            'totalPenugasan' => $guruPengajar->guruKelas->count(),
        ];

        return view('admin.guru-pengajar.show', compact(
            'guruPengajar', 'tahunAjarans', 'currentTahunAjaran', 
            'kelasList', 'mataPelajaranList', 'stats'
        ));
    }

    /**
     * Assign guru ke kelas dan mata pelajaran.
     */
    public function assign(Request $request, TenagaPendidik $guruPengajar)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        ]);

        // Check if already assigned (to ANY teacher)
        $existing = GuruPengajarKelas::where('kelas_id', $validated['kelas_id'])
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->with('tenagaPendidik') // Eager load to show who has it
            ->first();

        if ($existing) {
            $currentGuru = $existing->tenagaPendidik ? $existing->tenagaPendidik->nama_lengkap : 'Guru lain';
            return back()->with('error', "Gagal! Mata pelajaran ini sudah diajar oleh {$currentGuru} di kelas tersebut.");
        }

        GuruPengajarKelas::create([
            'tenaga_pendidik_id' => $guruPengajar->id,
            'kelas_id' => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
        ]);

        $kelas = Kelas::find($validated['kelas_id']);
        $mapel = MataPelajaran::find($validated['mata_pelajaran_id']);

        return back()->with('success', "Berhasil menugaskan {$guruPengajar->nama_lengkap} mengajar {$mapel->nama_mapel} di kelas {$kelas->nama_kelas}!");
    }

    /**
     * Remove guru assignment.
     */
    public function removeAssignment(Request $request, TenagaPendidik $guruPengajar)
    {
        $validated = $request->validate([
            'assignment_id' => 'required|exists:guru_pengajar_kelas,id',
        ]);

        $assignment = GuruPengajarKelas::find($validated['assignment_id']);
        
        if ($assignment && $assignment->tenaga_pendidik_id == $guruPengajar->id) {
            $assignment->delete();
            return back()->with('success', 'Penugasan berhasil dihapus!');
        }

        return back()->with('error', 'Penugasan tidak ditemukan!');
    }

    /**
     * Manage assignments for a specific class.
     */
    public function manageKelas(Request $request, Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'guruPengajar.tenagaPendidik', 'guruPengajar.mataPelajaran']);

        // Get available guru (hanya guru_pengajar, tidak termasuk wali_kelas)
        $guruList = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'guru_pengajar');
        })->orderBy('nama_lengkap')->get();

        // Get mata pelajaran sesuai jenjang kelas
        $mataPelajaranList = MataPelajaran::where('is_active', true)
            ->where('jenjang', $kelas->jenjang)
            ->orderBy('nama_mapel')
            ->get();

        return view('admin.guru-pengajar.manage-kelas', compact('kelas', 'guruList', 'mataPelajaranList'));
    }

    /**
     * Assign guru to kelas from kelas view.
     */
    public function assignToKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'tenaga_pendidik_id' => 'required|exists:tenaga_pendidik,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        ]);

        // Check if already assigned (to ANY teacher)
        $existing = GuruPengajarKelas::where('kelas_id', $kelas->id)
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->with('tenagaPendidik')
            ->first();

        if ($existing) {
             $currentGuru = $existing->tenagaPendidik ? $existing->tenagaPendidik->nama_lengkap : 'Guru lain';
            return back()->with('error', "Gagal! Mata pelajaran ini sudah diajar oleh {$currentGuru} di kelas ini.");
        }

        GuruPengajarKelas::create([
            'tenaga_pendidik_id' => $validated['tenaga_pendidik_id'],
            'kelas_id' => $kelas->id,
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
        ]);

        $guru = TenagaPendidik::find($validated['tenaga_pendidik_id']);
        $mapel = MataPelajaran::find($validated['mata_pelajaran_id']);

        return back()->with('success', "Berhasil menugaskan {$guru->nama_lengkap} mengajar {$mapel->nama_mapel}!");
    }

    /**
     * Remove assignment from kelas view.
     */
    public function removeFromKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'assignment_id' => 'required|exists:guru_pengajar_kelas,id',
        ]);

        $assignment = GuruPengajarKelas::find($validated['assignment_id']);
        
        if ($assignment && $assignment->kelas_id == $kelas->id) {
            $assignment->delete();
            return back()->with('success', 'Penugasan berhasil dihapus!');
        }

        return back()->with('error', 'Penugasan tidak ditemukan!');
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
