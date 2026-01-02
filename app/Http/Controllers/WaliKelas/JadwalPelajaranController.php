<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\PengaturanIstirahat;

class JadwalPelajaranController extends Controller
{
    /**
     * Display jadwal pelajaran (READ-ONLY)
     * Wali kelas hanya bisa melihat jadwal yang sudah dibuat oleh admin
     */
    public function index(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();

        if (!$tenagaPendidik) {
            return view('wali-kelas.jadwal.index')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'kelas' => null,
                'jadwalPerHari' => [],
                'hariList' => []
            ]);
        }

        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)
            ->with(['cabang', 'tahunAjaran'])
            ->first();

        if (!$kelas) {
            return view('wali-kelas.jadwal.index')->with([
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'jadwalPerHari' => [],
                'hariList' => []
            ]);
        }

        // Get jadwal pelajaran per hari (READ-ONLY dari database)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = JadwalPelajaran::where('kelas_id', $kelas->id)
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('wali-kelas.jadwal.index', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }

    /**
     * Print jadwal pelajaran
     */
    public function print()
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        $kelas = Kelas::where('wali_kelas_id', $tenagaPendidik->id)->first();

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

        return view('wali-kelas.jadwal.print', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }
}