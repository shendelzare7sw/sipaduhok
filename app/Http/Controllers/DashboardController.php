<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Siswa;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\GuruPengajarKelas;
use App\Models\JadwalPelajaran;

class DashboardController extends Controller
{
    
    /**
     * Admin Dashboard
     */
    public function admin(): View
    {
        $data = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::count(),
            'totalUser' => User::where('is_active', true)->count(),
            'siswaBaruBulanIni' => Siswa::whereMonth('tanggal_masuk', now()->month)
                ->whereYear('tanggal_masuk', now()->year)
                ->count(),
        ];

        return view('dashboard.admin', $data);
    }

    /**
     * Ketua PKBM Dashboard
     */
    public function ketua(): View
    {
        $data = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::count(),
        ];

        return view('dashboard.ketua', $data);
    }

    /**
     * Sekretaris Dashboard
     */
    public function sekretaris(): View
    {
        $data = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'siswaBaru' => Siswa::whereMonth('tanggal_masuk', now()->month)
                ->whereYear('tanggal_masuk', now()->year)
                ->get(),
        ];

        return view('dashboard.sekretaris', $data);
    }

    /**
     * Bendahara Dashboard
     */
    public function bendahara(): View
    {
        $data = [
            'totalTagihan' => Tagihan::sum('jumlah'),
            'totalTerbayar' => Pembayaran::where('status_validasi', 'disetujui')
                ->whereNotNull('tanggal_validasi')
                ->sum('jumlah_bayar'),
            'tagihanBelumLunas' => Tagihan::where('status', 'belum_bayar')->count(),
            'pembayaranBulanIni' => Pembayaran::where('status_validasi', 'disetujui')
                ->whereNotNull('tanggal_validasi')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->sum('jumlah_bayar'),
            'pembayaranMenungguValidasi' => Pembayaran::where('status_validasi', 'pending')->count(),
            'tagihanTerlambat' => Tagihan::where('status', 'terlambat')->count(),
        ];

        return view('dashboard.bendahara', $data);
    }


    /**
     * Guru Pengajar Dashboard
     * 
     * FIXED: Changed view path to 'guru.dashboard.dashboard'
     */
    public function guru(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        
        $kelasYangDiajar = collect();
        $totalSiswa = 0;
        $jadwalHariIni = collect();
        
        if ($tenagaPendidik) {
            // Get all kelas yang diajar guru ini
            $guruKelasRaw = GuruPengajarKelas::with(['kelas', 'mataPelajaran'])
                ->where('tenaga_pendidik_id', $tenagaPendidik->id)
                ->get()
                ->groupBy('kelas_id');
            
            // Transform data untuk view
            $kelasYangDiajar = collect();
            foreach ($guruKelasRaw as $kelasId => $items) {
                $kelas = $items->first()->kelas;
                $kelasYangDiajar->push([
                    'kelas' => $kelas,
                    'jumlah_siswa' => Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->count(),
                    'jumlah_mapel' => $items->count(),
                ]);
            }
            
            $kelasIds = $guruKelasRaw->keys();
            $totalSiswa = Siswa::whereIn('kelas_id', $kelasIds)
                ->where('status', 'aktif')
                ->count();
            
            $hariIni = $this->getHariIndonesia(now()->dayOfWeek);
            $jadwalHariIni = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $tenagaPendidik->id)
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();
        }
        
        $data = [
            'guru' => $tenagaPendidik,
            'kelasYangDiajar' => $kelasYangDiajar,
            'totalSiswa' => $totalSiswa,
            'jadwalHariIni' => $jadwalHariIni,
        ];

        return view('dashboard.guru', $data);
    }

    private function getHariIndonesia($dayOfWeek)
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        
        return $hari[$dayOfWeek] ?? 'Senin';
    }

}