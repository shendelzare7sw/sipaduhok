<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Cabang;
use App\Models\JadwalPelajaran;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Statistics for dashboard
        // Kelas dihitung per TA aktif saja - tanpa scoping, angkanya menumpuk tiap
        // ganti TA (45 kelas jadi 90, lalu 135, dst) karena kelas TA lama tetap ada.
        $taAktifId = \App\Models\TahunAjaran::where('is_active', true)->value('id');

        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalGuru = \App\Models\TenagaPendidik::count();
        $totalKelas = Kelas::when($taAktifId, fn ($q) => $q->where('tahun_ajaran_id', $taAktifId))->count();
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

        // CleanFlow onboarding hanya membaca data yang sudah ada. Aturan bisnis
        // dan proses penyimpanan setiap modul tetap berada di controller asalnya.
        $setupSteps = collect([
            [
                'label' => 'Aktifkan tahun ajaran',
                'description' => 'Periode aktif menjadi dasar kelas, jadwal, dan laporan.',
                'route' => 'admin.tahun-ajaran.index',
                'complete' => TahunAjaran::where('is_active', true)->exists(),
            ],
            [
                'label' => 'Siapkan cabang',
                'description' => 'Pastikan unit atau lokasi sekolah sudah tersedia.',
                'route' => 'admin.cabang.index',
                'complete' => Cabang::exists(),
            ],
            [
                'label' => 'Input warga sekolah',
                'description' => 'Tambah atau impor tenaga pendidik, siswa, dan wali.',
                'route' => 'admin.users.siswa',
                'complete' => $totalSiswa > 0 && $totalGuru > 0,
            ],
            [
                'label' => 'Susun kelas',
                'description' => 'Buat kelas, tentukan wali, lalu tempatkan siswa.',
                'route' => 'admin.kelas.index',
                'complete' => $totalKelas > 0,
            ],
            [
                'label' => 'Siapkan mata pelajaran',
                'description' => 'Lengkapi mapel sebelum menugaskan guru dan jadwal.',
                'route' => 'admin.mata-pelajaran.index',
                'complete' => MataPelajaran::exists(),
            ],
            [
                'label' => 'Terbitkan jadwal',
                'description' => 'Susun jadwal setelah kelas, mapel, dan guru siap.',
                'route' => 'admin.jadwal-pelajaran.index',
                'complete' => JadwalPelajaran::exists(),
            ],
        ]);

        $setupProgress = (int) round(($setupSteps->where('complete', true)->count() / $setupSteps->count()) * 100);
        $nextSetupStep = $setupSteps->firstWhere('complete', false);

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
            'chartPendaftaran',
            'setupSteps',
            'setupProgress',
            'nextSetupStep'
        ));
    }
}
