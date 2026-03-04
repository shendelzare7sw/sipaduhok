<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdminSecuritySetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is logged in and is Admin or Ketua PKBM
        if ($user && ($user->isAdmin() || $user->isKetuaPKBM())) {
            
            // Check if they haven't set up their security details yet
            if (!$user->security_question || !$user->security_answer || !$user->security_pin) {
                
                // Allow them to access the setup route and logout route so they aren't completely trapped
                if (!$request->routeIs('admin.security.setup') && !$request->routeIs('admin.security.setup.store') && !$request->routeIs('logout')) {
                    return redirect()->route('admin.security.setup')->with('warning', 'Anda diwajibkan untuk mengatur Pertanyaan dan PIN Keamanan terlebih dahulu sebelum melanjutkan.');
                }
            }
        }

        return $next($request);
    }
}
