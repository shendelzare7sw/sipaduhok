<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;

class GuruKelasController extends Controller
{
    /**
     * Tampilkan daftar KELAS yang diajar oleh guru ini
     */
    public function index()
    {
        $guru = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
        }

        // Ambil semua kelas yang diajar (group by kelas)
        $kelasYangDiajar = GuruPengajarKelas::where('tenaga_pendidik_id', $guru->id)
            ->with(['kelas', 'mataPelajaran'])
            ->get()
            ->groupBy('kelas_id');

        $dataKelas = [];
        foreach ($kelasYangDiajar as $kelasId => $pengajaran) {
            $kelas = Kelas::find($kelasId);
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
     * Tampilkan MATA PELAJARAN yang diajar di kelas tertentu
     */
    public function showMapel(Kelas $kelas)
    {
        $guru = TenagaPendidik::where('user_id', auth()->id())->first();
        
        if (!$guru) {
            return redirect()->route('guru.dashboard')->with('error', 'Data guru tidak ditemukan.');
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