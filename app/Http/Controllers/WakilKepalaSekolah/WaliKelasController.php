<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\TenagaPendidik;
use App\Models\TahunAjaran;
use App\Models\Cabang;
use Illuminate\Http\Request;

class WaliKelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(['tahunAjaran', 'cabang', 'waliKelas'])->withCount('siswa');

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
            $query->where(function($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                  ->orWhere('kode_kelas', 'like', "%{$search}%")
                  ->orWhereHas('waliKelas', fn($wq) => $wq->where('nama_lengkap', 'like', "%{$search}%"));
            });
        }

        // Filter by jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter by status (assigned/unassigned)
        if ($request->filled('status')) {
            if ($request->status == 'assigned') {
                $query->whereNotNull('wali_kelas_id');
            } elseif ($request->status == 'unassigned') {
                $query->whereNull('wali_kelas_id');
            }
        }

        $kelasList = $query->orderBy('jenjang')->orderBy('nama_kelas')->paginate(15);

        // Data untuk filter dan assignment
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get current tahun ajaran
        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        // Available wali kelas options
        $waliKelasOptions = TenagaPendidik::with(['user', 'kelasWali.cabang'])
            ->whereHas('user', fn($q) => $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();

        // Statistics
        $stats = [
            'totalKelas' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count(),
            'kelasWithWali' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereNotNull('wali_kelas_id')->count(),
            'kelasWithoutWali' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereNull('wali_kelas_id')->count(),
            'totalWaliKelas' => TenagaPendidik::whereHas('user', fn($q) => $q->whereIn('role', ['wali_kelas', 'guru_pengajar']))->count(),
        ];

        return view('waka.wali-kelas.index', compact(
            'kelasList',
            'tahunAjarans',
            'cabangs',
            'jenjangs',
            'currentTahunAjaran',
            'waliKelasOptions',
            'stats'
        ));
    }

    public function assign(Request $request, $kelasId)
    {
        $request->validate([
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id'
        ]);

        $kelas = Kelas::findOrFail($kelasId);

        // If wali_kelas_id is provided, check if this wali already assigned to other class
        if ($request->filled('wali_kelas_id')) {
            // Remove this wali from any other class first (satu wali hanya untuk satu kelas)
            Kelas::where('wali_kelas_id', $request->wali_kelas_id)
                ->where('id', '!=', $kelasId)
                ->update(['wali_kelas_id' => null]);

            $kelas->wali_kelas_id = $request->wali_kelas_id;
            $message = 'Wali kelas berhasil ditugaskan';
        } else {
            // Remove wali kelas
            $kelas->wali_kelas_id = null;
            $message = 'Wali kelas berhasil dihapus dari kelas';
        }

        $kelas->save();

        return redirect()->route('waka.wali-kelas.index')
            ->with('success', $message);
    }

    public function show(Kelas $kelas)
    {
        $kelas->load(['waliKelas', 'siswa', 'tahunAjaran', 'cabang']);

        // Available wali kelas options
        $waliKelasOptions = TenagaPendidik::with(['user', 'kelasWali.cabang'])
            ->whereHas('user', fn($q) => $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])->where('is_active', true))
            ->orderBy('nama_lengkap')
            ->get();

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
        $query = Kelas::with(['tahunAjaran', 'cabang', 'waliKelas'])->withCount('siswa');

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

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('status')) {
            if ($request->status == 'assigned') {
                $query->whereNotNull('wali_kelas_id');
            } elseif ($request->status == 'unassigned') {
                $query->whereNull('wali_kelas_id');
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
