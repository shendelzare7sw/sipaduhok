<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class AdminRecoveryController extends Controller
{
    /**
     * Unlock admin recovery route via Easter Egg
     */
    public function unlock(Request $request)
    {
        $request->session()->put('admin_recovery_unlocked', true);
        return response()->json(['success' => true]);
    }

    /**
     * Show the admin recovery form.
     */
    public function showLinkRequestForm()
    {
        if (!session('admin_recovery_unlocked')) {
            return redirect()->route('login')->with('error', 'Halaman pemulihan admin telah dinonaktifkan.');
        }

        return view('auth.admin-recovery');
    }

    /**
     * Handle an incoming admin recovery request.
     */
    public function reset(Request $request)
    {
        if (!session('admin_recovery_unlocked')) {
            return redirect()->route('login')->with('error', 'Fitur pemulihan admin telah dinonaktifkan.');
        }

        $request->validate([
            'identifier' => 'required|string',
            'security_question' => 'required|string',
            'security_answer' => 'required|string',
            'security_pin' => 'required|string|digits:6',
            'password' => ['required', 'string', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        // Find user by username or email
        $user = User::where('username', $request->identifier)
                    ->orWhere('email', $request->identifier)
                    ->first();

        // Check if user exists and has admin/ketua privileges
        // Use same error message for all failure cases to prevent user enumeration
        $genericError = 'Verifikasi gagal. Periksa kembali identitas dan kredensial keamanan Anda.';

        if (!$user) {
            return back()->withInput()->withErrors(['identifier' => $genericError]);
        }

        if (!$user->isAdmin() && !$user->isKetuaPKBM()) {
            Log::warning("Unauthorized recovery attempt for non-admin user: {$user->email}");
            return back()->withInput()->withErrors(['identifier' => $genericError]);
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

        // Lock the admin recovery route again
        $request->session()->forget('admin_recovery_unlocked');

        return redirect()->route('login')->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
    }
}
