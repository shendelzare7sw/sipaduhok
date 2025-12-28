<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa;
use App\Models\TenagaPendidik;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil berdasarkan role
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil role user
        $roleName = $user->roleRelation ? $user->roleRelation->name : $user->role;

        // Data profil berdasarkan role
        $profileData = null;

        switch ($roleName) {
            case 'siswa':
                $profileData = Siswa::where('user_id', $user->id)
                    ->with(['kelas', 'cabang', 'tahunAjaran'])
                    ->first();
                break;

            case 'guru':
            case 'wali_kelas':
            case 'ketua':
            case 'sekretaris':
            case 'bendahara':
                $profileData = TenagaPendidik::where('user_id', $user->id)
                    ->with('cabang')
                    ->first();
                break;

            case 'orang_tua':
                // Orang tua tidak punya profil khusus, hanya data user
                $profileData = null;
                break;

            case 'admin':
                // Admin tidak punya profil khusus
                $profileData = null;
                break;
        }

        return view('profile.index', compact('user', 'roleName', 'profileData'));
    }

    /**
     * Update profil (untuk role yang punya data tambahan)
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->roleRelation ? $user->roleRelation->name : $user->role;

        // Validasi berbeda sesuai role
        $validated = $request->validate([
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        switch ($roleName) {
            case 'siswa':
                $siswa = Siswa::where('user_id', $user->id)->first();
                if ($siswa) {
                    $siswa->update([
                        'no_telepon' => $validated['no_telepon'],
                        'alamat' => $validated['alamat'],
                    ]);
                }
                break;

            case 'guru':
            case 'wali_kelas':
            case 'ketua':
            case 'sekretaris':
            case 'bendahara':
                $tenagaPendidik = TenagaPendidik::where('user_id', $user->id)->first();
                if ($tenagaPendidik) {
                    $tenagaPendidik->update([
                        'no_telepon' => $validated['no_telepon'],
                        'alamat' => $validated['alamat'],
                    ]);
                }
                break;
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Upload foto profil
     */
    public function uploadFoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hapus foto lama jika ada
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Upload foto baru
        $path = $request->file('foto_profil')->store('profile-photos', 'public');

        $user->foto_profil = $path;
        $user->save();

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Hapus foto profil
     */
    public function deleteFoto()
    {
        $user = Auth::user();

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);

            $user->foto_profil = null;
            $user->save();

            return back()->with('success', 'Foto profil berhasil dihapus!');
        }

        return back()->with('error', 'Tidak ada foto profil untuk dihapus.');
    }
}
