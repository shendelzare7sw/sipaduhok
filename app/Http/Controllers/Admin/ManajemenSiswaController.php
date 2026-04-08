<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Cabang;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ManajemenSiswaController extends Controller
{
    /**
     * Display a listing of siswa.
     */
    public function index(Request $request)
    {
        $query = Siswa::with(['user', 'cabang', 'kelas.tahunAjaran']);

        // Filter by tahun ajaran (via kelas)
        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('kelas', fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id));
        }

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter by kelas
        if ($request->filled('kelas_id')) {
            $kelasId = $request->kelas_id;
            if (is_array($kelasId)) {
                $query->whereIn('kelas_id', $kelasId);
            } else {
                $query->where('kelas_id', $kelasId);
            }
        }

        // Filter by jenjang (via kelas)
        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'aktif'); // Default: hanya siswa aktif
        }

        // Filter belum punya kelas
        if ($request->filled('no_kelas') && $request->no_kelas == '1') {
            $query->whereNull('kelas_id');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswaList = $query->orderBy('nama_lengkap')->paginate(20);

        // Data untuk filter
        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $cabangs = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::with('cabang')
            ->when($request->tahun_ajaran_id, fn($q) => $q->where('tahun_ajaran_id', $request->tahun_ajaran_id))
            ->when(!$request->tahun_ajaran_id && $tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->orderBy('jenjang')->orderBy('nama_kelas')->get();

        // Jenjang options
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        // Statistics
        $stats = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'siswaWithKelas' => Siswa::where('status', 'aktif')->whereNotNull('kelas_id')->count(),
            'siswaNoKelas' => Siswa::where('status', 'aktif')->whereNull('kelas_id')->count(),
            'siswaLaki' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'siswaPerempuan' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
        ];

        return view('admin.manajemen-siswa.index', compact(
            'siswaList',
            'tahunAjarans',
            'tahunAjaranAktif',
            'cabangs',
            'kelasList',
            'jenjangs',
            'stats'
        ));
    }

    /**
     * Show siswa detail.
     */
    public function show(Siswa $siswa)
    {
        $siswa->load([
            'user',
            'cabang',
            'kelas.tahunAjaran',
            'kelas.waliKelas',
            'orangTua' // Load relasi orang tua
        ]);

        // Get available kelas for reassignment
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::with('cabang')
            ->when($tahunAjaranAktif, fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
            ->withCount('siswa')
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        // Get available parent users (role orang_tua) - support both old enum and new role_id system
        $availableParents = \App\Models\User::where(function ($query) {
            // Old role enum system
            $query->where('role', 'orang_tua')
                // OR new role_id system
                ->orWhereHas('roleRelation', function ($q) {
                    $q->where('name', 'orang_tua');
                });
        })
            ->where('is_active', true)
            ->with(['studentParents.siswa'])
            ->orderBy('name')
            ->get();

        return view('admin.manajemen-siswa.show', compact('siswa', 'kelasList', 'tahunAjaranAktif', 'availableParents'));
    }

    /**
     * Assign siswa ke kelas.
     */
    public function assignKelas(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $oldKelas = $siswa->kelas;
        $siswa->update(['kelas_id' => $validated['kelas_id']]);

        if ($validated['kelas_id']) {
            // Notify siswa and orang tua about class assignment
            $siswa->refresh()->load('kelas');
            $notificationService = app(NotificationService::class);
            $notificationService->notifyPlottingSiswa($siswa);

            $newKelas = Kelas::find($validated['kelas_id']);
            return back()->with('success', "Siswa {$siswa->nama_lengkap} berhasil dipindahkan ke kelas {$newKelas->nama_kelas}!");
        } else {
            return back()->with('success', "Siswa {$siswa->nama_lengkap} berhasil dikeluarkan dari kelas!");
        }
    }

    /**
     * Bulk assign siswa ke kelas.
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $kelas = Kelas::find($validated['kelas_id']);

        // Check kuota
        $currentCount = Siswa::where('kelas_id', $kelas->id)->count();
        $newCount = count($validated['siswa_ids']);

        if (($currentCount + $newCount) > $kelas->kuota_siswa) {
            return back()->with('error', "Kuota kelas {$kelas->nama_kelas} tidak mencukupi! (Sisa: " . ($kelas->kuota_siswa - $currentCount) . " siswa)");
        }

        Siswa::whereIn('id', $validated['siswa_ids'])->update(['kelas_id' => $validated['kelas_id']]);

        // Notify each siswa and orang tua about class assignment
        $notificationService = app(NotificationService::class);
        $siswaList = Siswa::whereIn('id', $validated['siswa_ids'])->with('kelas')->get();
        foreach ($siswaList as $siswa) {
            $notificationService->notifyPlottingSiswa($siswa);
        }

        return back()->with('success', "Berhasil memindahkan {$newCount} siswa ke kelas {$kelas->nama_kelas}!");
    }

    /**
     * Attach parent (orang tua) to siswa.
     * Supports both selecting existing parent and creating new parent.
     */
    public function attachParent(Request $request, Siswa $siswa)
    {
        // Check if creating new parent
        if ($request->has('create_new_parent') && $request->create_new_parent == '1') {
            return $this->createAndAttachParent($request, $siswa);
        }

        // Attach existing parent
        $validated = $request->validate([
            'parent_id' => 'required|exists:users,id',
            'relationship' => 'required|in:ayah_kandung,ibu_kandung,ayah_tiri,ibu_tiri,kakek,nenek,paman,bibi,wali,lainnya',
            'is_primary' => 'nullable|boolean',
            'is_financial_responsible' => 'nullable|boolean',
            'can_access_academic' => 'nullable|boolean',
        ]);

        // Check if already attached
        if ($siswa->parents()->where('parent_id', $validated['parent_id'])->exists()) {
            return back()->with('error', 'Orang tua ini sudah terhubung dengan siswa!');
        }

        // Check for duplicate ayah_kandung or ibu_kandung
        if (in_array($validated['relationship'], ['ayah_kandung', 'ibu_kandung'])) {
            $duplicateRelation = $siswa->parents()
                ->wherePivot('relationship', $validated['relationship'])
                ->exists();

            if ($duplicateRelation) {
                $relationLabel = $validated['relationship'] === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';
                return back()->with('error', "Siswa sudah memiliki {$relationLabel}! Satu siswa hanya boleh memiliki 1 Ayah Kandung dan 1 Ibu Kandung.");
            }
        }

        $siswa->parents()->attach($validated['parent_id'], [
            'relationship' => $validated['relationship'],
            'is_primary' => $request->has('is_primary'),
            'is_financial_responsible' => $request->has('is_financial_responsible'),
            'can_access_academic' => $request->has('can_access_academic'),
        ]);

        $parent = \App\Models\User::find($validated['parent_id']);
        $relationLabel = ucwords(str_replace('_', ' ', $validated['relationship']));
        return back()->with('success', "Berhasil menghubungkan {$parent->name} sebagai {$relationLabel}!");
    }

    /**
     * Create new parent account and attach to siswa.
     */
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

        // Check for duplicate ayah_kandung or ibu_kandung
        if (in_array($validated['relationship'], ['ayah_kandung', 'ibu_kandung'])) {
            $duplicateRelation = $siswa->parents()
                ->wherePivot('relationship', $validated['relationship'])
                ->exists();

            if ($duplicateRelation) {
                $relationLabel = $validated['relationship'] === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';
                return back()->with('error', "Siswa sudah memiliki {$relationLabel}! Satu siswa hanya boleh memiliki 1 Ayah Kandung dan 1 Ibu Kandung.");
            }
        }

        // Get orang_tua role - support both old and new role system
        $orangTuaRole = \App\Models\Role::where('name', 'orang_tua')->first();

        // Prepare user data
        $userData = [
            'name' => $validated['new_parent_name'],
            'username' => $validated['new_parent_username'],
            'email' => $validated['new_parent_email'],
            'password' => bcrypt($validated['new_parent_password']),
            'cabang_id' => $siswa->cabang_id,
            'is_active' => true,
        ];

        // Use new role_id if available, otherwise use old role enum
        if ($orangTuaRole) {
            $userData['role_id'] = $orangTuaRole->id;
        } else {
            $userData['role'] = 'orang_tua';
        }

        // Create new user
        $newParent = \App\Models\User::create($userData);

        // Attach parent to siswa
        $siswa->parents()->attach($newParent->id, [
            'relationship' => $validated['relationship'],
            'is_primary' => $request->has('is_primary'),
            'is_financial_responsible' => $request->has('is_financial_responsible'),
            'can_access_academic' => $request->has('can_access_academic'),
        ]);

        $relationLabel = ucwords(str_replace('_', ' ', $validated['relationship']));
        return back()->with('success', "Berhasil membuat akun dan menghubungkan {$newParent->name} sebagai {$relationLabel}!");
    }

    /**
     * Detach parent (orang tua) from siswa.
     */
    public function detachParent(Siswa $siswa, \App\Models\User $parent)
    {
        $siswa->parents()->detach($parent->id);
        return back()->with('success', "Berhasil menghapus hubungan dengan {$parent->name}!");
    }

    /**
     * Print daftar siswa.
     */
    public function print(Request $request)
    {
        $query = Siswa::with(['cabang', 'kelas']);

        // Filter
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('jenjang')) {
            $query->whereHas('kelas', fn($q) => $q->where('jenjang', $request->jenjang));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'aktif');
        }

        // Sort
        $sortBy = $request->sort_by ?? 'nama';
        switch ($sortBy) {
            case 'kelas':
                $query->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
                    ->orderBy('kelas.jenjang')
                    ->orderBy('kelas.nama_kelas')
                    ->orderBy('siswa.nama_lengkap')
                    ->select('siswa.*');
                break;
            case 'cabang':
                $query->orderBy('cabang_id')->orderBy('nama_lengkap');
                break;
            default: // nama (abjad)
                $query->orderBy('nama_lengkap');
        }

        $siswaList = $query->get();

        // Get filter info for title
        $kelas = $request->kelas_id ? Kelas::find($request->kelas_id) : null;
        $cabang = $request->cabang_id ? Cabang::find($request->cabang_id) : null;

        return view('admin.manajemen-siswa.print', compact('siswaList', 'kelas', 'cabang', 'sortBy'));
    }

    /**
     * Print kartu siswa.
     */
    public function printKartu(Siswa $siswa)
    {
        $siswa->load(['cabang', 'kelas.tahunAjaran', 'studentParents.parent']);
        return view('admin.manajemen-siswa.print-kartu', compact('siswa'));
    }

    /**
     * Manage siswa per kelas (view from kelas perspective).
     */
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

        return view('admin.manajemen-siswa.per-kelas', compact('kelas', 'siswaList', 'availableSiswa', 'stats'));
    }

    /**
     * Add siswa to kelas.
     */
    public function addToKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        // Check kuota
        $currentCount = Siswa::where('kelas_id', $kelas->id)->count();
        if ($currentCount >= $kelas->kuota_siswa) {
            return back()->with('error', 'Kuota kelas sudah penuh!');
        }

        $siswa = Siswa::find($validated['siswa_id']);
        $siswa->update(['kelas_id' => $kelas->id]);

        return back()->with('success', "Siswa {$siswa->nama_lengkap} berhasil ditambahkan ke kelas!");
    }

    /**
     * Remove siswa from kelas.
     */
    public function removeFromKelas(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
        ]);

        $siswa = Siswa::find($validated['siswa_id']);

        if ($siswa->kelas_id == $kelas->id) {
            $siswa->update(['kelas_id' => null]);
            return back()->with('success', "Siswa {$siswa->nama_lengkap} berhasil dikeluarkan dari kelas!");
        }

        return back()->with('error', 'Siswa tidak ditemukan di kelas ini!');
    }
}
