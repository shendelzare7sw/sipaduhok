<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\TahunAjaran;
use App\Models\Cabang;
use App\Models\WaliKelasAssignment;
use Illuminate\Http\Request;

class WaliKelasController extends Controller
{
    /**
     * Display a listing of wali kelas assignments.
     */
    public function index(Request $request)
    {
        // Get current or selected tahun ajaran
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelasAssignments.tenagaPendidik.user'])
            ->withCount('siswa');

        // Filter by tahun ajaran
        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        // Filter by jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter by status wali kelas
        if ($request->filled('status')) {
            if ($request->status == 'assigned') {
                $query->whereHas('waliKelasAssignments');
            } else {
                $query->whereDoesntHave('waliKelasAssignments');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%")
                  ->orWhereHas('waliKelasAssignments.tenagaPendidik', function($wq) use ($search) {
                      $wq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $kelasList = $query->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->paginate(15);

        // Data untuk filter
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get tenaga pendidik yang bisa jadi wali kelas (hanya role wali_kelas)
        // Load dengan informasi kelas yang sudah di-assign
        $waliKelasOptions = TenagaPendidik::whereHas('user.roleRelation', function($q) {
            $q->where('name', 'wali_kelas');
        })->whereHas('user', function($q) {
            $q->where('is_active', true);
        })->with(['waliKelasAssignments.kelas' => function($query) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $query->where('tahun_ajaran_id', $tahunAjaranId);
            }
            $query->with('cabang');
        }])->orderBy('nama_lengkap')->get();

        // Statistics
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;
        
        $totalKelas = Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count();
        $kelasWithWali = Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
            ->whereHas('waliKelasAssignments')->count();
        $kelasWithoutWali = $totalKelas - $kelasWithWali;
        $totalWaliKelas = TenagaPendidik::whereHas('user.roleRelation', function($q) {
            $q->where('name', 'wali_kelas');
        })->whereHas('user', function($q) {
            $q->where('is_active', true);
        })->count();

        $stats = compact('totalKelas', 'kelasWithWali', 'kelasWithoutWali', 'totalWaliKelas');

        return view('admin.wali-kelas.index', compact(
            'kelasList', 'tahunAjarans', 'cabangs', 'jenjangs', 
            'waliKelasOptions', 'currentTahunAjaran', 'stats'
        ));
    }

    /**
     * Assign wali kelas to a class.
     *
     * UPDATED: Satu wali kelas bisa mengajar BANYAK kelas (N:M relationship).
     * Assignment lama TIDAK dihapus otomatis.
     */
    public function assign(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
        ]);

        $waliKelasId = $request->input('wali_kelas_id', null);

        if ($waliKelasId) {
            // Check if assignment already exists
            $existingAssignment = WaliKelasAssignment::where('kelas_id', $kelas->id)
                ->where('tenaga_pendidik_id', $waliKelasId)
                ->first();

            if ($existingAssignment) {
                return back()->with('info', 'Wali kelas ini sudah ditugaskan ke kelas ' . $kelas->nama_kelas);
            }

            // Create new assignment (NOT replacing old ones)
            WaliKelasAssignment::create([
                'tenaga_pendidik_id' => $waliKelasId,
                'kelas_id' => $kelas->id,
                'assigned_at' => now(),
            ]);

            // Also update the legacy wali_kelas_id field for backward compatibility
            $kelas->update(['wali_kelas_id' => $waliKelasId]);

            $newWali = TenagaPendidik::find($waliKelasId);
            return back()->with('success', "Berhasil menambahkan {$newWali->nama_lengkap} sebagai Wali Kelas {$kelas->nama_kelas}!");
        } else {
            // Remove all wali kelas assignments for this kelas
            WaliKelasAssignment::where('kelas_id', $kelas->id)->delete();
            $kelas->update(['wali_kelas_id' => null]);
            return back()->with('success', "Semua Wali Kelas {$kelas->nama_kelas} berhasil dihapus!");
        }
    }

    /**
     * Remove a specific wali kelas assignment.
     */
    public function removeAssignment(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'required|exists:tenaga_pendidik,id',
        ]);

        WaliKelasAssignment::where('kelas_id', $kelas->id)
            ->where('tenaga_pendidik_id', $request->wali_kelas_id)
            ->delete();

        // Update legacy field - set to first remaining assignment or null
        $remainingAssignment = WaliKelasAssignment::where('kelas_id', $kelas->id)->first();
        $kelas->update(['wali_kelas_id' => $remainingAssignment?->tenaga_pendidik_id]);

        $wali = TenagaPendidik::find($request->wali_kelas_id);
        return back()->with('success', "{$wali->nama_lengkap} berhasil dihapus dari Wali Kelas {$kelas->nama_kelas}!");
    }

    /**
     * Bulk assign wali kelas.
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.kelas_id' => 'required|exists:kelas,id',
            'assignments.*.wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
        ]);

        $count = 0;
        foreach ($validated['assignments'] as $assignment) {
            $kelas = Kelas::find($assignment['kelas_id']);
            if ($kelas && isset($assignment['wali_kelas_id']) && $assignment['wali_kelas_id']) {
                // Check if assignment exists
                $exists = WaliKelasAssignment::where('kelas_id', $kelas->id)
                    ->where('tenaga_pendidik_id', $assignment['wali_kelas_id'])
                    ->exists();

                if (!$exists) {
                    WaliKelasAssignment::create([
                        'tenaga_pendidik_id' => $assignment['wali_kelas_id'],
                        'kelas_id' => $kelas->id,
                        'assigned_at' => now(),
                    ]);
                    $kelas->update(['wali_kelas_id' => $assignment['wali_kelas_id']]);
                    $count++;
                }
            }
        }

        return back()->with('success', "Berhasil menambahkan {$count} penugasan Wali Kelas!");
    }

    /**
     * Show detail of wali kelas assignment.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'waliKelasAssignments.tenagaPendidik.user', 'siswa']);
        
        // Get siswa statistics
        $stats = [
            'totalSiswa' => $kelas->siswa->count(),
            'siswaLaki' => $kelas->siswa->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => $kelas->siswa->where('jenis_kelamin', 'P')->count(),
        ];

        // Get other wali kelas options for reassignment (hanya role wali_kelas)
        $waliKelasOptions = TenagaPendidik::whereHas('user.roleRelation', function($q) {
            $q->where('name', 'wali_kelas');
        })->whereHas('user', function($q) {
            $q->where('is_active', true);
        })->orderBy('nama_lengkap')->get();

        return view('admin.wali-kelas.show', compact('kelas', 'stats', 'waliKelasOptions'));
    }

    /**
     * Print daftar wali kelas.
     */
    public function print(Request $request)
    {
        $tahunAjaranId = $request->tahun_ajaran_id;
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaranId && $tahunAjaranAktif) {
            $tahunAjaranId = $tahunAjaranAktif->id;
        }

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelasAssignments.tenagaPendidik'])
            ->withCount('siswa')
            ->whereHas('waliKelasAssignments');

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        $kelasList = $query->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $tahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;

        return view('admin.wali-kelas.print', compact('kelasList', 'tahunAjaran'));
    }
}
