<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cabang::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_cabang', 'like', "%{$search}%")
                  ->orWhere('kode_cabang', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'aktif');
        }
        
        $cabangs = $query->withCount(['siswa', 'kelas', 'users'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Statistics
        $stats = [
            'totalCabang' => Cabang::count(),
            'cabangAktif' => Cabang::where('is_active', true)->count(),
            'cabangNonAktif' => Cabang::where('is_active', false)->count(),
            'totalSiswaSemuaCabang' => Siswa::count(),
        ];
        
        return view('admin.cabang.index', compact('cabangs', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cabang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_cabang' => 'required|string|max:10|unique:cabang,kode_cabang|alpha_num',
            'nama_cabang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ], [
            'kode_cabang.required' => 'Kode cabang harus diisi',
            'kode_cabang.unique' => 'Kode cabang sudah digunakan',
            'kode_cabang.max' => 'Kode cabang maksimal 10 karakter',
            'kode_cabang.alpha_num' => 'Kode cabang hanya boleh berisi huruf dan angka',
            'nama_cabang.required' => 'Nama cabang harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        // Convert kode_cabang to uppercase
        $validated['kode_cabang'] = strtoupper($validated['kode_cabang']);
        $validated['is_active'] = $request->has('is_active');

        Cabang::create($validated);

        return redirect()->route('admin.cabang.index')
            ->with('success', 'Cabang berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cabang $cabang)
    {
        // Load related data with counts
        $cabang->loadCount(['siswa', 'kelas', 'users']);
        
        // Get siswa in this cabang with pagination
        $siswa = Siswa::with('kelas')
            ->where('cabang_id', $cabang->id)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->paginate(10, ['*'], 'siswa_page');
        
        // Get kelas in this cabang
        $kelas = Kelas::with(['tahunAjaran', 'waliKelas'])
            ->where('cabang_id', $cabang->id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();
        
        // Get users/tenaga pendidik in this cabang
        $users = User::where('cabang_id', $cabang->id)
            ->where('role', '!=', 'siswa')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Statistics for this cabang
        $stats = [
            'totalSiswa' => Siswa::where('cabang_id', $cabang->id)->count(),
            'siswaAktif' => Siswa::where('cabang_id', $cabang->id)->where('status', 'aktif')->count(),
            'totalKelas' => Kelas::where('cabang_id', $cabang->id)->count(),
            'totalTenagaPendidik' => User::where('cabang_id', $cabang->id)
                ->where('role', '!=', 'siswa')
                ->where('is_active', true)
                ->count(),
        ];
        
        return view('admin.cabang.show', compact('cabang', 'siswa', 'kelas', 'users', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cabang $cabang)
    {
        return view('admin.cabang.edit', compact('cabang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cabang $cabang)
    {
        $validated = $request->validate([
            'kode_cabang' => 'required|string|max:10|alpha_num|unique:cabang,kode_cabang,' . $cabang->id,
            'nama_cabang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telepon' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ], [
            'kode_cabang.required' => 'Kode cabang harus diisi',
            'kode_cabang.unique' => 'Kode cabang sudah digunakan',
            'kode_cabang.max' => 'Kode cabang maksimal 10 karakter',
            'kode_cabang.alpha_num' => 'Kode cabang hanya boleh berisi huruf dan angka',
            'nama_cabang.required' => 'Nama cabang harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'telepon.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        // Convert kode_cabang to uppercase
        $validated['kode_cabang'] = strtoupper($validated['kode_cabang']);
        $validated['is_active'] = $request->has('is_active');

        $cabang->update($validated);

        return redirect()->route('admin.cabang.index')
            ->with('success', 'Cabang berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cabang $cabang)
    {
        // Check if cabang is being used
        $siswaCount = Siswa::where('cabang_id', $cabang->id)->count();
        $kelasCount = Kelas::where('cabang_id', $cabang->id)->count();
        $userCount = User::where('cabang_id', $cabang->id)->count();
        
        if ($siswaCount > 0 || $kelasCount > 0 || $userCount > 0) {
            return redirect()->route('admin.cabang.index')
                ->with('error', 'Cabang tidak dapat dihapus karena masih memiliki data siswa, kelas, atau user terkait.');
        }

        $cabang->delete();

        return redirect()->route('admin.cabang.index')
            ->with('success', 'Cabang berhasil dihapus!');
    }

    /**
     * Toggle cabang active status
     */
    public function toggleStatus(Cabang $cabang)
    {
        $cabang->update(['is_active' => !$cabang->is_active]);
        
        $status = $cabang->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.cabang.index')
            ->with('success', "Cabang berhasil {$status}!");
    }
}
