<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\TahunAjaran;
use App\Models\Cabang;
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

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas.user'])
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
                $query->whereNotNull('wali_kelas_id');
            } else {
                $query->whereNull('wali_kelas_id');
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%")
                  ->orWhereHas('waliKelas', function($wq) use ($search) {
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
        $jenjangs = ['PAUD', 'SD', 'SMP', 'SMA'];

        // Get tenaga pendidik yang bisa jadi wali kelas
        $waliKelasOptions = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)
              ->whereIn('role', ['wali_kelas', 'guru_pengajar', 'admin']);
        })->orderBy('nama_lengkap')->get();

        // Statistics
        $currentTahunAjaran = $tahunAjaranId ? TahunAjaran::find($tahunAjaranId) : $tahunAjaranAktif;
        
        $totalKelas = Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count();
        $kelasWithWali = Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
            ->whereNotNull('wali_kelas_id')->count();
        $kelasWithoutWali = $totalKelas - $kelasWithWali;
        $totalWaliKelas = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)->where('role', 'wali_kelas');
        })->count();

        $stats = compact('totalKelas', 'kelasWithWali', 'kelasWithoutWali', 'totalWaliKelas');

        return view('admin.wali-kelas.index', compact(
            'kelasList', 'tahunAjarans', 'cabangs', 'jenjangs', 
            'waliKelasOptions', 'currentTahunAjaran', 'stats'
        ));
    }

    /**
     * Assign wali kelas to a class.
     */
    public function assign(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
        ]);

        $oldWali = $kelas->waliKelas;
        $kelas->update(['wali_kelas_id' => $validated['wali_kelas_id']]);

        if ($validated['wali_kelas_id']) {
            $newWali = TenagaPendidik::find($validated['wali_kelas_id']);
            return back()->with('success', "Berhasil menunjuk {$newWali->nama_lengkap} sebagai Wali Kelas {$kelas->nama_kelas}!");
        } else {
            return back()->with('success', "Wali Kelas {$kelas->nama_kelas} berhasil dihapus!");
        }
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
            if ($kelas && isset($assignment['wali_kelas_id'])) {
                $kelas->update(['wali_kelas_id' => $assignment['wali_kelas_id'] ?: null]);
                $count++;
            }
        }

        return back()->with('success', "Berhasil memperbarui {$count} penugasan Wali Kelas!");
    }

    /**
     * Show detail of wali kelas assignment.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'waliKelas.user', 'siswa']);
        
        // Get siswa statistics
        $stats = [
            'totalSiswa' => $kelas->siswa->count(),
            'siswaLaki' => $kelas->siswa->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => $kelas->siswa->where('jenis_kelamin', 'P')->count(),
        ];

        // Get other wali kelas options for reassignment
        $waliKelasOptions = TenagaPendidik::whereHas('user', function($q) {
            $q->where('is_active', true)
              ->whereIn('role', ['wali_kelas', 'guru_pengajar', 'admin']);
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

        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])
            ->withCount('siswa')
            ->whereNotNull('wali_kelas_id');

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
