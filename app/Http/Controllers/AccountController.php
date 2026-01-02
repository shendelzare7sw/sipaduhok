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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

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
