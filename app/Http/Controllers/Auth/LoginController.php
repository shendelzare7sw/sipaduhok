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

        // Determine role from either new role_id or old role field
        $roleName = null;

        // Try new role system first (role_id relationship)
        if ($user->role_id && $user->roleRelation) {
            $roleName = $user->roleRelation->name;
        }
        // Fallback to old role field
        elseif ($user->role) {
            $roleName = $user->role;
        }

        // If still no role, redirect to default dashboard
        if (!$roleName) {
            return redirect()->intended(route('dashboard'));
        }

        // Check for intended URL but avoid AJAX/API endpoints
        $intendedUrl = redirect()->intended()->getTargetUrl();
        if (str_contains($intendedUrl, 'notifications/unread-count') || 
            str_contains($intendedUrl, 'api/') || 
            str_contains($intendedUrl, 'get-') ||
            str_contains($intendedUrl, 'check-')) {
            $intendedUrl = null;
        }

        // Redirect based on role
        if ($intendedUrl && $intendedUrl !== route('dashboard') && $intendedUrl !== url('/')) {
            return redirect($intendedUrl);
        }

        return match($roleName) {
            'admin' => redirect()->route('admin.dashboard'),
            'ketua_pkbm' => redirect()->route('ketua.dashboard'),
            'sekretaris' => redirect()->route('sekretaris.dashboard'),
            'bendahara' => redirect()->route('bendahara.dashboard'),
            'wali_kelas' => redirect()->route('wali.dashboard'),
            'guru_pengajar' => redirect()->route('guru.dashboard'),
            'orang_tua' => redirect()->route('wali-siswa.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('dashboard'),
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
