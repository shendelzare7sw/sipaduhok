<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Pengumuman;
use App\Models\Flyer;
use App\Models\KalenderAkademik;
use App\Models\JadwalPelajaran;
use App\Models\TenagaPendidik;
use App\Models\Presensi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\Materi;
use Carbon\Carbon;

class SiswaDashboardController extends Controller
{
    /**
     * Main Dashboard Router - Redirect ke SIA Dashboard
     */
    public function index()
    {
        // Default redirect ke SIA Dashboard
        return redirect()->route('siswa.sia.dashboard');
    }

    /**
     * Dashboard SIA - Tampilan pertama setelah login
     */
    public function dashboardSIA()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran', 'kelas.waliKelas', 'cabang'])
            ->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Pengumuman Aktif Hari Ini & Mendatang
        $pengumuman = Pengumuman::aktif()
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->orderBy('tanggal_pengumuman', 'asc')
            ->limit(5)
            ->get();

        // Flyer Pop-up untuk Siswa
        $flyers = Flyer::getPopupFlyers('siswa', 3);

        // Grafik Performa Siswa
        $performa = $this->getPerformaSiswa($siswa->id);

        // Rekap Absensi Bulan Ini
        $absensi = $this->getRekapAbsensi($siswa->id);

        // Determine Semester (Logic Sederhana berdasarkan bulan)
        $bulan = now()->month;
        $semester = ($bulan >= 7 && $bulan <= 12) ? 'Ganjil' : 'Genap';

        return view('siswa.sia.dashboard', compact(
            'siswa',
            'pengumuman',
            'flyers',
            'performa',
            'absensi',
            'semester'
        ));
    }

    /**
     * Dashboard LMS - Learning Management System
     */
    public function dashboardLMS()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran'])
            ->first();

        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek akses LMS ditangani oleh middleware 'lms.access'

        // Kalender Akademik Bulan Ini
        $kalenderBulanIni = KalenderAkademik::aktif()
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id)
            ->whereMonth('tanggal_mulai', now()->month)
            ->whereYear('tanggal_mulai', now()->year)
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Pengumuman
        $pengumuman = Pengumuman::aktif()
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->limit(3)
            ->get();

        // Jadwal Pelajaran Minggu Ini
        $jadwalMingguIni = JadwalPelajaran::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        // Daftar Guru Pengajar
        $guruPengajar = TenagaPendidik::whereHas('guruKelas', function ($q) use ($siswa) {
            $q->where('kelas_id', $siswa->kelas_id);
        })
            ->with(['guruKelas.mataPelajaran'])
            ->get();

        // Notifikasi Hari Ini (Materi, Tugas, Ujian Baru)
        $notifikasiHariIni = $this->getNotifikasiHariIni($siswa->kelas_id);

        return view('siswa.lms.dashboard', compact(
            'siswa',
            'kalenderBulanIni',
            'pengumuman',
            'jadwalMingguIni',
            'guruPengajar',
            'notifikasiHariIni'
        ));
    }

    /**
     * Lihat Kalender Akademik Setahun
     */
    public function kalenderTahunan()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa || !$siswa->kelas) {
            return redirect()->route('siswa.lms.dashboard')
                ->with('error', 'Data kelas tidak ditemukan');
        }

        $tahunAjaranId = $siswa->kelas->tahun_ajaran_id;

        // Ambil semua kegiatan dalam tahun ajaran ini
        $kalenderTahunan = KalenderAkademik::aktif()
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal_mulai)->format('Y-m');
            });

        return view('siswa.lms.kalender', compact('siswa', 'kalenderTahunan'));
    }

    /**
     * Lihat Detail Hari Tertentu di Kalender
     */
    public function kalenderDetail($tanggal)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.lms.dashboard');
        }

        // Kegiatan di tanggal tersebut
        $kegiatan = KalenderAkademik::aktif()
            ->where('tahun_ajaran_id', $siswa->kelas->tahun_ajaran_id)
            ->whereDate('tanggal_mulai', '<=', $tanggal)
            ->where(function ($q) use ($tanggal) {
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

        return view('siswa.lms.kalender-detail', compact(
            'siswa',
            'tanggal',
            'kegiatan',
            'jadwalHariIni'
        ));
    }

    /**
     * Helper: Hitung Performa Siswa (untuk Dashboard SIA)
     */
    private function getPerformaSiswa($siswaId)
    {
        // Total Tugas
        $totalTugas = Tugas::whereHas('kelas', function ($q) use ($siswaId) {
            $q->whereHas('siswa', function ($q2) use ($siswaId) {
                $q2->where('siswa.id', $siswaId);
            });
        })
            ->count();

        // Tugas Selesai
        $tugasSelesai = Tugas::whereHas('tugasSiswa', function ($q) use ($siswaId) {
            $q->where('siswa_id', $siswaId)
                ->whereIn('status', ['dikerjakan', 'dinilai']);
        })
            ->count();

        // Total Ujian
        $totalUjian = Ujian::whereHas('kelas.siswa', function ($q) use ($siswaId) {
            $q->where('siswa.id', $siswaId);
        })
            ->count();

        // Ujian Selesai
        $ujianSelesai = Ujian::whereHas('ujianSiswa', function ($q) use ($siswaId) {
            $q->where('siswa_id', $siswaId)
                ->whereIn('status', ['selesai', 'dinilai']);
        })
            ->count();

        // Total Materi
        $totalMateri = Materi::whereHas('kelas.siswa', function ($q) use ($siswaId) {
            $q->where('siswa.id', $siswaId);
        })
            ->count();

        // Materi Dipelajari (anggap semua materi bisa diakses)
        $materiDipelajari = $totalMateri; // Placeholder, bisa ditrack lebih detail

        return [
            'tugas' => [
                'total' => $totalTugas,
                'selesai' => $tugasSelesai,
                'persentase' => $totalTugas > 0 ? round(($tugasSelesai / $totalTugas) * 100, 1) : 0
            ],
            'ujian' => [
                'total' => $totalUjian,
                'selesai' => $ujianSelesai,
                'persentase' => $totalUjian > 0 ? round(($ujianSelesai / $totalUjian) * 100, 1) : 0
            ],
            'materi' => [
                'total' => $totalMateri,
                'dipelajari' => $materiDipelajari,
                'persentase' => $totalMateri > 0 ? round(($materiDipelajari / $totalMateri) * 100, 1) : 100
            ]
        ];
    }

    /**
     * Helper: Rekap Absensi Bulan Ini
     */
    private function getRekapAbsensi($siswaId)
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $sakit = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'sakit')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        $izin = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'izin')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        $alpha = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'alpha')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

        return compact('sakit', 'izin', 'alpha');
    }

    /**
     * Helper: Notifikasi Hari Ini (Materi, Tugas, Ujian Baru)
     */
    private function getNotifikasiHariIni($kelasId)
    {
        $today = now()->toDateString();

        // Materi Baru Hari Ini
        $materiBaru = Materi::where('kelas_id', $kelasId)
            ->whereDate('tanggal_upload', $today)
            ->with('mataPelajaran')
            ->get();

        // Tugas Baru Hari Ini
        $tugasBaru = Tugas::where('kelas_id', $kelasId)
            ->whereDate('tanggal_mulai', $today)
            ->with('mataPelajaran')
            ->get();

        // Ujian Hari Ini
        $ujianHariIni = Ujian::where('kelas_id', $kelasId)
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_selesai', '>=', now())
            ->with('mataPelajaran')
            ->get();

        return [
            'materi' => $materiBaru,
            'tugas' => $tugasBaru,
            'ujian' => $ujianHariIni
        ];
    }
}