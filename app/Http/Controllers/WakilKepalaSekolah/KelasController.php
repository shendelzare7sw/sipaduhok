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
    private function getUserCabangId()
    {
        $cabangId = auth()->user()->cabang_id;

        if (!$cabangId) {
            abort(403, 'Akun Anda belum memiliki cabang yang ditetapkan. Hubungi administrator.');
        }

        return $cabangId;
    }

    private function ensureKelasInUserCabang(Kelas $kelas): void
    {
        if ((int) $kelas->cabang_id !== (int) $this->getUserCabangId()) {
            abort(403, 'Anda tidak berhak mengakses kelas dari cabang lain.');
        }
    }

    private function ensureWaliKelasInUserCabang(?int $waliKelasId): void
    {
        if (!$waliKelasId) {
            return;
        }

        $userCabangId = $this->getUserCabangId();

        $exists = TenagaPendidik::where('id', $waliKelasId)
            ->whereHas('user', function ($query) use ($userCabangId) {
                $query->where('cabang_id', $userCabangId)
                    ->where('is_active', true)
                    ->whereIn('role', ['wali_kelas', 'guru_pengajar']);
            })
            ->exists();

        if (!$exists) {
            abort(403, 'Wali kelas harus berasal dari cabang Anda.');
        }
    }

    public function index(Request $request)
    {
        $userCabangId = auth()->user()->cabang_id;
        if (!$userCabangId) {
            return redirect()->back()->with('error', 'Akun Anda belum memiliki cabang yang ditetapkan. Hubungi administrator.');
        }

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

        // Mandatory filter by user's assigned cabang
        $query->where('cabang_id', $userCabangId);

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
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Get current tahun ajaran untuk display
        $currentTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $currentTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        } else {
            $currentTahunAjaran = TahunAjaran::where('is_active', true)->first();
        }

        // Statistics (filtered by user's cabang)
        $stats = [
            'totalKelas' => Kelas::where('cabang_id', $userCabangId)
                ->when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))->count(),
            'totalSiswa' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->count(),
            'kelasWithWali' => Kelas::where('cabang_id', $userCabangId)
                ->when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereHas('waliKelasAssignments')->count(),
            'kelasWithoutWali' => Kelas::where('cabang_id', $userCabangId)
                ->when($currentTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $currentTahunAjaran->id))
                ->whereDoesntHave('waliKelasAssignments')->count(),
        ];

        return view('waka.kelas.index', compact('kelas', 'tahunAjarans', 'jenjangs', 'currentTahunAjaran', 'stats'));
    }

    public function create()
    {
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $userCabangId = $this->getUserCabangId();
        $userCabang = auth()->user()->cabang;
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $waliKelasOptions = TenagaPendidik::whereHas('user', function ($q) use ($userCabangId) {
            $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])
                ->where('is_active', true)
                ->where('cabang_id', $userCabangId);
        })->orderBy('nama_lengkap')->get();

        return view('waka.kelas.create', compact('tahunAjarans', 'userCabang', 'jenjangs', 'waliKelasOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'cabang_id' => 'nullable|exists:cabang,id',
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
            'kuota_siswa' => 'required|integer|min:1',
        ]);

        $this->ensureWaliKelasInUserCabang($validated['wali_kelas_id'] ?? null);

        // Force cabang from authenticated user (security: ignore submitted cabang_id)
        $validated['cabang_id'] = $this->getUserCabangId();

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
        $this->ensureKelasInUserCabang($kelas);

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
        $this->ensureKelasInUserCabang($kelas);

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $userCabangId = $this->getUserCabangId();
        $userCabang = auth()->user()->cabang;
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];
        $waliKelasOptions = TenagaPendidik::whereHas('user', function ($q) use ($userCabangId) {
            $q->whereIn('role', ['wali_kelas', 'guru_pengajar'])
                ->where('is_active', true)
                ->where('cabang_id', $userCabangId);
        })->orderBy('nama_lengkap')->get();

        return view('waka.kelas.edit', compact('kelas', 'tahunAjarans', 'userCabang', 'jenjangs', 'waliKelasOptions'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->ensureKelasInUserCabang($kelas);

        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jenjang' => 'required|in:KB,TKA,TKB,SD,SMP,SMA',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'cabang_id' => 'nullable|exists:cabang,id',
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
            'kuota_siswa' => 'required|integer|min:1',
        ]);

        $this->ensureWaliKelasInUserCabang($validated['wali_kelas_id'] ?? null);

        // Preserve the existing cabang (waka cannot change it)
        $validated['cabang_id'] = $kelas->cabang_id;

        // Regenerate kode_kelas if needed
        $cabang = Cabang::find($validated['cabang_id']);
        $tahunAjaran = TahunAjaran::find($validated['tahun_ajaran_id']);
        $tahun = date('Y', strtotime($tahunAjaran->tanggal_mulai));

        $newKodeKelas = $cabang->kode_cabang . '-' . $validated['jenjang'] . '-' .
            strtoupper(str_replace(' ', '', $validated['nama_kelas'])) . '-' . $tahun;

        if ($newKodeKelas !== $kelas->kode_kelas) {
            $existingKelas = Kelas::where('kode_kelas', $newKodeKelas)->first();
            if ($existingKelas) {
                return back()->withInput()->with('error', 'Kelas dengan kode tersebut sudah ada.');
            }
            $validated['kode_kelas'] = $newKodeKelas;
        }

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
        $this->ensureKelasInUserCabang($kelas);

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

    public function assignWaliKelas(Request $request, Kelas $kelas)
    {
        $this->ensureKelasInUserCabang($kelas);

        $validated = $request->validate([
            'wali_kelas_id' => 'nullable|exists:tenaga_pendidik,id',
        ]);

        $this->ensureWaliKelasInUserCabang($validated['wali_kelas_id'] ?? null);

        $kelas->update(['wali_kelas_id' => $validated['wali_kelas_id']]);

        // Sync with pivot table wali_kelas_assignments
        WaliKelasAssignment::where('kelas_id', $kelas->id)->delete();
        
        if ($validated['wali_kelas_id']) {
            WaliKelasAssignment::create([
                'tenaga_pendidik_id' => $validated['wali_kelas_id'],
                'kelas_id' => $kelas->id,
                'assigned_at' => now(),
            ]);
            
            $waliKelas = TenagaPendidik::find($validated['wali_kelas_id']);
            return back()->with('success', $waliKelas->nama_lengkap . ' berhasil ditunjuk sebagai Wali Kelas!');
        } else {
            return back()->with('success', 'Wali Kelas berhasil dihapus dari kelas ini!');
        }
    }

    public function manageSiswa(Kelas $kelas)
    {
        $this->ensureKelasInUserCabang($kelas);

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
        $this->ensureKelasInUserCabang($kelas);

        $validated = $request->validate([
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
            'siswa_id' => 'nullable|exists:siswa,id',
        ]);

        $siswaIds = collect($validated['siswa_ids'] ?? [])
            ->when(isset($validated['siswa_id']), fn($ids) => $ids->push($validated['siswa_id']))
            ->filter()
            ->unique()
            ->values();

        if ($siswaIds->isEmpty()) {
            return redirect()->route('waka.kelas.manage-siswa', $kelas)
                ->with('error', 'Pilih siswa yang akan ditambahkan');
        }

        // Check if kelas is full
        $currentCount = $kelas->siswa()->count();
        if (($currentCount + $siswaIds->count()) > $kelas->kuota_siswa) {
            return redirect()->route('waka.kelas.manage-siswa', $kelas)
                ->with('error', 'Kelas sudah penuh, kuota tercapai');
        }

        $eligibleIds = Siswa::whereIn('id', $siswaIds->all())
            ->where('status', 'aktif')
            ->where('cabang_id', $kelas->cabang_id)
            ->whereNull('kelas_id')
            ->pluck('id');

        if ($eligibleIds->count() !== $siswaIds->count()) {
            return redirect()->route('waka.kelas.manage-siswa', $kelas)
                ->with('error', 'Pastikan siswa aktif, berasal dari cabang yang sama, dan belum memiliki kelas');
        }

        Siswa::whereIn('id', $eligibleIds->all())->update(['kelas_id' => $kelas->id]);

        return redirect()->route('waka.kelas.manage-siswa', $kelas)
            ->with('success', $eligibleIds->count() . ' siswa berhasil ditambahkan ke kelas');
    }

    public function removeSiswa(Request $request, Kelas $kelas)
    {
        $this->ensureKelasInUserCabang($kelas);

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        if ((int) $siswa->kelas_id !== (int) $kelas->id) {
            return redirect()->route('waka.kelas.manage-siswa', $kelas)
                ->with('error', 'Siswa tersebut tidak terdaftar di kelas ini');
        }

        $siswa->kelas_id = null;
        $siswa->save();

        return redirect()->route('waka.kelas.manage-siswa', $kelas)
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas');
    }

    public function print(Request $request)
    {
        $userCabangId = $this->getUserCabangId();
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

        // Mandatory filter by user's assigned cabang
        $query->where('cabang_id', $userCabangId);

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
        $this->getUserCabangId();

        return view('waka.kelas.import');
    }

    public function downloadTemplate()
    {
        return Excel::download(new KelasTemplate($this->getUserCabangId()), 'template_kelas.xlsx');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        $import = new KelasImport($this->getUserCabangId());

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
