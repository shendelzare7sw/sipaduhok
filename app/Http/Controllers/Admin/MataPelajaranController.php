<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MataPelajaranController extends Controller
{
    /**
     * Display a listing of mata pelajaran.
     */
    public function index(Request $request)
    {
        $jenjang = $request->jenjang;

        $query = MataPelajaran::query();

        // Filter by jenjang if provided
        if ($jenjang) {
            $query->where('jenjang', $jenjang);
        }

        $mataPelajaranList = $query->orderBy('jenjang')
            ->orderBy('nama_mapel')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => MataPelajaran::count(),
            'kb' => MataPelajaran::where('jenjang', 'KB')->count(),
            'tka' => MataPelajaran::where('jenjang', 'TKA')->count(),
            'tkb' => MataPelajaran::where('jenjang', 'TKB')->count(),
            'sd' => MataPelajaran::where('jenjang', 'SD')->count(),
            'smp' => MataPelajaran::where('jenjang', 'SMP')->count(),
            'sma' => MataPelajaran::where('jenjang', 'SMA')->count(),
        ];

        return view('admin.mata-pelajaran.index', compact('mataPelajaranList', 'stats', 'jenjang'));
    }

    /**
     * Show the form for creating a new mata pelajaran.
     */
    public function create()
    {
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        return view('admin.mata-pelajaran.create', compact('jenjangList'));
    }

    /**
     * Store a newly created mata pelajaran in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'kode_mapel' => 'nullable|string|max:20|unique:mata_pelajaran,kode_mapel',
            'deskripsi' => 'nullable|string',
        ]);

        MataPelajaran::create($validated);

        return redirect()
            ->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    /**
     * Display the specified mata pelajaran.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load(['jadwalPelajaran.kelas', 'jadwalPelajaran.guru']);

        // Statistics untuk mata pelajaran ini
        $stats = [
            'totalJadwal' => $mataPelajaran->jadwalPelajaran()->count(),
            'totalKelas' => $mataPelajaran->jadwalPelajaran()->distinct('kelas_id')->count(),
            'totalGuru' => $mataPelajaran->jadwalPelajaran()->whereNotNull('guru_id')->distinct('guru_id')->count(),
        ];

        return view('admin.mata-pelajaran.show', compact('mataPelajaran', 'stats'));
    }

    /**
     * Show the form for editing the specified mata pelajaran.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        return view('admin.mata-pelajaran.edit', compact('mataPelajaran', 'jenjangList'));
    }

    /**
     * Update the specified mata pelajaran in storage.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:100',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'kode_mapel' => 'nullable|string|max:20|unique:mata_pelajaran,kode_mapel,' . $mataPelajaran->id,
            'deskripsi' => 'nullable|string',
        ]);

        $mataPelajaran->update($validated);

        return redirect()
            ->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified mata pelajaran from storage.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        // Check if mata pelajaran is being used in jadwal
        $jadwalCount = $mataPelajaran->jadwalPelajaran()->count();

        if ($jadwalCount > 0) {
            return back()->with('error', "Mata pelajaran tidak dapat dihapus karena masih digunakan di {$jadwalCount} jadwal!");
        }

        $mataPelajaran->delete();

        return redirect()
            ->route('admin.mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus!');
    }
}
