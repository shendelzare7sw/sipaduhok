<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun
     */
    public function settings()
    {
        $user = Auth::user();

        return view('account.settings', compact('user'));
    }

    /**
     * Update pengaturan akun (email, nama)
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        // Base rules
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
        ];

        // Conditional Email Rules
        if ($user->isAdmin()) {
            $rules['email'] = 'required|email|unique:users,email,' . $user->id;
        } else {
            // Non-admin validates valid string for local part
            $rules['email_local'] = 'required|string|max:64|regex:/^[a-zA-Z0-9.]+$/';
        }

        $validated = $request->validate($rules);

        // Prepare data
        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
        ];

        // Process Email
        if ($user->isAdmin()) {
            $updateData['email'] = $validated['email'];
        } else {
            // Reconstruct Email
            $currentEmailParts = explode('@', $user->email);
            $domain = isset($currentEmailParts[1]) ? $currentEmailParts[1] : 'sipaduhok.com'; // Fallback
            $newEmail = $validated['email_local'] . '@' . $domain;

            // Check if new email is unique (manual check since we reconstructed it)
            if (\App\Models\User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
                return back()->withErrors(['email_local' => 'Email ini sudah digunakan oleh pengguna lain.'])->withInput();
            }

            $updateData['email'] = $newEmail;
        }

        $user->update($updateData);

        // Sync Name to Biodata Tables
        // Check for Siswa
        $siswa = \App\Models\Siswa::where('user_id', $user->id)->first();
        if ($siswa) {
            $siswa->update(['nama_lengkap' => $validated['name']]);
        }

        // Check for Tenaga Pendidik
        $tenagaPendidik = \App\Models\TenagaPendidik::where('user_id', $user->id)->first();
        if ($tenagaPendidik) {
            $tenagaPendidik->update(['nama_lengkap' => $validated['name']]);
        }

        return back()->with('success', 'Pengaturan akun berhasil diperbarui!');
    }

    /**
     * Ubah password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Cek password lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        // Logout user dan hapus semua sesi
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke login dengan pesan sukses
        return redirect()->route('login')->with('success', 'Password berhasil diubah! Silakan login dengan password baru Anda.');
    }
}
