<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Cabang;
use Illuminate\Http\Request;

class ManajemenSiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas.tahunAjaran', 'cabang', 'user']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter by jenjang
        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show only aktif
            $query->where('status', 'aktif');
        }

        // Filter no kelas
        if ($request->filled('no_kelas') && $request->no_kelas == '1') {
            $query->whereNull('kelas_id');
        }

        $siswaList = $query->orderBy('nama_lengkap')->paginate(20);

        // Data for filters
        $cabangs = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::with('tahunAjaran')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Statistics
        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'siswaWithKelas' => Siswa::where('status', 'aktif')->whereNotNull('kelas_id')->count(),
            'siswaNoKelas' => Siswa::where('status', 'aktif')->whereNull('kelas_id')->count(),
            'siswaLaki' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
        ];

        return view('waka.manajemen-siswa.index', compact('siswaList', 'cabangs', 'kelasList', 'jenjangs', 'stats'));
    }

    public function show(Siswa $siswa)
    {
        $siswa->load(['kelas.cabang', 'cabang', 'user', 'orangTua']);

        // Get available classes with student count
        $kelasList = Kelas::with(['cabang', 'tahunAjaran'])
            ->withCount('siswa')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        // Get available parents (orang_tua role yang belum terhubung dengan siswa ini)
        $currentParentIds = $siswa->orangTua->pluck('id')->toArray();
        $availableParents = \App\Models\User::where('role', 'orang_tua')
            ->whereNotIn('id', $currentParentIds)
            ->orderBy('name')
            ->get();

        return view('waka.manajemen-siswa.show', compact('siswa', 'kelasList', 'availableParents'));
    }

    public function assignKelas(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        $siswa->kelas_id = $validated['kelas_id'];
        $siswa->save();

        $message = $validated['kelas_id']
            ? 'Siswa berhasil ditempatkan ke kelas'
            : 'Siswa berhasil dihapus dari kelas';

        return redirect()->route('waka.manajemen-siswa.show', $siswa)
            ->with('success', $message);
    }

    public function attachParent(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'parent_id' => 'required|exists:users,id',
            'relationship' => 'required|string|max:50'
        ]);

        // Check if parent already attached
        if ($siswa->orangTua->contains($validated['parent_id'])) {
            return redirect()->route('waka.manajemen-siswa.show', $siswa)
                ->with('error', 'Orang tua sudah terhubung dengan siswa ini');
        }

        // Attach parent with relationship
        $siswa->orangTua()->attach($validated['parent_id'], [
            'relationship' => $validated['relationship']
        ]);

        return redirect()->route('waka.manajemen-siswa.show', $siswa)
            ->with('success', 'Orang tua berhasil ditambahkan');
    }

    public function detachParent(Siswa $siswa, $parentId)
    {
        // Detach the parent from the student
        $siswa->orangTua()->detach($parentId);

        return redirect()->route('waka.manajemen-siswa.show', $siswa)
            ->with('success', 'Hubungan dengan orang tua berhasil dihapus');
    }

    public function print(Request $request)
    {
        $query = Siswa::with(['kelas.tahunAjaran.waliKelas', 'cabang']);

        // Apply same filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'aktif');
        }

        if ($request->filled('no_kelas') && $request->no_kelas == '1') {
            $query->whereNull('kelas_id');
        }

        // Sort by
        $sortBy = $request->input('sort_by', 'kelas');
        if ($sortBy == 'kelas') {
            $siswaList = $query->orderBy('kelas_id')->orderBy('nama_lengkap')->get();
        } elseif ($sortBy == 'cabang') {
            $siswaList = $query->orderBy('cabang_id')->orderBy('nama_lengkap')->get();
        } else {
            $siswaList = $query->orderBy('nama_lengkap')->get();
        }

        // Get specific kelas or cabang if filtered
        $kelas = $request->filled('kelas_id') ? Kelas::with('waliKelas')->find($request->kelas_id) : null;
        $cabang = $request->filled('cabang_id') ? Cabang::find($request->cabang_id) : null;

        return view('waka.manajemen-siswa.print', compact('siswaList', 'kelas', 'cabang', 'sortBy'));
    }

    public function printKartu(Siswa $siswa)
    {
        $siswa->load(['kelas', 'cabang']);
        return view('waka.manajemen-siswa.print-kartu', compact('siswa'));
    }

    public function perKelas(Request $request, Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'waliKelas']);

        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        // Siswa tanpa kelas untuk ditambahkan
        $availableSiswa = Siswa::whereNull('kelas_id')
            ->where('status', 'aktif')
            ->where('cabang_id', $kelas->cabang_id)
            ->orderBy('nama_lengkap')
            ->get();

        $stats = [
            'totalSiswa' => $siswaList->count(),
            'siswaLaki' => $siswaList->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => $siswaList->where('jenis_kelamin', 'P')->count(),
            'sisaKuota' => $kelas->kuota_siswa - $siswaList->count(),
        ];

        return view('waka.manajemen-siswa.per-kelas', compact('kelas', 'siswaList', 'availableSiswa', 'stats'));
    }

    public function addToKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);

        // Check if kelas is full
        $currentCount = $kelas->siswa()->count();
        if ($currentCount >= $kelas->kuota_siswa) {
            return redirect()->route('waka.manajemen-siswa.per-kelas', $kelas)
                ->with('error', 'Kelas sudah penuh, kuota tercapai');
        }

        $siswa->kelas_id = $kelas->id;
        $siswa->save();

        return redirect()->route('waka.manajemen-siswa.per-kelas', $kelas)
            ->with('success', 'Siswa berhasil ditambahkan ke kelas');
    }

    public function removeFromKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $siswa->kelas_id = null;
        $siswa->save();

        return redirect()->route('waka.manajemen-siswa.per-kelas', $kelas)
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas');
    }
}
