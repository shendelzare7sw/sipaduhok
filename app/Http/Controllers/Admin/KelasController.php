<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\TahunAjaran;
use App\Models\TenagaPendidik;
use App\Models\Siswa;
use App\Imports\KelasImport;
use App\Exports\Templates\KelasTemplate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas']);

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
                ->whereNotNull('wali_kelas_id')->count(),
            'kelasWithoutWali' => Kelas::when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereNull('wali_kelas_id')->count(),
        ];

        return view('admin.kelas.index', compact(
            'kelas',
            'tahunAjarans',
            'cabangs',
            'jenjangs',
            'currentTahunAjaran',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get tenaga pendidik yang bisa jadi wali kelas (hanya role wali_kelas)
        $waliKelasOptions = TenagaPendidik::whereHas('user.roleRelation', function ($q) {
            $q->where('name', 'wali_kelas');
        })->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->orderBy('nama_lengkap')->get();

        return view('admin.kelas.create', compact('tahunAjarans', 'cabangs', 'jenjangs', 'waliKelasOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cabang_id' => 'required|exists:cabang,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
            'nama_kelas' => 'required|string|max:50',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'kuota_siswa' => 'required|integer|min:1|max:100',
        ], [
            'cabang_id.required' => 'Cabang harus dipilih',
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'nama_kelas.required' => 'Nama kelas harus diisi',
            'jenjang.required' => 'Jenjang harus dipilih',
            'kuota_siswa.required' => 'Kuota siswa harus diisi',
            'kuota_siswa.min' => 'Kuota siswa minimal 1',
        ]);

        // Generate kode_kelas otomatis
        $cabang = Cabang::find($validated['cabang_id']);
        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);
        $tahun = date('Y', strtotime($tahunAjaran->tanggal_mulai));

        $kodeKelas = $cabang->kode_cabang . '-' . $validated['jenjang'] . '-' .
            strtoupper(str_replace(' ', '', $validated['nama_kelas'])) . '-' . $tahun;

        // Check if kode_kelas already exists
        $existingKelas = Kelas::where('kode_kelas', $kodeKelas)->first();
        if ($existingKelas) {
            return back()->withInput()->with('error', 'Kelas dengan kode tersebut sudah ada. Silakan gunakan nama kelas yang berbeda.');
        }

        $validated['kode_kelas'] = $kodeKelas;

        // PENTING: Hapus assignment lama jika wali kelas dipilih sudah mengajar di kelas lain
        if (isset($validated['wali_kelas_id']) && $validated['wali_kelas_id']) {
            // Hapus wali kelas dari kelas lain (set jadi NULL)
            Kelas::where('wali_kelas_id', $validated['wali_kelas_id'])
                ->update(['wali_kelas_id' => null]);
        }

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'waliKelas']);

        // Get siswa in this kelas
        $siswa = Siswa::where('kelas_id', $kelas->id)
            ->orderBy('nama_lengkap')
            ->paginate(20);

        // Statistics
        $stats = [
            'totalSiswa' => Siswa::where('kelas_id', $kelas->id)->count(),
            'siswaLaki' => Siswa::where('kelas_id', $kelas->id)->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => Siswa::where('kelas_id', $kelas->id)->where('jenis_kelamin', 'P')->count(),
            'sisaKuota' => $kelas->kuota_siswa - Siswa::where('kelas_id', $kelas->id)->count(),
        ];

        return view('admin.kelas.show', compact('kelas', 'siswa', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get tenaga pendidik yang bisa jadi wali kelas (hanya role wali_kelas)
        $waliKelasOptions = TenagaPendidik::whereHas('user.roleRelation', function ($q) {
            $q->where('name', 'wali_kelas');
        })->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->orderBy('nama_lengkap')->get();

        return view('admin.kelas.edit', compact('kelas', 'tahunAjarans', 'cabangs', 'jenjangs', 'waliKelasOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'cabang_id' => 'required|exists:cabang,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
            'nama_kelas' => 'required|string|max:50',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'kuota_siswa' => 'required|integer|min:1|max:100',
        ], [
            'cabang_id.required' => 'Cabang harus dipilih',
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'nama_kelas.required' => 'Nama kelas harus diisi',
            'jenjang.required' => 'Jenjang harus dipilih',
            'kuota_siswa.required' => 'Kuota siswa harus diisi',
        ]);

        // PENTING: Hapus assignment lama jika wali kelas dipilih sudah mengajar di kelas lain
        if (isset($validated['wali_kelas_id']) && $validated['wali_kelas_id']) {
            // Hapus wali kelas dari kelas lain (set jadi NULL)
            Kelas::where('wali_kelas_id', $validated['wali_kelas_id'])
                ->where('id', '!=', $kelas->id)
                ->update(['wali_kelas_id' => null]);
        }

        // Regenerate kode_kelas jika ada perubahan
        $cabang = Cabang::find($validated['cabang_id']);
        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);
        $tahun = date('Y', strtotime($tahunAjaran->tanggal_mulai));

        $newKodeKelas = $cabang->kode_cabang . '-' . $validated['jenjang'] . '-' .
            strtoupper(str_replace(' ', '', $validated['nama_kelas'])) . '-' . $tahun;

        // Check if new kode_kelas already exists (excluding current)
        if ($newKodeKelas !== $kelas->kode_kelas) {
            $existingKelas = Kelas::where('kode_kelas', $newKodeKelas)->first();
            if ($existingKelas) {
                return back()->withInput()->with('error', 'Kelas dengan kode tersebut sudah ada.');
            }
            $validated['kode_kelas'] = $newKodeKelas;
        }

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        // Check if kelas has siswa
        $siswaCount = Siswa::where('kelas_id', $kelas->id)->count();
        if ($siswaCount > 0) {
            return redirect()->route('admin.kelas.index')
                ->with('error', "Kelas tidak dapat dihapus karena masih memiliki {$siswaCount} siswa terdaftar.");
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil dihapus!');
    }

    /**
     * Manage siswa in kelas (add/remove)
     */
    public function manageSiswa(Kelas $kelas)
    {
        $kelas->load(['cabang', 'tahunAjaran', 'waliKelas']);

        // Siswa yang sudah ada di kelas ini
        $siswaInKelas = Siswa::where('kelas_id', $kelas->id)
            ->orderBy('nama_lengkap')
            ->get();

        // Siswa yang belum memiliki kelas atau di kelas lain (untuk ditambahkan)
        $siswaAvailable = Siswa::where('status', 'aktif')
            ->where(function ($q) use ($kelas) {
                $q->whereNull('kelas_id')
                    ->orWhere('kelas_id', '!=', $kelas->id);
            })
            ->where('cabang_id', $kelas->cabang_id) // Filter by same cabang
            ->orderBy('nama_lengkap')
            ->get();

        $sisaKuota = $kelas->kuota_siswa - $siswaInKelas->count();

        return view('admin.kelas.manage-siswa', compact('kelas', 'siswaInKelas', 'siswaAvailable', 'sisaKuota'));
    }

    /**
     * Add siswa to kelas
     */
    public function addSiswa(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        // Check kuota
        $currentCount = Siswa::where('kelas_id', $kelas->id)->count();
        $newCount = count($validated['siswa_ids']);

        if (($currentCount + $newCount) > $kelas->kuota_siswa) {
            return back()->with('error', 'Jumlah siswa melebihi kuota kelas. Sisa kuota: ' . ($kelas->kuota_siswa - $currentCount));
        }

        // Update siswa kelas_id
        Siswa::whereIn('id', $validated['siswa_ids'])
            ->update(['kelas_id' => $kelas->id]);

        return back()->with('success', $newCount . ' siswa berhasil ditambahkan ke kelas!');
    }

    /**
     * Remove siswa from kelas
     */
    public function removeSiswa(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $siswa = Siswa::find($validated['siswa_id']);
        $siswa->update(['kelas_id' => null]);

        return back()->with('success', 'Siswa ' . $siswa->nama_lengkap . ' berhasil dikeluarkan dari kelas!');
    }

    /**
     * Assign wali kelas
     */
    public function assignWaliKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
        ]);

        $kelas->update(['wali_kelas_id' => $validated['wali_kelas_id']]);

        if ($validated['wali_kelas_id']) {
            $waliKelas = TenagaPendidik::find($validated['wali_kelas_id']);
            return back()->with('success', $waliKelas->nama_lengkap . ' berhasil ditunjuk sebagai Wali Kelas!');
        } else {
            return back()->with('success', 'Wali Kelas berhasil dihapus dari kelas ini!');
        }
    }

    /**
     * Print daftar kelas
     */
    public function printDaftarKelas(Request $request)
    {
        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])
            ->withCount('siswa');

        // Filter by tahun ajaran
        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }

        // Filter by jenjang
        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        $kelas = $query->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $tahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : TahunAjaran::where('is_active', true)->first();

        return view('admin.kelas.print', compact('kelas', 'tahunAjaran'));
    }

    /**
     * Show the import form.
     */
    public function importForm()
    {
        return view('admin.kelas.import');
    }

    /**
     * Process the import from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'File Excel wajib dipilih',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 5MB',
        ]);

        try {
            $import = new KelasImport();
            Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();
            $missingCabang = $import->getMissingCabang();
            $missingWaliKelas = $import->getMissingWaliKelas();

            $message = "Berhasil mengimport {$imported} kelas.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati.";
            }

            // Build warning message
            $warningMessage = '';
            if (!empty($missingCabang)) {
                $warningMessage .= "Cabang tidak ditemukan: " . implode(', ', $missingCabang) . ". ";
            }
            if (!empty($missingWaliKelas)) {
                $warningMessage .= "Wali Kelas tidak ditemukan: " . implode(', ', $missingWaliKelas) . ". ";
            }

            if (!empty($warningMessage)) {
                return redirect()
                    ->route('admin.kelas.index')
                    ->with('success', $message)
                    ->with('warning', $warningMessage);
            }

            return redirect()
                ->route('admin.kelas.index')
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
        return Excel::download(new KelasTemplate(), 'template_kelas.xlsx');
    }
}

