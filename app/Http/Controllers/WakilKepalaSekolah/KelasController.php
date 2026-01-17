<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Siswa;
use App\Models\WaliKelasAssignment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Templates\KelasTemplate;
use App\Imports\KelasImport;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelasAssignments.tenagaPendidik']);

        // Filter by tahun ajaran
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        } else {
            // Default: tampilkan tahun ajaran aktif
            $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            }
        }

        // Filter by jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('kode_kelas', 'like', "%{$search}%");
            });
        }

        $kelas = $query->withCount('siswa')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->paginate(15);

        // Data untuk filter
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get current tahun ajaran untuk display
        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        // Statistics
        $stats = [
            'totalKelas' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count(),
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'kelasWithWali' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
            ->whereHas('waliKelasAssignments')->count(),
        'kelasWithoutWali' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
            ->whereDoesntHave('waliKelasAssignments')->count(),
        ];

        return view('waka.kelas.index', compact('kelas', 'tahunAjarans', 'cabangs', 'jenjangs', 'currentTahunAjaran', 'stats'));
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $waliKelasOptions = TenagaPendidik::whereHas('user', function ($q) {
            $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])
                ->where('is_active', true);
        })->orderBy('nama_lengkap')->get();

        return view('waka.kelas.create', compact('tahunAjarans', 'cabangs', 'jenjangs', 'waliKelasOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kode_kelas' => 'required|string|max:50|unique:kelas,kode_kelas',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'cabang_id' => 'required|exists:cabang,id',
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
            'kuota_siswa' => 'required|integer|min:1',
        ]);

        $kelas = Kelas::create($validated);

        // Sync with pivot table wali_kelas_assignments
        if (isset($validated['wali_kelas_id']) && $validated['wali_kelas_id']) {
            WaliKelasAssignment::updateOrCreate(
                [
                    'tenaga_pendidik_id' => $validated['wali_kelas_id'],
                    'kelas_id' => $kelas->id,
                ],
                [
                    'assigned_at' => now(),
                ]
            );
        }

        return redirect()->route('waka.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function show(Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'waliKelas']);

        // Get siswa with pagination
        $siswa = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->paginate(20);

        // Statistics
        $stats = [
            'totalSiswa' => Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->count(),
            'siswaLaki' => Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
            'sisaKuota' => $kelas->kuota_siswa - Siswa::where('kelas_id', $kelas->id)->where('status', 'aktif')->count(),
        ];

        return view('waka.kelas.show', compact('kelas', 'siswa', 'stats'));
    }

    public function edit(Kelas $kelas)
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $waliKelasOptions = TenagaPendidik::whereHas('user', function ($q) {
            $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])
                ->where('is_active', true);
        })->orderBy('nama_lengkap')->get();

        return view('waka.kelas.edit', compact('kelas', 'tahunAjarans', 'cabangs', 'jenjangs', 'waliKelasOptions'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kode_kelas' => 'required|string|max:50|unique:kelas,kode_kelas,' . $kelas->id,
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'cabang_id' => 'required|exists:cabang,id',
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
            'kuota_siswa' => 'required|integer|min:1',
        ]);

        $kelas->update($validated);

        // Sync with pivot table wali_kelas_assignments
        WaliKelasAssignment::where('kelas_id', $kelas->id)->delete();
        
        if (isset($validated['wali_kelas_id']) && $validated['wali_kelas_id']) {
            WaliKelasAssignment::create([
                'tenaga_pendidik_id' => $validated['wali_kelas_id'],
                'kelas_id' => $kelas->id,
                'assigned_at' => now(),
            ]);
        }

        return redirect()->route('waka.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        try {
            // Check if there are any students in this class
            if ($kelas->siswa()->count() > 0) {
                return redirect()->back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa');
            }

            $kelas->delete();
            return redirect()->route('waka.kelas.index')
                ->with('success', 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus kelas');
        }
    }

    public function manageSiswa(Kelas $kelas)
    {
        $kelas->load(['siswa', 'tahunAjaran', 'cabang', 'waliKelas']);

        // Students currently in this class
        $siswaInKelas = $kelas->siswa;

        // Get available students (not in any class)
        $siswaAvailable = Siswa::whereNull('kelas_id')
            ->where('status', 'aktif')
            ->where('cabang_id', $kelas->cabang_id)
            ->orderBy('nama_lengkap')
            ->get();

        // Calculate sisa kuota
        $sisaKuota = $kelas->kuota_siswa - $siswaInKelas->count();

        // Statistics
        $stats = [
            'totalSiswa' => $siswaInKelas->count(),
            'siswaLaki' => $siswaInKelas->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => $siswaInKelas->where('jenis_kelamin', 'P')->count(),
            'sisaKuota' => $sisaKuota,
        ];

        return view('waka.kelas.manage-siswa', compact('kelas', 'siswaInKelas', 'siswaAvailable', 'stats', 'sisaKuota'));
    }

    public function addSiswa(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);

        // Check if kelas is full
        $currentCount = $kelas->siswa()->count();
        if ($currentCount >= $kelas->kuota_siswa) {
            return redirect()->route('waka.kelas.manage-siswa', $kelas)
                ->with('error', 'Kelas sudah penuh, kuota tercapai');
        }

        $siswa->kelas_id = $kelas->id;
        $siswa->save();

        return redirect()->route('waka.kelas.manage-siswa', $kelas)
            ->with('success', 'Siswa berhasil ditambahkan ke kelas');
    }

    public function removeSiswa(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $siswa->kelas_id = null;
        $siswa->save();

        return redirect()->route('waka.kelas.manage-siswa', $kelas)
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas');
    }

    public function print(Request $request)
    {
        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])->withCount('siswa');

        // Apply same filters as index
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

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('kode_kelas', 'like', "%{$search}%");
            });
        }

        $kelas = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();

        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        return view('waka.kelas.print', compact('kelas', 'currentTahunAjaran'));
    }

    public function import()
    {
        return view('waka.kelas.import');
    }

    public function downloadTemplate()
    {
        return Excel::download(new KelasTemplate, 'template_kelas.xlsx');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        $import = new KelasImport;

        try {
            Excel::import($import, $request->file('file'));

            $count = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $warnings = $import->getWarnings();

            $message = "Import selesai! {$count} data berhasil diimport.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            if (count($warnings) > 0) {
                return redirect()->route('waka.kelas.index')
                    ->with('success', $message)
                    ->with('warning', 'Beberapa data memiliki peringatan: ' . implode(', ', array_slice($warnings, 0, 5)) . (count($warnings) > 5 ? '...' : ''));
            }

            return redirect()->route('waka.kelas.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
