<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Imports\MataPelajaranImport;
use App\Exports\Templates\MataPelajaranTemplate;
use Maatwebsite\Excel\Facades\Excel;
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

    /**
     * Show the import form.
     */
    public function importForm()
    {
        return view('admin.mata-pelajaran.import');
    }

    /**
     * Process the import from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120', // Max 5MB
        ], [
            'file.required' => 'File Excel wajib dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        try {
            $import = new MataPelajaranImport();
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $failures = $import->failures();

            $message = "Berhasil mengimport {$imported} mata pelajaran.";

            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (sudah ada).";
            }

            if ($failures->count() > 0) {
                $errorRows = $failures->map(fn($f) => $f->row())->unique()->implode(', ');
                $message .= " Baris dengan error: {$errorRows}";
                return redirect()
                    ->route('admin.mata-pelajaran.index')
                    ->with('warning', $message);
            }

            return redirect()
                ->route('admin.mata-pelajaran.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download the import template.
     */
    public function downloadTemplate()
    {
        return Excel::download(new MataPelajaranTemplate(), 'template_mata_pelajaran.xlsx');
    }
}

