<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Services\EmailRecoveryService;

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
            $rules['personal_email'] = 'nullable|email';
        } else {
            // Non-admin validates valid string for local part
            $rules['email_local'] = 'required|string|max:64|regex:/^[a-zA-Z0-9.]+$/';
            $rules['personal_email'] = 'nullable|email';
        }

        $validated = $request->validate($rules);

        // Prepare data
        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'personal_email' => $validated['personal_email'] ?? null,
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

    /**
     * Ubah Pengaturan Keamanan (Hanya untuk Admin/Ketua PKBM)
     */
    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        // Hanya Admin / Ketua PKBM yang boleh update PIN
        if (!$user->isAdmin() && !$user->isKetuaPKBM()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'current_password' => 'required',
            'security_question' => 'required|string|max:255',
            'security_answer'   => 'required|string|max:255',
            'security_pin'      => 'required|string|digits:6|confirmed',
        ], [
            'security_pin.confirmed' => 'Konfirmasi PIN Keamanan tidak cocok.',
        ]);

        // Cek Otoritas dengan password lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password_security' => 'Password saat ini tidak sesuai. Anda tidak diizinkan mengubah pengaturan keamanan keamanan.']);
        }

        // Update keamanan
        $user->update([
            'security_question' => $validated['security_question'],
            'security_answer'   => Hash::make(strtolower(trim($validated['security_answer']))),
            'security_pin'      => Hash::make($validated['security_pin']),
        ]);

        // Kirim Notifikasi ke Email Pribadi Admin (jika ada)
        if ($user->personal_email) {
            $emailService = new EmailRecoveryService();
            $emailService->sendSecurityUpdateNotification($user, $validated['security_question']);
        }

        return back()->with('success', 'Pertanyaan Keamanan dan PIN berhasil diperbarui!');
    }
}
