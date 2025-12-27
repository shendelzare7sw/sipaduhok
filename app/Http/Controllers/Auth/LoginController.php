<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Multi-role redirect based on user role
        $user = Auth::user();

        return match($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'ketua_pkbm' => redirect()->intended(route('ketua.dashboard')),
            'sekretaris' => redirect()->intended(route('sekretaris.dashboard')),
            'bendahara' => redirect()->intended(route('bendahara.dashboard')),
            'wali_kelas' => redirect()->intended(route('wali.dashboard')),
            'guru_pengajar' => redirect()->intended(route('guru.dashboard')),
            'siswa' => redirect()->intended(route('siswa.dashboard')),
            default => redirect()->intended(route('dashboard')),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
