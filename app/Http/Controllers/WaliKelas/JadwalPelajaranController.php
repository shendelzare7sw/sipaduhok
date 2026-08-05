<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Http\Controllers\WaliKelas\Traits\WaliKelasHelper;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\PengaturanIstirahat;

class JadwalPelajaranController extends Controller
{
    use WaliKelasHelper;

    /**
     * Display jadwal pelajaran (READ-ONLY)
     * Wali kelas hanya bisa melihat jadwal yang sudah dibuat oleh admin
     */
    public function index(): View|RedirectResponse
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return view('wali-kelas.jadwal.index')->with([
                'error' => 'Data tenaga pendidik tidak ditemukan.',
                'kelas' => null,
                'kelasList' => collect(),
                'jadwalPerHari' => [],
                'hariList' => []
            ]);
        }

        $kelasList = $this->getKelasWali($tenagaPendidik);

        if ($kelasList->isEmpty()) {
            return view('wali-kelas.jadwal.index')->with([
                'error' => 'Anda belum ditugaskan sebagai wali kelas.',
                'kelas' => null,
                'kelasList' => collect(),
                'jadwalPerHari' => [],
                'hariList' => []
            ]);
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return $this->redirectToPilihKelas();
        }

        $kelas->load(['cabang', 'tahunAjaran']);

        // Get jadwal pelajaran per hari (READ-ONLY dari database)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = JadwalPelajaran::whereHas('kelas', function($q) use ($kelas) {
                    $q->where('kelas.id', $kelas->id);
                })
                ->where('hari', $hari)
                ->with(['mataPelajaran', 'guru'])
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('wali-kelas.jadwal.index', [
            'kelas' => $kelas,
            'kelasList' => $kelasList,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }

    /**
     * Print jadwal pelajaran
     */
    public function print()
    {
        $tenagaPendidik = $this->getTenagaPendidik();

        if (!$tenagaPendidik) {
            return redirect()->route('wali.jadwal-pelajaran')
                ->with('error', 'Data tenaga pendidik tidak ditemukan.');
        }

        if ($this->needsKelasSelection($tenagaPendidik)) {
            return $this->redirectToPilihKelas();
        }

        $kelas = $this->getSelectedKelas($tenagaPendidik);

        if (!$kelas) {
            return redirect()->route('wali.jadwal-pelajaran')
                ->with('error', 'Anda belum ditugaskan sebagai wali kelas.');
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            // Get jadwal pelajaran
            $jadwalPelajaran = JadwalPelajaran::whereHas('kelas', function($q) use ($kelas) {
                    $q->where('kelas.id', $kelas->id);
                })
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
                    'sort_time' => $jadwal->jam_mulai ? $jadwal->jam_mulai->format('H:i') : '00:00',
                ]);
            }

            // Add istirahat
            foreach ($istirahatList as $istirahat) {
                $merged->push([
                    'type' => 'istirahat',
                    'data' => $istirahat,
                    'jam_mulai' => $istirahat->jam_mulai,
                    'sort_time' => substr($istirahat->jam_mulai, 0, 5),
                ]);
            }

            // Sort by sort_time
            $jadwalPerHari[$hari] = $merged->sortBy('sort_time')->values();
        }

        return view('wali-kelas.jadwal.print', [
            'kelas' => $kelas,
            'jadwalPerHari' => $jadwalPerHari,
            'hariList' => $hariList,
        ]);
    }
}