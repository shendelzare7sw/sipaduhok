<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SiaPresensiController extends Controller
{
    /**
     * Tampilkan halaman presensi
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Presensi bulan ini (grouped by week)
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        $presensi = Presensi::where('siswa_id', $siswa->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->weekOfMonth;
            });

        // Rekap total
        $rekap = [
            'hadir' => Presensi::where('siswa_id', $siswa->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'hadir')
                ->count(),
            'sakit' => Presensi::where('siswa_id', $siswa->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'sakit')
                ->count(),
            'izin' => Presensi::where('siswa_id', $siswa->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'izin')
                ->count(),
            'alpha' => Presensi::where('siswa_id', $siswa->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'alpha')
                ->count(),
        ];

        return view('siswa.sia.presensi.index', compact('siswa', 'presensi', 'rekap'));
    }

    /**
     * Note: Method ajukanIzin(), createIzin(), dan storeIzin() telah dihapus.
     * Fitur pengajuan izin dipindahkan ke OrangTuaController.
     * Siswa fokus pada pembelajaran, pengajuan izin dilakukan oleh orang tua sebagai bentuk pendampingan.
     */

    /**
     * Presensi otomatis (ketika siswa klik mata pelajaran di LMS)
     */
    public function presensiOtomatis()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan'], 404);
        }

        $today = now()->toDateString();

        // Cek apakah sudah presensi hari ini
        $existingPresensi = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$existingPresensi) {
            Presensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tanggal' => $today,
                'status' => 'hadir',
                'keterangan' => 'Presensi otomatis via LMS',
                'diinput_oleh' => $user->id,
            ]);

            return response()->json(['success' => true, 'message' => 'Presensi berhasil dicatat']);
        }

        return response()->json(['success' => true, 'message' => 'Sudah presensi hari ini']);
    }
}