<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Presensi;
use App\Models\Rapor;
use App\Models\JadwalPelajaran;

class WaliKelasController extends Controller
{
    /**
     * Display dashboard Wali Kelas
     */
    public function dashboard(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$tenagaPendidik) {
            return view('wali-kelas.dashboard')->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        // Get kelas yang diajar oleh wali kelas ini
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();
        
        if (!$kelas) {
            return view('wali-kelas.dashboard')->with([
                'waliKelas' => $tenagaPendidik,
                'kelas' => null,
                'message' => 'Anda belum ditugaskan sebagai wali kelas.'
            ]);
        }

        // Get siswa di kelas ini
        $siswa = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->get();

        // Statistik presensi hari ini
        $today = now()->toDateString();
        $presensiHariIni = Presensi::where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $today)
            ->get();

        $presensiStats = [
            'hadir' => $presensiHariIni->where('status', 'hadir')->count(),
            'sakit' => $presensiHariIni->where('status', 'sakit')->count(),
            'izin' => $presensiHariIni->where('status', 'izin')->count(),
            'alpha' => $presensiHariIni->where('status', 'alpha')->count(),
        ];

        // Izin yang perlu divalidasi
        $izinMenungguValidasi = Presensi::where('kelas_id', $kelas->id)
            ->where('status', 'izin')
            ->whereNull('diinput_oleh') // Belum divalidasi
            ->count();

        // Rapor yang perlu diselesaikan
        $raporBelumSelesai = Rapor::where('kelas_id', $kelas->id)
            ->where('status', 'draft')
            ->count();

        // Jadwal pelajaran hari ini
        $hari = now()->locale('id')->dayName;
        $jadwalHariIni = JadwalPelajaran::where('kelas_id', $kelas->id)
            ->where('hari', $hari)
            ->with('mataPelajaran', 'guru')
            ->orderBy('jam_mulai')
            ->get();

        return view('wali-kelas.dashboard', [
            'waliKelas' => $tenagaPendidik,
            'kelas' => $kelas,
            'totalSiswa' => $siswa->count(),
            'siswa' => $siswa,
            'presensiStats' => $presensiStats,
            'izinMenungguValidasi' => $izinMenungguValidasi,
            'raporBelumSelesai' => $raporBelumSelesai,
            'jadwalHariIni' => $jadwalHariIni,
        ]);
    }
}