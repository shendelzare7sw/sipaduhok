<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminSecuritySetupController extends Controller
{
    /**
     * Show the security setup form.
     */
    public function showSetupForm()
    {
        $user = Auth::user();
        
        // If they already set it up, no need to be here
        if ($user->security_question && $user->security_answer && $user->security_pin) {
            return redirect()->route('dashboard');
        }

        return view('auth.admin-security-setup');
    }

    /**
     * Handle the security setup request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'security_question' => 'required|string|max:255',
            'security_answer'   => 'required|string|max:255',
            'security_pin'      => 'required|string|digits:6|confirmed',
        ], [
            'security_pin.confirmed' => 'Konfirmasi PIN tidak cocok.',
        ]);

        $user = Auth::user();

        $user->security_question = $request->security_question;
        // Hash the answer for security (though we verify with Hash::check, converting to lowercase first is good for UX)
        $user->security_answer = Hash::make(strtolower(trim($request->security_answer)));
        $user->security_pin = Hash::make($request->security_pin);
        
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Keamanan Akun berhasil ditingkatkan. Anda sekarang memiliki opsi pemulihan mandiri.');
    }
}
