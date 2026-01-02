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
            return view('wali-kelas.dashboard')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'waliKelas' => null,
                'kelas' => null,
                'totalSiswa' => 0,
                'siswa' => collect(),
                'presensiStats' => ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0],
                'izinMenungguValidasi' => 0,
                'raporBelumSelesai' => 0,
                'jadwalHariIni' => collect(),
            ]);
        }

        // Get kelas yang diajar oleh wali kelas ini
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

        if (!$kelas) {
            return view('wali-kelas.dashboard')->with([
                'waliKelas' => $tenagaPendidik,
                'kelas' => null,
                'message' => 'Anda belum ditugaskan sebagai wali kelas.',
                'totalSiswa' => 0,
                'siswa' => collect(),
                'presensiStats' => ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0],
                'izinMenungguValidasi' => 0,
                'raporBelumSelesai' => 0,
                'jadwalHariIni' => collect(),
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

        // Izin yang perlu divalidasi (dari orang tua)
        $izinMenungguValidasi = Presensi::where('kelas_id', $kelas->id)
            ->whereIn('status', ['sakit', 'izin'])
            ->where('keterangan', 'LIKE', '%Diajukan oleh orang tua%')
            ->where('keterangan', 'NOT LIKE', '%Divalidasi%')
            ->whereNotNull('diinput_oleh')
            ->whereHas('inputBy', function($query) {
                $query->whereHas('roleRelation', function($q) {
                    $q->where('name', 'orang_tua');
                });
            })
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

    /**
     * Display jadwal pelajaran for wali kelas (read-only)
     */
    public function jadwalPelajaran(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();

        if (!$tenagaPendidik) {
            return view('wali-kelas.jadwal-pelajaran')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'waliKelas' => null,
                'kelas' => null,
                'jadwalByHari' => collect(),
                'hariList' => [],
            ]);
        }

        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->with('cabang', 'tahunAjaran')->first();

        if (!$kelas) {
            return view('wali-kelas.jadwal-pelajaran')->with([
                'waliKelas' => $tenagaPendidik,
                'kelas' => null,
                'message' => 'Anda belum ditugaskan sebagai wali kelas.',
                'jadwalByHari' => collect(),
                'hariList' => [],
            ]);
        }

        $jadwalList = JadwalPelajaran::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $kelas->id)
            ->where('tahun_ajaran_id', $kelas->tahun_ajaran_id)
            ->get();

        // Group by hari
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalByHari = collect($hariList)->mapWithKeys(function($hari) use ($jadwalList) {
            return [
                $hari => $jadwalList->where('hari', $hari)->sortBy('jam_mulai')->values()
            ];
        });

        return view('wali-kelas.jadwal-pelajaran', [
            'waliKelas' => $tenagaPendidik,
            'kelas' => $kelas,
            'jadwalByHari' => $jadwalByHari,
            'hariList' => $hariList,
        ]);
    }
}