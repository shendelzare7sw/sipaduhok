<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenagaPendidik;
use App\Models\Siswa;
use App\Models\StudentParent;
use App\Models\Cabang;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $tenagaPendidik = TenagaPendidik::with('user')->latest()->take(10)->get();
        $siswa = Siswa::with('user', 'kelas')->latest()->take(10)->get();
        $orangTua = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'totalTenagaPendidik' => TenagaPendidik::count(),
            'totalSiswa' => Siswa::count(),
            'totalUserAktif' => User::where('is_active', true)->count(),
            'totalUserNonAktif' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('tenagaPendidik', 'siswa', 'orangTua', 'stats'));
    }

    // --- TENAGA PENDIDIK ---

    public function tenagaPendidik(Request $request)
    {
        $query = TenagaPendidik::with('user');

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Handle role filter
        if ($request->has('role') && $request->role != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        $tenagaPendidik = $query->paginate(15);

        // Available roles for filter
        $roles = [
            'ketua_pkbm' => 'Ketua PKBM',
            'wakil_kepala_sekolah' => 'Wakil Kepala Sekolah',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'wali_kelas' => 'Wali Kelas',
            'guru_pengajar' => 'Guru Pengajar',
        ];

        return view('admin.users.tenaga-pendidik', compact('tenagaPendidik', 'roles'));
    }

    public function createTenagaPendidik()
    {
        $cabangList = Cabang::where('is_active', true)->get();
        $roles = ['ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'];

        return view('admin.users.tenaga-pendidik-create', compact('cabangList', 'roles'));
    }

    public function storeTenagaPendidik(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:8',
            'role' => 'required|in:ketua_pkbm,wakil_kepala_sekolah,sekretaris,bendahara,wali_kelas,guru_pengajar',
            'cabang_id' => 'required|exists:cabang,id',
            'nip' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'pendidikan_terakhir' => 'required|string|max:100',
        ]);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'cabang_id' => $validated['cabang_id'],
            'is_active' => true,
        ]);

        TenagaPendidik::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'telepon' => $validated['telepon'],
            'email' => $validated['email'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
        ]);

        return redirect()->route('admin.users.tenaga-pendidik')->with('success', 'Tenaga Pendidik berhasil ditambahkan!');
    }

    public function editTenagaPendidik($id)
    {
        $tenagaPendidik = TenagaPendidik::with('user')->findOrFail($id);
        $cabangList = Cabang::where('is_active', true)->get();
        $roles = ['ketua_pkbm', 'wakil_kepala_sekolah', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'];

        return view('admin.users.tenaga-pendidik-edit', compact('tenagaPendidik', 'cabangList', 'roles'));
    }

    public function updateTenagaPendidik(Request $request, $id)
    {
        $tenagaPendidik = TenagaPendidik::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($tenagaPendidik->user_id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($tenagaPendidik->user_id)],
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:ketua_pkbm,wakil_kepala_sekolah,sekretaris,bendahara,wali_kelas,guru_pengajar',
            'cabang_id' => 'required|exists:cabang,id',
            'nip' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'telepon' => 'required|string|max:20',
            'pendidikan_terakhir' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $userData = [
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'role' => $validated['role'],
            'cabang_id' => $validated['cabang_id'],
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $tenagaPendidik->user->update($userData);

        $tenagaPendidik->update([
            'nip' => $validated['nip'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'telepon' => $validated['telepon'],
            'email' => $validated['email'],
            'pendidikan_terakhir' => $validated['pendidikan_terakhir'],
        ]);

        return redirect()->route('admin.users.tenaga-pendidik')->with('success', 'Tenaga Pendidik berhasil diupdate!');
    }

    public function showTenagaPendidik($id)
    {
        $tenagaPendidik = TenagaPendidik::with(['user.cabang'])->where('user_id', $id)->first();
        if (!$tenagaPendidik) {
            $tenagaPendidik = TenagaPendidik::with(['user.cabang'])->find($id);
        }

        if (!$tenagaPendidik) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('admin.users.tenaga-pendidik-show', compact('tenagaPendidik'));
    }

    public function deleteTenagaPendidik($id)
    {
        $tenagaPendidik = TenagaPendidik::findOrFail($id);
        $user = $tenagaPendidik->user;
        $tenagaPendidik->delete();
        $user->delete();

        return redirect()->route('admin.users.tenaga-pendidik')->with('success', 'Tenaga Pendidik berhasil dihapus!');
    }

    // --- SISWA ---

    public function siswa(Request $request)
    {
        $query = Siswa::with('user', 'kelas.tahunAjaran', 'cabang');

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Handle other filters
        if ($request->has('jenjang') && $request->jenjang != '') {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->where('cabang_id', $request->cabang_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $siswa = $query->orderBy('nama_lengkap')->paginate(15);
        $kelasList = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();
        $cabangList = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.users.siswa', compact('siswa', 'kelasList', 'cabangList', 'jenjangs'));
    }

    public function createSiswa()
    {
        $cabangList = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::all();
        $orangTuaList = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa'])
            ->orderBy('name')
            ->get();

        return view('admin.users.siswa-create', compact('cabangList', 'kelasList', 'orangTuaList'));
    }

    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:8',
            'cabang_id' => 'required|exists:cabang,id',
            'kelas_id' => 'required|exists:kelas,id',
            'nisn' => 'required|string|max:20',
            'nis' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'telepon_orangtua' => 'nullable|string|max:20',
            'tanggal_masuk' => 'required|date',
            'parent_option' => 'nullable|in:existing,new,none',
            'parent_id' => 'required_if:parent_option,existing|exists:users,id',
            'existing_relationship' => 'required_if:parent_option,existing|string',
            'existing_relationship_lainnya' => 'nullable|string|max:100',
            'parent_name' => 'required_if:parent_option,new|string|max:255',
            'parent_username' => 'required_if:parent_option,new|string|unique:users,username|max:50',
            'parent_email' => 'required_if:parent_option,new|email|unique:users,email',
            'parent_password' => 'required_if:parent_option,new|string|min:8',
            'parent_phone' => 'nullable|string|max:20',
            'new_relationship' => 'required_if:parent_option,new|string',
            'new_relationship_lainnya' => 'nullable|string|max:100',
            'is_primary' => 'nullable|boolean',
            'can_access_academic' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? $validated['username'] . '@siswa.sipaduhok.sch.id',
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
            'cabang_id' => $validated['cabang_id'],
            'is_active' => true,
        ]);

        $siswa = Siswa::create([
            'user_id' => $user->id,
            'cabang_id' => $validated['cabang_id'],
            'kelas_id' => $validated['kelas_id'],
            'nisn' => $validated['nisn'],
            'nis' => $validated['nis'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'nama_ayah' => $validated['nama_ayah'],
            'nama_ibu' => $validated['nama_ibu'],
            'telepon_orangtua' => $validated['telepon_orangtua'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'status' => 'aktif',
        ]);

        // Handle parent assignment
        if ($request->parent_option === 'existing' && $request->parent_id) {
            // Determine the actual relationship value
            $existingRelationship = $request->existing_relationship;
            if ($existingRelationship === 'lainnya' && !empty($request->existing_relationship_lainnya)) {
                $existingRelationship = $request->existing_relationship_lainnya;
            }

            // Link to existing parent
            StudentParent::create([
                'siswa_id' => $siswa->id,
                'parent_id' => $request->parent_id,
                'relationship' => $existingRelationship,
                'is_primary' => true,
                'is_financial_responsible' => true,
                'can_access_academic' => true,
            ]);
        } elseif ($request->parent_option === 'new') {
            // Determine the actual relationship value
            $newRelationship = $request->new_relationship;
            if ($newRelationship === 'lainnya' && !empty($request->new_relationship_lainnya)) {
                $newRelationship = $request->new_relationship_lainnya;
            }

            // Create new parent account
            $parentUser = User::create([
                'name' => $request->parent_name,
                'email' => $request->parent_email,
                'username' => $request->parent_username,
                'password' => Hash::make($request->parent_password),
                'phone' => $request->parent_phone,
                'role' => 'orang_tua',
                'is_active' => true,
            ]);

            // Link parent to student
            StudentParent::create([
                'siswa_id' => $siswa->id,
                'parent_id' => $parentUser->id,
                'relationship' => $newRelationship,
                'is_primary' => $request->has('is_primary'),
                'is_financial_responsible' => true,
                'can_access_academic' => $request->has('can_access_academic'),
            ]);
        }

        return redirect()->route('admin.users.siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function editSiswa($id)
    {
        $siswa = Siswa::with(['user', 'studentParents.parent'])->findOrFail($id);
        $cabangList = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::all();
        $orangTuaList = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa'])
            ->orderBy('name')
            ->get();

        return view('admin.users.siswa-edit', compact('siswa', 'cabangList', 'kelasList', 'orangTuaList'));
    }

    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($siswa->user_id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($siswa->user_id)],
            'password' => 'nullable|string|min:8',
            'cabang_id' => 'required|exists:cabang,id',
            'kelas_id' => 'required|exists:kelas,id',
            'nisn' => 'required|string|max:20',
            'nis' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'telepon_orangtua' => 'nullable|string|max:20',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
            'is_active' => 'required|boolean',
            'remove_parents' => 'nullable|array',
            'add_parent_option' => 'nullable|in:existing,new',
            'add_existing_parent_id' => 'nullable|exists:users,id',
            'add_existing_relationship' => 'nullable|string',
            'add_existing_relationship_lainnya' => 'nullable|string|max:100',
            'add_new_parent_name' => 'nullable|string|max:255',
            'add_new_parent_username' => 'nullable|string|unique:users,username|max:50',
            'add_new_parent_email' => 'nullable|email|unique:users,email',
            'add_new_parent_password' => 'nullable|string|min:8',
            'add_new_relationship' => 'nullable|string',
            'add_new_relationship_lainnya' => 'nullable|string|max:100',
        ]);

        $userData = [
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'] ?? $siswa->user->email,
            'username' => $validated['username'],
            'cabang_id' => $validated['cabang_id'],
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $siswa->user->update($userData);

        $siswa->update([
            'cabang_id' => $validated['cabang_id'],
            'kelas_id' => $validated['kelas_id'],
            'nisn' => $validated['nisn'],
            'nis' => $validated['nis'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'nama_ayah' => $validated['nama_ayah'],
            'nama_ibu' => $validated['nama_ibu'],
            'telepon_orangtua' => $validated['telepon_orangtua'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'status' => $validated['status'],
        ]);

        // Handle removing parent relationships
        if ($request->has('remove_parents')) {
            $removeIds = array_filter($request->remove_parents);
            if (!empty($removeIds)) {
                StudentParent::whereIn('id', $removeIds)->delete();
            }
        }

        // Handle adding existing parent
        if ($request->add_parent_option === 'existing' && $request->add_existing_parent_id) {
            // Check if relationship already exists
            $exists = StudentParent::where('siswa_id', $siswa->id)
                ->where('parent_id', $request->add_existing_parent_id)
                ->exists();

            if (!$exists) {
                // Determine the actual relationship value
                $addExistingRelationship = $request->add_existing_relationship;
                if ($addExistingRelationship === 'lainnya' && !empty($request->add_existing_relationship_lainnya)) {
                    $addExistingRelationship = $request->add_existing_relationship_lainnya;
                }

                // Check for duplicate ayah_kandung or ibu_kandung
                if (in_array($addExistingRelationship, ['ayah_kandung', 'ibu_kandung'])) {
                    $duplicateRelation = StudentParent::where('siswa_id', $siswa->id)
                        ->where('relationship', $addExistingRelationship)
                        ->exists();

                    if ($duplicateRelation) {
                        $relationLabel = $addExistingRelationship === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';
                        return back()->with('error', "Siswa sudah memiliki {$relationLabel}! Satu siswa hanya boleh memiliki 1 Ayah Kandung dan 1 Ibu Kandung.");
                    }
                }

                StudentParent::create([
                    'siswa_id' => $siswa->id,
                    'parent_id' => $request->add_existing_parent_id,
                    'relationship' => $addExistingRelationship,
                    'is_primary' => false,
                    'is_financial_responsible' => true,
                    'can_access_academic' => true,
                ]);
            }
        }

        // Handle adding new parent
        if ($request->add_parent_option === 'new' && $request->add_new_parent_name) {
            // Determine the actual relationship value
            $addNewRelationship = $request->add_new_relationship;
            if ($addNewRelationship === 'lainnya' && !empty($request->add_new_relationship_lainnya)) {
                $addNewRelationship = $request->add_new_relationship_lainnya;
            }

            // Check for duplicate ayah_kandung or ibu_kandung
            if (in_array($addNewRelationship, ['ayah_kandung', 'ibu_kandung'])) {
                $duplicateRelation = StudentParent::where('siswa_id', $siswa->id)
                    ->where('relationship', $addNewRelationship)
                    ->exists();

                if ($duplicateRelation) {
                    $relationLabel = $addNewRelationship === 'ayah_kandung' ? 'Ayah Kandung' : 'Ibu Kandung';
                    return back()->with('error', "Siswa sudah memiliki {$relationLabel}! Satu siswa hanya boleh memiliki 1 Ayah Kandung dan 1 Ibu Kandung.");
                }
            }

            $parentUser = User::create([
                'name' => $request->add_new_parent_name,
                'email' => $request->add_new_parent_email,
                'username' => $request->add_new_parent_username,
                'password' => Hash::make($request->add_new_parent_password),
                'phone' => $request->add_new_parent_phone,
                'role' => 'orang_tua',
                'is_active' => true,
            ]);

            StudentParent::create([
                'siswa_id' => $siswa->id,
                'parent_id' => $parentUser->id,
                'relationship' => $addNewRelationship,
                'is_primary' => false,
                'is_financial_responsible' => true,
                'can_access_academic' => true,
            ]);
        }

        return redirect()->route('admin.users.siswa')->with('success', 'Siswa berhasil diupdate!');
    }

    public function showSiswa($id)
    {
        $siswa = Siswa::with(['user', 'kelas', 'cabang', 'studentParents.parent'])->where('id', $id)->first();
        if (!$siswa) {
            $siswa = Siswa::with(['user', 'kelas', 'cabang', 'studentParents.parent'])->where('user_id', $id)->first();
        }

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        return view('admin.users.siswa-show', compact('siswa'));
    }

    public function deleteSiswa($id)
    {
        $siswa = Siswa::findOrFail($id);
        $user = $siswa->user;
        $siswa->delete();
        $user->delete();

        return redirect()->route('admin.users.siswa')->with('success', 'Siswa berhasil dihapus!');
    }

    // --- ORANG TUA ---

    public function orangTua(Request $request)
    {
        $query = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas', 'studentParents.siswa.cabang']);

        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Handle filters
        if ($request->has('jenjang') && $request->jenjang != '') {
            $query->whereHas('studentParents.siswa.kelas', function ($q) use ($request) {
                $q->where('jenjang', $request->jenjang);
            });
        }

        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->whereHas('studentParents.siswa', function ($q) use ($request) {
                $q->where('cabang_id', $request->cabang_id);
            });
        }

        if ($request->has('status') && $request->status != '') {
            $isActive = $request->status === 'aktif';
            $query->where('is_active', $isActive);
        }

        $orangTua = $query->orderBy('name')->paginate(15);
        $cabangList = Cabang::where('is_active', true)->get();
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.users.orang-tua', compact('orangTua', 'cabangList', 'jenjangs'));
    }

    public function createOrangTua()
    {
        // Get all active siswa for optional linking
        $siswaList = Siswa::with(['kelas', 'cabang', 'user'])
            ->whereHas('user', function ($q) {
                $q->where('is_active', true);
            })
            ->get()
            ->sortBy(function ($siswa) {
                return $siswa->user->name ?? $siswa->nama_lengkap;
            });

        // Get unique cabang list from siswa
        $cabangList = \App\Models\Cabang::orderBy('nama_cabang')->get();

        // Get unique kelas list from siswa
        $kelasList = \App\Models\Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();

        // Jenjang list
        $jenjangs = ['KB', 'TKA', 'TKB', 'SD', 'SMP', 'SMA'];

        return view('admin.users.orang-tua-create', compact('siswaList', 'cabangList', 'kelasList', 'jenjangs'));
    }

    public function storeOrangTua(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
            'hubungan_keluarga' => 'required_with:siswa_ids|nullable|string|max:50',
            'hubungan_keluarga_lainnya' => 'nullable|string|max:100',
        ]);

        // Create parent user account
        $orangTua = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'role' => 'orang_tua',
            'is_active' => true,
        ]);

        // Link to students if selected
        if (!empty($validated['siswa_ids']) && !empty($validated['hubungan_keluarga'])) {
            // Determine the actual relationship value
            $relationship = $validated['hubungan_keluarga'];
            if ($relationship === 'lainnya' && !empty($validated['hubungan_keluarga_lainnya'])) {
                $relationship = $validated['hubungan_keluarga_lainnya'];
            }

            foreach ($validated['siswa_ids'] as $siswaId) {
                StudentParent::create([
                    'siswa_id' => $siswaId,
                    'parent_id' => $orangTua->id,
                    'relationship' => $relationship,
                    'is_primary' => false,
                    'is_financial_responsible' => true,
                    'can_access_academic' => true,
                ]);
            }
        }

        return redirect()->route('admin.users.orang-tua')
            ->with('success', 'Akun orang tua berhasil dibuat!');
    }

    public function toggleOrangTuaStatus($id)
    {
        $user = User::where('role', 'orang_tua')->findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.users.orang-tua')->with('success', "Akun orang tua berhasil {$status}!");
    }

    public function showOrangTua($id)
    {
        $orangTua = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas.cabang', 'studentParents.siswa.cabang'])
            ->findOrFail($id);

        return view('admin.users.orang-tua-show', compact('orangTua'));
    }

    public function editOrangTua($id)
    {
        $orangTua = User::where('role', 'orang_tua')
            ->with(['studentParents.siswa.kelas'])
            ->findOrFail($id);

        return view('admin.users.orang-tua-edit', compact('orangTua'));
    }

    public function updateOrangTua(Request $request, $id)
    {
        $orangTua = User::where('role', 'orang_tua')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($orangTua->id)
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($orangTua->id)
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'is_active' => 'required|boolean',
            'relationships' => 'nullable|array',
            'relationships.*' => 'nullable|string|max:100',
            'relationships_lainnya' => 'nullable|array',
            'relationships_lainnya.*' => 'nullable|string|max:100',
        ]);

        // Update user data
        $orangTua->name = $validated['name'];
        $orangTua->username = $validated['username'];
        $orangTua->email = $validated['email'];
        $orangTua->phone = $validated['phone'];
        $orangTua->is_active = $validated['is_active'];

        // Update password only if provided
        if ($request->filled('password')) {
            $orangTua->password = Hash::make($validated['password']);
        }

        $orangTua->save();

        // Update relationships if provided
        if ($request->has('relationships')) {
            foreach ($request->relationships as $studentParentId => $relationship) {
                if (!empty($relationship)) {
                    // Determine the actual relationship value
                    $actualRelationship = $relationship;
                    if ($relationship === 'lainnya' && isset($request->relationships_lainnya[$studentParentId])) {
                        $actualRelationship = $request->relationships_lainnya[$studentParentId];
                    }

                    StudentParent::where('id', $studentParentId)
                        ->where('parent_id', $orangTua->id)
                        ->update(['relationship' => $actualRelationship]);
                }
            }
        }

        return redirect()->route('admin.users.show-orang-tua', $orangTua->id)
            ->with('success', 'Data orang tua berhasil diperbarui!');
    }

    public function deleteOrangTua($id)
    {
        $user = User::where('role', 'orang_tua')->findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.orang-tua')->with('success', 'Akun orang tua berhasil dihapus!');
    }

    // --- IMPORT SISWA ---

    public function importSiswaForm()
    {
        return view('admin.users.siswa-import');
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\SiswaImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();

            $message = "Berhasil mengimport {$imported} siswa.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (sudah ada).";
            }

            return redirect()->route('admin.users.siswa')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: ' . $e->getMessage());
        }
    }

    public function downloadSiswaTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Templates\SiswaTemplate(), 'template_siswa.xlsx');
    }

    // --- IMPORT TENAGA PENDIDIK ---

    public function importTenagaPendidikForm()
    {
        return view('admin.users.tenaga-pendidik-import');
    }

    public function importTenagaPendidik(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\TenagaPendidikImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();

            $message = "Berhasil mengimport {$imported} tenaga pendidik.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (sudah ada).";
            }

            return redirect()->route('admin.users.tenaga-pendidik')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: ' . $e->getMessage());
        }
    }

    public function downloadTenagaPendidikTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Templates\TenagaPendidikTemplate(), 'template_tenaga_pendidik.xlsx');
    }

    // --- IMPORT ORANG TUA ---

    public function importOrangTuaForm()
    {
        return view('admin.users.orang-tua-import');
    }

    public function importOrangTua(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:5120',
        ]);

        try {
            $import = new \App\Imports\OrangTuaImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

            $imported = $import->getImportedCount();
            $skipped = $import->getSkippedCount();

            $message = "Berhasil mengimport {$imported} orang tua.";
            if ($skipped > 0) {
                $message .= " {$skipped} data dilewati (sudah ada).";
            }

            return redirect()->route('admin.users.orang-tua')->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimport: ' . $e->getMessage());
        }
    }

    public function downloadOrangTuaTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\Templates\OrangTuaTemplate(), 'template_orang_tua.xlsx');
    }
}