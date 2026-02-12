<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Templates\MataPelajaranTemplate;
use App\Imports\MataPelajaranImport;

class MataPelajaranController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        // Capture jenjang filter value
        $jenjang = $request->input('jenjang', null);

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('search')) {
            $query->where('nama_mapel', 'like', '%' . $request->search . '%');
        }

        $mataPelajaranList = $query->orderBy('jenjang')->orderBy('nama_mapel')->paginate(20);

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

        return view('waka.mata-pelajaran.index', compact('mataPelajaranList', 'stats', 'jenjang'));
    }

    public function create()
    {
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        return view('waka.mata-pelajaran.create', compact('jenjangList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajaran,kode_mapel',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'kategori' => 'nullable|in:wajib,pilihan,muatan_lokal',
            'deskripsi' => 'nullable|string'
        ]);

        MataPelajaran::create($validated);

        return redirect()->route('waka.mata-pelajaran.index')
            ->with('success', 'Mata Pelajaran berhasil ditambahkan');
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load(['jadwalPelajaran.kelas.cabang', 'jadwalPelajaran.guru']);

        // Statistics
        $stats = [
            'totalJadwal' => $mataPelajaran->jadwalPelajaran()->count(),
            'totalKelas' => $mataPelajaran->jadwalPelajaran()->distinct('kelas_id')->count('kelas_id'),
            'totalGuru' => $mataPelajaran->jadwalPelajaran()->distinct('guru_id')->whereNotNull('guru_id')->count('guru_id'),
        ];

        return view('waka.mata-pelajaran.show', compact('mataPelajaran', 'stats'));
    }

    public function edit(MataPelajaran $mataPelajaran)
    {
        $jenjangList = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        return view('waka.mata-pelajaran.edit', compact('mataPelajaran', 'jenjangList'));
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:50|unique:mata_pelajaran,kode_mapel,' . $mataPelajaran->id,
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'kategori' => 'nullable|in:wajib,pilihan,muatan_lokal',
            'deskripsi' => 'nullable|string'
        ]);

        $mataPelajaran->update($validated);

        return redirect()->route('waka.mata-pelajaran.index')
            ->with('success', 'Mata Pelajaran berhasil diperbarui');
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        try {
            $mataPelajaran->delete();
            return redirect()->route('waka.mata-pelajaran.index')
                ->with('success', 'Mata Pelajaran berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus mata pelajaran. Mungkin masih ada data terkait.');
        }
    }

    public function import()
    {
        return view('waka.mata-pelajaran.import');
    }

    public function downloadTemplate()
    {
        return Excel::download(new MataPelajaranTemplate, 'template_mata_pelajaran.xlsx');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        $import = new MataPelajaranImport;

        try {
            Excel::import($import, $request->file('file'));

            $count = $import->getImportedCount();
            $skipped = $import->getSkippedCount();

            $message = "Import selesai! {$count} data berhasil diimport.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (duplikat/invalid).";
            }

            return redirect()->route('waka.mata-pelajaran.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    /**
     * Get suggested kode mapel based on jenjang.
     */
    public function suggestKodeMapel(Request $request)
    {
        $jenjang = $request->input('jenjang');
        
        if (!$jenjang) {
            return response()->json(['suggestions' => []]);
        }

        // Get all existing codes for this jenjang
        $existingCodes = MataPelajaran::where('jenjang', $jenjang)
            ->whereNotNull('kode_mapel')
            ->pluck('kode_mapel')
            ->toArray();

        // Extract numbers from existing codes (e.g., "SMA-002" -> 2)
        $usedNumbers = [];
        foreach ($existingCodes as $code) {
            // Match pattern: JENJANG-XXX
            if (preg_match('/^' . preg_quote($jenjang, '/') . '-(\d+)$/', $code, $matches)) {
                $usedNumbers[] = (int)$matches[1];
            }
        }

        // Find next available numbers (suggest 5 options)
        $suggestions = [];
        $nextNumber = empty($usedNumbers) ? 1 : max($usedNumbers) + 1;
        
        for ($i = 0; $i < 5; $i++) {
            $number = $nextNumber + $i;
            $code = $jenjang . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
            $suggestions[] = $code;
        }

        return response()->json([
            'suggestions' => $suggestions,
            'jenjang' => $jenjang
        ]);
    }
}
