<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Pengumuman;
use App\Models\Flyer;
use App\Models\Presensi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\Materi;
use App\Models\Nilai;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;

use App\Models\Rapor; // Added Rapor model import

class SiaDashboardController extends Controller
{
    /**
     * Method index - Landing page SIA
     * Sesuai dengan route siswa.sia.dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)
            ->with(['kelas.tahunAjaran', 'kelas.waliKelas', 'cabang'])
            ->first();

        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan');
        }

        // Check if Alumni (Lulus)
        if ($siswa->status === 'lulus') {
            return $this->dashboardAlumni($siswa);
        }

        // Pengumuman Aktif Hari Ini & Mendatang
        $pengumumanList = Pengumuman::aktif()
            ->where('tanggal_pengumuman', '>=', now()->toDateString())
            ->orderBy('prioritas', 'desc')
            ->orderBy('tanggal_pengumuman', 'asc')
            ->limit(5)
            ->get();

        // Flyer Pop-up untuk Siswa
        $flyerList = Flyer::getPopupFlyers('siswa', 3);

        // Grafik Performa Siswa
        $performaData = $this->getPerformaSiswa($siswa->id, $siswa->kelas_id);

        // Rekap Absensi Bulan Ini
        $rekapAbsen = $this->getRekapAbsensi($siswa->id);

        // Jadwal Hari Ini (Real Data)
        $hariIni = Carbon::now()->locale('id')->dayName;
        $hariIni = ucfirst($hariIni); // Senin, Selasa, etc.

        $jadwalHariIni = JadwalPelajaran::whereHas('kelas', function($q) use ($siswa) {
                $q->where('kelas.id', $siswa->kelas_id);
            })
            ->where('hari', $hariIni)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('jam_mulai')
            ->get();

        // Tugas & Deadline (untuk LMS - Check Settings)
        $setting = \App\Models\AppSetting::where('key', 'lms_allowed_jenjang')->first();
        $allowedJenjang = $setting ? json_decode($setting->value, true) : [];

        $tugasList = collect();
        if ($siswa->kelas && in_array($siswa->kelas->jenjang, $allowedJenjang)) {
            $tugasList = Tugas::where('kelas_id', $siswa->kelas_id)
                ->where('tanggal_deadline', '>=', now())
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('tanggal_deadline', 'asc')
                ->limit(5)
                ->get();
        }

        // Nilai Terbaru
        $nilaiTerbaru = Nilai::where('siswa_id', $siswa->id)
            ->where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Show LMS Button
        $showLmsButton = true;

        // Determine Semester
        $bulan = now()->month;
        $semester = ($bulan >= 7 && $bulan <= 12) ? 'Ganjil' : 'Genap';

        return view('siswa.sia.dashboard', [
            'siswa' => $siswa,
            'pengumuman' => $pengumumanList,
            'flyers' => $flyerList,
            'performa' => $performaData,
            'absensi' => $rekapAbsen,
            'jadwalHariIni' => $jadwalHariIni,
            'tugasList' => $tugasList,
            'nilaiTerbaru' => $nilaiTerbaru,
            'showLmsButton' => $showLmsButton,
            'semester' => $semester
        ]);
    }

    /**
     * Dashboard SIA - Deprecated, gunakan index() sebagai gantinya
     * Method ini tetap ada untuk backward compatibility
     */
    public function dashboardSIA()
    {
        return $this->index();
    }

    /**
     * Penilaian Harian - Deprecated, gunakan penilaian() sebagai gantinya
     * Method ini tetap ada untuk backward compatibility
     */
    public function penilaianHarian()
    {
        return $this->penilaian();
    }

    /**
     * Data Penilaian Harian
     */
    public function penilaian(Request $request)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Determine Semester
        $currentSemester = Nilai::getCurrentSemester();
        $semester = $request->get('semester', $currentSemester);

        // Ambil nilai siswa
        $nilaiList = Nilai::where('siswa_id', $siswa->id)
            ->where('kelas_id', $siswa->kelas_id)
            ->where('semester', $semester)
            ->with(['mataPelajaran', 'guru'])
            ->get();

        return view('siswa.sia.penilaian.index', compact('siswa', 'nilaiList', 'semester'));
    }

    /**
     * Daftar Tugas Siswa
     */
    public function tugas()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Ambil tugas untuk kelas siswa
        $tugasList = Tugas::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('deadline', 'asc')
            ->get();

        return view('siswa.sia.tugas.index', compact('siswa', 'tugasList'));
    }

    /**
     * Daftar Ujian Siswa
     */
    public function ujian()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Ambil ujian untuk kelas siswa
        $ujianList = Ujian::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('tanggal_ujian', 'asc')
            ->get();

        return view('siswa.sia.ujian.index', compact('siswa', 'ujianList'));
    }

    /**
     * Daftar Materi Pelajaran
     */
    public function materi()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Ambil materi untuk kelas siswa
        $materiList = Materi::where('kelas_id', $siswa->kelas_id)
            ->with(['mataPelajaran', 'guru'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('siswa.sia.materi.index', compact('siswa', 'materiList'));
    }

    /**
     * Rekap Presensi Siswa
     */
    public function presensi()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Ambil presensi siswa
        $presensiList = Presensi::where('siswa_id', $siswa->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Rekap per bulan
        $rekapBulanan = $this->getRekapAbsensi($siswa->id);

        return view('siswa.sia.presensi.index', compact('siswa', 'presensiList', 'rekapBulanan'));
    }

    /**
     * Helper: Hitung Performa Siswa (untuk Dashboard SIA)
     */
    private function getPerformaSiswa($siswaId, $kelasId)
    {
        // Total Tugas
        $totalTugas = Tugas::where('kelas_id', $kelasId)->count();

        // Tugas Selesai
        $tugasSelesai = Tugas::whereHas('tugasSiswa', function ($q) use ($siswaId) {
            $q->where('siswa_id', $siswaId)
                ->whereIn('status', ['dikerjakan', 'dinilai']);
        })
            ->count();

        // Total Ujian
        $totalUjian = Ujian::where('kelas_id', $kelasId)->count();

        // Ujian Selesai
        $ujianSelesai = Ujian::whereHas('ujianSiswa', function ($q) use ($siswaId) {
            $q->where('siswa_id', $siswaId)
                ->whereIn('status', ['selesai', 'dinilai']);
        })
            ->count();

        // Total Materi
        $totalMateri = Materi::where('kelas_id', $kelasId)->count();

        // Materi Dipelajari (anggap semua materi bisa diakses)
        $materiDipelajari = $totalMateri;

        // Hitung total dan selesai
        $total = $totalTugas + $totalUjian + $totalMateri;
        $selesai = $tugasSelesai + $ujianSelesai + $materiDipelajari;
        $proses = 0; // Bisa disesuaikan dengan logika Anda
        $belum = $total - $selesai;

        return [
            'selesai' => $selesai,
            'proses' => $proses,
            'belum' => max(0, $belum),
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
                'persentase' => $totalMateri > 0 ? 100 : 0
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

        $hadir = Presensi::where('siswa_id', $siswaId)
            ->where('status', 'hadir')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->count();

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

        return compact('hadir', 'sakit', 'izin', 'alpha');
    }

    /**
     * Dashboard khusus Alumni (Status Lulus)
     */
    private function dashboardAlumni($siswa)
    {
        // Ambil riwayat rapor terakhir
        $raporTerakhir = Rapor::where('siswa_id', $siswa->id)
            ->with('tahunAjaran')
            ->orderBy('semester', 'desc')
            ->first();
        
        return view('siswa.alumni.dashboard', compact('siswa', 'raporTerakhir'));
    }
}