<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TenagaPendidik;
use App\Models\Siswa;
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
        
        $stats = [
            'totalTenagaPendidik' => TenagaPendidik::count(),
            'totalSiswa' => Siswa::count(),
            'totalUserAktif' => User::where('is_active', true)->count(),
            'totalUserNonAktif' => User::where('is_active', false)->count(),
        ];

        return view('admin.users.index', compact('tenagaPendidik', 'siswa', 'stats'));
    }

    // --- TENAGA PENDIDIK ---

    public function tenagaPendidik(Request $request)
    {
        $query = TenagaPendidik::with('user');
        
        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $tenagaPendidik = $query->paginate(15);
        return view('admin.users.tenaga-pendidik', compact('tenagaPendidik'));
    }

    public function createTenagaPendidik()
    {
        $cabangList = Cabang::where('is_active', true)->get();
        $roles = ['ketua_pkbm', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'];
        
        return view('admin.users.tenaga-pendidik-create', compact('cabangList', 'roles'));
    }

    public function storeTenagaPendidik(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:8',
            'role' => 'required|in:ketua_pkbm,sekretaris,bendahara,wali_kelas,guru_pengajar',
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
        $roles = ['ketua_pkbm', 'sekretaris', 'bendahara', 'wali_kelas', 'guru_pengajar'];
        
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
            'role' => 'required|in:ketua_pkbm,sekretaris,bendahara,wali_kelas,guru_pengajar',
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
        $query = Siswa::with('user', 'kelas', 'cabang');
        
        // Handle search parameter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        
        // Handle other filters
        if ($request->has('kelas_id') && $request->kelas_id != '') {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->has('cabang_id') && $request->cabang_id != '') {
            $query->where('cabang_id', $request->cabang_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $siswa = $query->paginate(15);
        $kelasList = Kelas::all();
        $cabangList = Cabang::where('is_active', true)->get();
        
        return view('admin.users.siswa', compact('siswa', 'kelasList', 'cabangList'));
    }

    public function createSiswa()
    {
        $cabangList = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::all();
        
        return view('admin.users.siswa-create', compact('cabangList', 'kelasList'));
    }

    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
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
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'telepon_orangtua' => 'required|string|max:20',
            'tanggal_masuk' => 'required|date',
        ]);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role' => 'siswa',
            'cabang_id' => $validated['cabang_id'],
            'is_active' => true,
        ]);

        Siswa::create([
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

        return redirect()->route('admin.users.siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function editSiswa($id)
    {
        $siswa = Siswa::with('user')->findOrFail($id);
        $cabangList = Cabang::where('is_active', true)->get();
        $kelasList = Kelas::all();
        
        return view('admin.users.siswa-edit', compact('siswa', 'cabangList', 'kelasList'));
    }

    public function updateSiswa(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($siswa->user_id)],
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
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'telepon_orangtua' => 'required|string|max:20',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:aktif,lulus,pindah,keluar',
            'is_active' => 'required|boolean',
        ]);

        $userData = [
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
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

        return redirect()->route('admin.users.siswa')->with('success', 'Siswa berhasil diupdate!');
    }

    public function showSiswa($id)
    {
        $siswa = Siswa::with(['user', 'kelas', 'cabang'])->where('id', $id)->first();
        if (!$siswa) {
            $siswa = Siswa::with(['user', 'kelas', 'cabang'])->where('user_id', $id)->first();
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
}