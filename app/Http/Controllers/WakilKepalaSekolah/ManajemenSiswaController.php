<?php

namespace App\Http\Controllers\WakilKepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\TahunAjaran;
use App\Models\StatusNaikKelasSiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Templates\SiswaTemplate;
use App\Imports\SiswaImport;

class ManajemenSiswaController extends Controller
{
    private function getUserCabangId(): int
    {
        $cabangId = auth()->user()->cabang_id;

        if (!$cabangId) {
            abort(403, 'Akun Anda belum memiliki cabang yang ditetapkan. Hubungi administrator.');
        }

        return (int) $cabangId;
    }

    private function ensureSiswaInUserCabang(Siswa $siswa): void
    {
        if ((int) $siswa->cabang_id !== $this->getUserCabangId()) {
            abort(403, 'Anda tidak berhak mengakses siswa dari cabang lain.');
        }
    }

    private function ensureKelasInUserCabang(Kelas $kelas): void
    {
        if ((int) $kelas->cabang_id !== $this->getUserCabangId()) {
            abort(403, 'Anda tidak berhak mengakses kelas dari cabang lain.');
        }
    }

    private function scopeOrangTuaRole($query): void
    {
        $query->where('role', 'orang_tua')
            ->orWhereHas('roleRelation', fn($roleQuery) => $roleQuery->where('name', 'orang_tua'));
    }

    private function ensureParentInUserCabang(int $parentId): void
    {
        $userCabangId = $this->getUserCabangId();

        $exists = User::where('id', $parentId)
            ->where('cabang_id', $userCabangId)
            ->where('is_active', true)
            ->where(function ($query) {
                $this->scopeOrangTuaRole($query);
            })
            ->exists();

        if (!$exists) {
            abort(403, 'Wali siswa/wali harus berasal dari cabang Anda.');
        }
    }

    public function index(Request $request)
    {
        $userCabangId = $this->getUserCabangId();

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $taFilterId = $request->tahun_ajaran_id ?: ($tahunAjaranAktif?->id);
        $isHistorical = $taFilterId && $tahunAjaranAktif && $taFilterId != $tahunAjaranAktif->id;
        $filterNoKelas = !$isHistorical && $request->boolean('no_kelas');

        $query = Siswa::with(['user', 'cabang', 'kelas.tahunAjaran']);

        // Mandatory filter by user's assigned cabang
        $query->where('cabang_id', $userCabangId);

        // Filter by tahun ajaran via scope (mendukung snapshot historis)
        if ($taFilterId && !$filterNoKelas) {
            $query->forTahunAjaran($taFilterId);
            $query->with(['statusNaikKelas' => fn($q) => $q->where('tahun_ajaran_id', $taFilterId)]);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter by jenjang — hanya saat TA aktif
        if (!$isHistorical && !$filterNoKelas && $request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        // Filter by kelas — hanya saat TA aktif
        if (!$isHistorical && !$filterNoKelas && $request->filled('kelas_id')) {
            $kelasId = $request->kelas_id;
            if (is_array($kelasId)) {
                $query->whereIn('kelas_id', $kelasId);
            } else {
                $query->where('kelas_id', $kelasId);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$isHistorical) {
            $query->where('status', 'aktif');
        }

        // Filter no kelas — hanya saat TA aktif
        if ($filterNoKelas) {
            $query->whereNull('kelas_id');
        }

        $siswaList = $query->orderBy('nama_lengkap')->paginate(20)->withQueryString();

        // Data for filters
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $cabangs = Cabang::where('id', $userCabangId)->get(); // hanya cabang user
        $kelasList = Kelas::with('cabang')
            ->where('cabang_id', $userCabangId)
            ->when($taFilterId, fn($q) => $q->where('tahun_ajaran_id', $taFilterId))
            ->orderBy('jenjang')->orderBy('nama_kelas')->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Statistics — historis pakai snapshot, aktif pakai live
        if ($isHistorical) {
            $snapshotIds = StatusNaikKelasSiswa::where('tahun_ajaran_id', $taFilterId)
                ->whereHas('siswa', fn($q) => $q->where('cabang_id', $userCabangId))
                ->pluck('siswa_id');
            $stats = [
                'totalSiswa' => $snapshotIds->count(),
                'siswaWithKelas' => StatusNaikKelasSiswa::where('tahun_ajaran_id', $taFilterId)
                    ->whereIn('siswa_id', $snapshotIds)
                    ->whereNotNull('kelas_asal')->where('kelas_asal', '!=', '-')->count(),
                'siswaNoKelas' => 0,
                'siswaLaki' => Siswa::whereIn('id', $snapshotIds)->where('jenis_kelamin', 'L')->count(),
                'siswaPerempuan' => Siswa::whereIn('id', $snapshotIds)->where('jenis_kelamin', 'P')->count(),
            ];
        } else {
            $stats = [
                'totalSiswa' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->count(),
                'siswaWithKelas' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->whereNotNull('kelas_id')->count(),
                'siswaNoKelas' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->whereNull('kelas_id')->count(),
                'siswaLaki' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->where('jenis_kelamin', 'L')->count(),
                'siswaPerempuan' => Siswa::where('status', 'aktif')->where('cabang_id', $userCabangId)->where('jenis_kelamin', 'P')->count(),
            ];
        }

        return view('waka.manajemen-siswa.index', compact(
            'siswaList', 'tahunAjarans', 'tahunAjaranAktif', 'cabangs', 'kelasList',
            'jenjangs', 'stats', 'isHistorical', 'taFilterId'
        ));
    }

    public function show(Siswa $siswa)
    {
        $this->ensureSiswaInUserCabang($siswa);

        $userCabangId = $this->getUserCabangId();

        $siswa->load(['kelas.cabang', 'kelas.tahunAjaran', 'kelas.waliKelas', 'cabang', 'user', 'orangTua.studentParents.siswa']);

        // Get available classes with student count
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::with(['cabang', 'tahunAjaran'])
            ->withCount('siswa')
            ->where('cabang_id', $userCabangId)
            ->when($tahunAjaranAktif, fn($query) => $query->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        // Get available parents (orang_tua role yang belum terhubung dengan siswa ini)
        $currentParentIds = $siswa->orangTua->pluck('id')->toArray();
        $availableParents = User::where(function ($query) {
                $this->scopeOrangTuaRole($query);
            })
            ->where('cabang_id', $userCabangId)
            ->where('is_active', true)
            ->whereNotIn('id', $currentParentIds)
            ->with(['studentParents.siswa'])
            ->orderBy('name')
            ->get();

        return view('waka.manajemen-siswa.show', compact('siswa', 'kelasList', 'availableParents'));
    }

    public function assignKelas(Request $request, Siswa $siswa)
    {
        $this->ensureSiswaInUserCabang($siswa);

        $validated = $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        if ($validated['kelas_id']) {
            $kelas = Kelas::findOrFail($validated['kelas_id']);
            $this->ensureKelasInUserCabang($kelas);

            $currentCount = Siswa::where('kelas_id', $kelas->id)->where('id', '!=', $siswa->id)->count();
            if ($currentCount >= $kelas->kuota_siswa && (int) $siswa->kelas_id !== (int) $kelas->id) {
                return back()->with('error', 'Kelas sudah penuh, kuota tercapai');
            }
        }

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
        $this->ensureSiswaInUserCabang($siswa);

        if ($request->input('create_new_parent') === '1') {
            return $this->createAndAttachParent($request, $siswa);
        }

        $validated = $request->validate([
            'parent_id' => 'required|exists:users,id',
            'relationship' => 'required|in:ayah_kandung,ibu_kandung,ayah_tiri,ibu_tiri,kakek,nenek,paman,bibi,wali,lainnya',
            'is_primary' => 'nullable|boolean',
            'is_financial_responsible' => 'nullable|boolean',
            'can_access_academic' => 'nullable|boolean',
        ]);

        $this->ensureParentInUserCabang((int) $validated['parent_id']);

        // Check if parent already attached
        if ($siswa->parents()->where('parent_id', $validated['parent_id'])->exists()) {
            return redirect()->route('waka.manajemen-siswa.show', $siswa)
                ->with('error', 'Wali siswa sudah terhubung dengan siswa ini');
        }

        if (in_array($validated['relationship'], ['ayah_kandung', 'ibu_kandung'])) {
            $duplicateRelation = $siswa->parents()
                ->wherePivot('relationship', $validated['relationship'])
                ->exists();

            if ($duplicateRelation) {
                $relationLabel = $validated['relationship'] === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';
                return back()->with('error', "Siswa sudah memiliki {$relationLabel}.");
            }
        }

        // Attach parent with relationship
        $siswa->parents()->attach($validated['parent_id'], [
            'relationship' => $validated['relationship'],
            'is_primary' => $request->boolean('is_primary'),
            'is_financial_responsible' => $request->boolean('is_financial_responsible'),
            'can_access_academic' => $request->boolean('can_access_academic'),
        ]);

        return redirect()->route('waka.manajemen-siswa.show', $siswa)
            ->with('success', 'Wali siswa berhasil ditambahkan');
    }

    protected function createAndAttachParent(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'new_parent_name' => 'required|string|max:255',
            'new_parent_username' => 'required|string|max:255|unique:users,username',
            'new_parent_email' => 'required|email|max:255|unique:users,email',
            'new_parent_password' => 'required|string|min:8',
            'new_parent_phone' => 'nullable|string|max:20',
            'relationship' => 'required|in:ayah_kandung,ibu_kandung,ayah_tiri,ibu_tiri,kakek,nenek,paman,bibi,wali,lainnya',
            'is_primary' => 'nullable|boolean',
            'is_financial_responsible' => 'nullable|boolean',
            'can_access_academic' => 'nullable|boolean',
        ]);

        if (in_array($validated['relationship'], ['ayah_kandung', 'ibu_kandung'])) {
            $duplicateRelation = $siswa->parents()
                ->wherePivot('relationship', $validated['relationship'])
                ->exists();

            if ($duplicateRelation) {
                $relationLabel = $validated['relationship'] === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';
                return back()->with('error', "Siswa sudah memiliki {$relationLabel}.");
            }
        }

        $orangTuaRole = \App\Models\Role::where('name', 'orang_tua')->first();

        $parentData = [
            'name' => $validated['new_parent_name'],
            'username' => $validated['new_parent_username'],
            'email' => $validated['new_parent_email'],
            'password' => bcrypt($validated['new_parent_password']),
            'phone' => $validated['new_parent_phone'] ?? null,
            'cabang_id' => $siswa->cabang_id,
            'is_active' => true,
        ];

        if ($orangTuaRole) {
            $parentData['role_id'] = $orangTuaRole->id;
        } else {
            $parentData['role'] = 'orang_tua';
        }

        $parent = User::create($parentData);

        $siswa->parents()->attach($parent->id, [
            'relationship' => $validated['relationship'],
            'is_primary' => $request->boolean('is_primary'),
            'is_financial_responsible' => $request->boolean('is_financial_responsible'),
            'can_access_academic' => $request->boolean('can_access_academic'),
        ]);

        return back()->with('success', 'Akun wali siswa berhasil dibuat dan dihubungkan');
    }

    public function detachParent(Siswa $siswa, $parentId)
    {
        $this->ensureSiswaInUserCabang($siswa);
        $this->ensureParentInUserCabang((int) $parentId);

        // Detach the parent from the student
        $siswa->parents()->detach($parentId);

        return redirect()->route('waka.manajemen-siswa.show', $siswa)
            ->with('success', 'Hubungan dengan wali siswa berhasil dihapus');
    }

    public function print(Request $request)
    {
        $userCabangId = $this->getUserCabangId();
        $query = Siswa::with(['kelas.waliKelas', 'kelas.tahunAjaran', 'cabang']);

        // Samakan scope tahun ajaran dengan index(). Sebelumnya filter TA tidak ikut
        // di sini, sehingga hasil CETAK berbeda dari yang tampil di layar - mis. layar
        // menampilkan 0 siswa (TA baru belum diisi) tapi cetakan tetap keluar 88 siswa
        // dari TA lama.
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $taFilterId = $request->tahun_ajaran_id ?: ($tahunAjaranAktif?->id);
        $isHistorical = $taFilterId && $tahunAjaranAktif && $taFilterId != $tahunAjaranAktif->id;
        $filterNoKelas = ! $isHistorical && $request->boolean('no_kelas');
        if ($taFilterId && ! $filterNoKelas) {
            $query->forTahunAjaran($taFilterId);
        }

        // Apply same filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Mandatory filter by user's assigned cabang
        $query->where('cabang_id', $userCabangId);

        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        if ($request->filled('kelas_id')) {
            $kelasIds = (array) $request->kelas_id;
            $validKelasIds = Kelas::whereIn('id', $kelasIds)
                ->where('cabang_id', $userCabangId)
                ->pluck('id')
                ->all();

            $query->whereIn('kelas_id', $validKelasIds);
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
        } else {
            $siswaList = $query->orderBy('nama_lengkap')->get();
        }

        // Get specific kelas if filtered
        $kelas = null;
        if ($request->filled('kelas_id') && !is_array($request->kelas_id)) {
            $kelas = Kelas::with('waliKelas')
                ->where('cabang_id', $userCabangId)
                ->find($request->kelas_id);
        }
        $cabang = auth()->user()->cabang;

        return view('waka.manajemen-siswa.print', compact('siswaList', 'kelas', 'cabang', 'sortBy'));
    }

    public function printKartu(Siswa $siswa)
    {
        $this->ensureSiswaInUserCabang($siswa);

        $siswa->load(['kelas.tahunAjaran', 'cabang', 'studentParents.parent']);
        return view('waka.manajemen-siswa.print-kartu', compact('siswa'));
    }

    public function perKelas(Request $request, Kelas $kelas)
    {
        $this->ensureKelasInUserCabang($kelas);

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
        $this->ensureKelasInUserCabang($kelas);

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $this->ensureSiswaInUserCabang($siswa);

        if ($siswa->kelas_id) {
            return redirect()->route('waka.manajemen-siswa.per-kelas', $kelas)
                ->with('error', 'Siswa sudah memiliki kelas');
        }

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
        $this->ensureKelasInUserCabang($kelas);

        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::findOrFail($validated['siswa_id']);
        $this->ensureSiswaInUserCabang($siswa);

        if ((int) $siswa->kelas_id !== (int) $kelas->id) {
            return redirect()->route('waka.manajemen-siswa.per-kelas', $kelas)
                ->with('error', 'Siswa tidak berada di kelas ini');
        }

        $siswa->kelas_id = null;
        $siswa->save();

        return redirect()->route('waka.manajemen-siswa.per-kelas', $kelas)
            ->with('success', 'Siswa berhasil dikeluarkan dari kelas');
    }
}
