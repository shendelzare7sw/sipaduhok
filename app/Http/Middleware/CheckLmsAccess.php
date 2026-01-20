<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Siswa;
use App\Models\AppSetting;

class CheckLmsAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get current user (assumed to be Siswa from role check)
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user is Siswa
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa || !$siswa->kelas) {
            // If not a student or no class, deny (or let role middleware handle it, but for LMS specific we deny)
            abort(403, 'Akses ditolak. Data siswa tidak valid.');
        }

        // Get Allowed Jenjang from Settings
        $setting = AppSetting::where('key', 'lms_allowed_jenjang')->first();
        $allowedJenjang = $setting ? json_decode($setting->value, true) : [];

        // Check if student's jenjang is in allowed list
        if (!in_array($siswa->kelas->jenjang, $allowedJenjang)) {
            return response()->view('errors.lms-disabled', ['jenjang' => $siswa->kelas->jenjang]);
        }

        return $next($request);
    }
}
