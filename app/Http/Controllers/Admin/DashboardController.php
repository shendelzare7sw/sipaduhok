<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistics for dashboard (matching the view variables)
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalGuru = \App\Models\TenagaPendidik::count();
        $totalKelas = \App\Models\Kelas::count();
        $totalUser = User::where('is_active', true)->count();
        $siswaBaruBulanIni = Siswa::whereMonth('tanggal_masuk', now()->month)
            ->whereYear('tanggal_masuk', now()->year)
            ->count();

        // Additional stats for cards
        $stats = [
            'total_users' => $totalUser,
            'total_students' => $totalSiswa,
            'total_payments_today' => Pembayaran::whereDate('tanggal_bayar', today())->count(),
            'pending_payments' => Tagihan::where('status', 'belum_bayar')->count(),
        ];

        // Recent activities (last 10 logins)
        $recent_logins = User::with('roleRelation')
            ->whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboard.admin', compact(
            'user',
            'stats',
            'recent_logins',
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalUser',
            'siswaBaruBulanIni'
        ));
    }
}
