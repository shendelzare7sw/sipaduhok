<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminRecoveryController extends Controller
{
    /**
     * Show the admin recovery form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.admin-recovery');
    }

    /**
     * Handle an incoming admin recovery request.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'security_pin' => 'required|string|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find user by username or email
        $user = User::where('username', $request->identifier)
                    ->orWhere('email', $request->identifier)
                    ->first();

        // Check if user exists and has admin/ketua privileges
        if (!$user) {
            return back()->withInput()->withErrors(['identifier' => 'Akun tidak ditemukan.']);
        }

        if (!$user->isAdmin() && !$user->isKetuaPKBM()) {
            // Security measure: pretend it failed randomly or just say unauthorized
            Log::warning("Unauthorized recovery attempt for non-admin user: {$user->email}");
            return back()->withErrors(['identifier' => 'Akun ini tidak memiliki akses ke fitur ini.']);
        }

        // Check if security questions are set up
        if (!$user->security_question || !$user->security_answer || !$user->security_pin) {
            return back()->withErrors(['identifier' => 'Akun ini belum mengatur Pertanyaan Keamanan. Hubungi Developer.']);
        }

        // Verify Question Match
        if ($user->security_question !== $request->security_question) {
            return back()->withInput()->withErrors(['security_question' => 'Pertanyaan atau jawaban keamanan tidak cocok.']);
        }

        // Verify Answer (Assuming it's hashed in the DB - usually a good practice, but for simplicity we might just do case-insensitive comparison if it's plaintext, or Hash::check if hashed. Let's assume it's hashed as per our plan)
        if (!Hash::check(strtolower(trim($request->security_answer)), $user->security_answer)) {
            return back()->withInput()->withErrors(['security_answer' => 'Pertanyaan atau jawaban keamanan tidak cocok.']);
        }

        // Verify PIN
        if (!Hash::check($request->security_pin, $user->security_pin)) {
            return back()->withInput()->withErrors(['security_pin' => 'PIN Keamanan salah.']);
        }

        // All checks passed, update password
        $user->password = Hash::make($request->password);
        $user->save();

        Log::info("Admin password reset successful via Security Question for user: {$user->email}");

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
