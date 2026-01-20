<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\Siswa;

class GuruLmsController extends Controller
{
    /**
     * Dashboard LMS untuk mata pelajaran tertentu di kelas tertentu
     */
    public function dashboard($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();

        // Verifikasi akses
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        // Statistik
        $totalMateri = Materi::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->count();

        $totalTugas = Tugas::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->count();

        $totalUjian = Ujian::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->count();

        $totalSiswa = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->count();

        // Tugas yang perlu dikoreksi (submitted tapi belum dinilai)
        $tugasBelumDikoreksi = \DB::table('tugas_siswa')
            ->join('tugas', 'tugas_siswa.tugas_id', '=', 'tugas.id')
            ->where('tugas.kelas_id', $kelasId)
            ->where('tugas.mata_pelajaran_id', $mapelId)
            ->where('tugas.guru_id', $tenagaPendidik->id)
            ->where('tugas_siswa.status', 'dikerjakan')
            ->whereNull('tugas_siswa.nilai')
            ->count();

        // Recent activities
        $recentMateri = Materi::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('tanggal_upload', 'desc')
            ->limit(5)
            ->get();

        $recentTugas = Tugas::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Fetch pertemuans with eager loading
        $pertemuans = \App\Models\Pertemuan::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->with(['materi', 'tugas', 'ujian', 'forumDiskusi'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('guru.lms.dashboard', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'mapel' => $mataPelajaran,              // Alias untuk view
            'guru' => $tenagaPendidik,
            'totalMateri' => $totalMateri,
            'jumlahMateri' => $totalMateri,         // Alias untuk view
            'totalTugas' => $totalTugas,
            'jumlahTugas' => $totalTugas,           // Alias untuk view
            'totalUjian' => $totalUjian,
            'totalSiswa' => $totalSiswa,
            'jumlahSiswa' => $totalSiswa,           // Alias untuk view
            'tugasBelumDikoreksi' => $tugasBelumDikoreksi,
            'recentMateri' => $recentMateri,
            'materiTerbaru' => $recentMateri,       // Alias untuk view
            'recentTugas' => $recentTugas,
            'tugasTerbaru' => $recentTugas,         // Alias untuk view
            'pertemuans' => $pertemuans,
        ]);
    }

    /**
     * Verifikasi akses guru ke kelas dan mata pelajaran
     */
    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        $access = GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();

        if (!$access) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }
}