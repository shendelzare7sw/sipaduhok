<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistics for dashboard
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalGuru = \App\Models\TenagaPendidik::count();
        $totalKelas = Kelas::count();
        $totalUser = User::where('is_active', true)->count();
        
        $siswaBaruBulanIni = Siswa::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
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
            ->take(5)
            ->get();

        // --- DYNAMIC CHART DATA ---
        
        // 1. Distribusi Gender (Siswa Aktif)
        $genderData = [
            'L' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'L')->count(),
            'P' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'P')->count(),
        ];

        // 2. Siswa per Kelas
        $kelasDataRaw = Kelas::withCount(['siswa' => function($query) {
            $query->where('status', 'aktif');
        }])->get();
        // Since there might be many classes, let's take the ones with most students or group them
        // If there are too many, chart looks bad. For now, let's pass all but maybe limit to top 15
        $kelasDataRaw = $kelasDataRaw->sortByDesc('siswa_count')->take(10);
        $kelasLabels = $kelasDataRaw->pluck('nama_kelas')->toArray();
        $kelasCounts = $kelasDataRaw->pluck('siswa_count')->toArray();

        // 3. Statistik Pendaftaran Siswa (Time Series: 6 months, 3 months, 1 year)
        // Helper specifically for generating past X months data
        $getRegistrationData = function ($monthsCount) {
            $labels = [];
            $data = [];
            for ($i = $monthsCount - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subMonthsNoOverflow($i);
                $labels[] = $date->translatedFormat('M Y'); // e.g. "Jan 2024"
                
                $count = Siswa::whereYear('created_at', $date->year)
                              ->whereMonth('created_at', $date->month)
                              ->count();
                $data[] = $count;
            }
            return ['labels' => $labels, 'data' => $data];
        };

        $chartPendaftaran = [
            '3_bulan' => $getRegistrationData(3),
            '6_bulan' => $getRegistrationData(6),
            '1_tahun' => $getRegistrationData(12),
        ];

        return view('dashboard.admin', compact(
            'user',
            'stats',
            'recent_logins',
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'totalUser',
            'siswaBaruBulanIni',
            'genderData',
            'kelasLabels',
            'kelasCounts',
            'chartPendaftaran'
        ));
    }
}
