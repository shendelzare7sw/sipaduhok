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

        // Track login activity
        $user = Auth::user();
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->save();

        // Determine which role system to use
        $roleName = null;

        if ($user->role_id && $user->roleRelation) {
            // New role system (from role_id relationship)
            $roleName = $user->roleRelation->name;
        } else {
            // Old role enum system (get from attributes)
            $roleName = $user->attributes['role'] ?? null;
        }

        // Redirect based on role
        return match($roleName) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'ketua_pkbm' => redirect()->intended(route('ketua.dashboard')),
            'sekretaris' => redirect()->intended(route('sekretaris.dashboard')),
            'bendahara' => redirect()->intended(route('bendahara.dashboard')),
            'wali_kelas' => redirect()->intended(route('wali.dashboard')),
            'guru_pengajar' => redirect()->intended(route('guru.dashboard')),
            'orang_tua' => redirect()->intended(route('orang-tua.dashboard')),
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
