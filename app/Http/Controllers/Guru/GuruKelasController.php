<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class GuruKelasController extends Controller
{
    /**
     * Tampilkan daftar KELAS yang diajar oleh guru ini di TA aktif.
     * Kelas dari TA lama tidak ditampilkan di sini — aksesnya lewat menu Arsip LMS.
     */
    public function index()
    {
        $guru = TenagaPendidik::where('user_id', auth()->id())->first();

        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        $taAktifId = TahunAjaran::where('is_active', true)->value('id');

        // Ambil semua kelas yang diajar di TA aktif (group by kelas)
        $kelasYangDiajar = GuruPengajarKelas::where('tenaga_pendidik_id', $guru->id)
            ->when($taAktifId, fn ($q) => $q->whereHas('kelas', fn ($k) => $k->where('tahun_ajaran_id', $taAktifId)))
            ->with(['kelas', 'mataPelajaran'])
            ->get()
            ->groupBy('kelas_id');

        $dataKelas = [];
        foreach ($kelasYangDiajar as $kelasId => $pengajaran) {
            $kelas = $pengajaran->first()->kelas;
            if ($kelas) {
                $dataKelas[] = [
                    'kelas' => $kelas,
                    'jumlah_mapel' => $pengajaran->count(),
                    'jumlah_siswa' => Siswa::where('kelas_id', $kelasId)->count(),
                    'mapel' => $pengajaran->pluck('mataPelajaran')
                ];
            }
        }

        return view('guru.kelas.index', compact('dataKelas', 'guru'));
    }

    /**
     * Tampilkan MATA PELAJARAN yang diajar di kelas tertentu.
     * Kelas dari TA lama diarahkan ke Arsip LMS agar guru tidak masuk LMS
     * (buat tugas/ujian dll) untuk kelas yang sudah tidak berjalan.
     */
    public function showMapel(Kelas $kelas)
    {
        $guru = TenagaPendidik::where('user_id', auth()->id())->first();

        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        $taAktifId = TahunAjaran::where('is_active', true)->value('id');
        if ($taAktifId && $kelas->tahun_ajaran_id != $taAktifId) {
            return redirect()->route('guru.lms.arsip.index')
                ->with('error', 'Kelas ini bukan dari tahun ajaran aktif. Gunakan menu Arsip LMS untuk mengakses konten kelas lama.');
        }

        // Ambil mata pelajaran yang diajar di kelas ini
        $mapelYangDiajar = GuruPengajarKelas::where('tenaga_pendidik_id', $guru->id)
            ->where('kelas_id', $kelas->id)
            ->with('mataPelajaran')
            ->get();

        if ($mapelYangDiajar->isEmpty()) {
            return redirect()->route('guru.kelas.index')->with('error', 'Anda tidak mengajar di kelas ini.');
        }

        $jumlahSiswa = Siswa::where('kelas_id', $kelas->id)->count();

        return view('guru.kelas.mapel', compact('kelas', 'mapelYangDiajar', 'jumlahSiswa', 'guru'));
    }
}