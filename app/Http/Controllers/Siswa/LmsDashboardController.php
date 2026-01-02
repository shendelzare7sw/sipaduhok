<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\KalenderAkademik;
use App\Models\Pengumuman;
use App\Models\JadwalPelajaran;
use App\Models\TenagaPendidik;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\Presensi;
use App\Models\PengaturanIstirahat;
use Carbon\Carbon;

class LmsDashboardController extends Controller
{
    /**
     * Dashboard LMS - Beranda
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran'])
            ->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek akses LMS (hanya SMP & SMA)
        if (!in_array($siswa->kelas->jenjang ?? '', ['SMP', 'SMA'])) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Akses LMS hanya tersedia untuk siswa SMP dan SMA');
        }

        // Statistik Kehadiran
        $totalHariAktif = Presensi::where('siswa_id', $siswa->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        
        $hadir = Presensi::where('siswa_id', $siswa->id)
            ->where('status', 'hadir')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
        
        $persenKehadiran = $totalHariAktif > 0 ? round(($hadir / $totalHariAktif) * 100) : 0;

        // Tugas Pending
        $tugasPending = Tugas::where('kelas_id', $siswa->kelas_id)
            ->where('tanggal_deadline', '>=', now())
            ->whereDoesntHave('tugasSiswa', function($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id)
                  ->whereIn('status', ['dikerjakan', 'dinilai']);
            })
            ->count();

        // Agenda Bulan Ini
        $agendaBulanIni = KalenderAkademik::where('status', 'aktif')
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id ?? null)
            ->whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->count();

        // Jadwal Hari Ini
        $hariIni = Carbon::now()->locale('id')->dayName;
        $hariIni = ucfirst($hariIni); // Senin, Selasa, etc.

        $jadwalHariIni = JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
            ->where('hari', $hariIni)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // Pengumuman Terbaru
        $pengumuman = Pengumuman::where('status', 'aktif')
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->orderBy('tanggal_pengumuman', 'desc')
            ->first();

        return view('siswa.lms.dashboard', compact(
            'siswa',
            'persenKehadiran',
            'tugasPending',
            'agendaBulanIni',
            'jadwalHariIni',
            'pengumuman'
        ));
    }

    /**
     * Kalender Akademik
     */
    public function kalender()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa || !$siswa->kelas) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data kelas tidak ditemukan');
        }

        $tahunAjaranId = $siswa->kelas->tahun_ajaran_id;

        // Ambil semua kegiatan dalam tahun ajaran ini
        $kalenderTahunan = KalenderAkademik::where('status', 'aktif')
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->tanggal_mulai)->format('Y-m');
            });

        return view('siswa.lms.kalender.index', compact('siswa', 'kalenderTahunan'));
    }

    /**
     * Detail Kalender per Tanggal
     */
    public function kalenderDetail($tanggal)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        // Kegiatan di tanggal tersebut
        $kegiatan = KalenderAkademik::where('status', 'aktif')
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->where(function($q) use ($tanggal) {
                $q->whereDate('tanggal_selesai', '>=', $tanggal)
                  ->orWhereNull('tanggal_selesai');
            })
            ->get();

        // Jadwal Pelajaran di hari tersebut
        $hariIndo = Carbon::parse($tanggal)->locale('id')->dayName;
        $hariIndo = ucfirst($hariIndo);

        $jadwalHariIni = JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
            ->where('hari', $hariIndo)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        return view('siswa.lms.kalender.detail', compact(
            'siswa',
            'tanggal',
            'kegiatan',
            'jadwalHariIni'
        ));
    }

    /**
     * Jadwal Pelajaran Lengkap
     */
    public function jadwal()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $jadwalMingguIni = JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('siswa.lms.jadwal', compact('siswa', 'jadwalMingguIni'));
    }

    /**
     * Print Jadwal Pelajaran
     */
    public function printJadwal()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with(['kelas.tahunAjaran', 'kelas.waliKelas'])->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $kelas = $siswa->kelas;
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            // Get jadwal pelajaran
            $jadwalPelajaran = JadwalPelajaran::where('kelas_id', $kelas->id)
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();

            // Get waktu istirahat untuk jenjang dan hari ini
            $istirahatList = PengaturanIstirahat::jenjang($kelas->jenjang)
                ->aktif()
                ->untukHari($hari)
                ->orderBy('jam_mulai')
                ->get();

            // Merge jadwal dan istirahat, kemudian sort by jam_mulai
            $merged = collect();

            // Add jadwal pelajaran
            foreach ($jadwalPelajaran as $jadwal) {
                $merged->push([
                    'type' => 'jadwal',
                    'data' => $jadwal,
                    'jam_mulai' => $jadwal->jam_mulai,
                ]);
            }

            // Add istirahat
            foreach ($istirahatList as $istirahat) {
                $merged->push([
                    'type' => 'istirahat',
                    'data' => $istirahat,
                    'jam_mulai' => $istirahat->jam_mulai,
                ]);
            }

            // Sort by jam_mulai
            $jadwalPerHari[$hari] = $merged->sortBy('jam_mulai')->values();
        }

        return view('siswa.lms.jadwal-print', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }

    /**
     * Daftar Guru Pengajar
     */
    public function guru()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        $guruPengajar = TenagaPendidik::whereHas('guruKelas', function($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id);
            })
            ->with(['guruKelas' => function($q) use ($siswa) {
                $q->where('kelas_id', $siswa->kelas_id)->with('mataPelajaran');
            }])
            ->get();

        return view('siswa.lms.guru', compact('siswa', 'guruPengajar'));
    }
}