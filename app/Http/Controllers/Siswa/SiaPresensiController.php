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
     * Tampilkan form pengajuan izin
     */
    public function ajukanIzin()
    {
        return $this->createIzin();
    }

    /**
     * Form pengajuan izin
     */
    public function createIzin()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        return view('siswa.sia.presensi.ajukan-izin', compact('siswa'));
    }

    /**
     * Proses pengajuan izin
     */
    public function storeIzin(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:sakit,izin',
            'keterangan' => 'required|string|max:500',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        // Cek apakah sudah ada presensi di tanggal tersebut
        $existingPresensi = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existingPresensi && $existingPresensi->status === 'hadir') {
            return back()->with('error', 'Anda sudah hadir pada tanggal tersebut');
        }

        // Upload bukti jika ada
        $buktiFoto = null;
        if ($request->hasFile('bukti')) {
            $buktiFoto = $request->file('bukti')->store('presensi/bukti', 'public');
        }

        // Simpan atau update presensi
        if ($existingPresensi) {
            $existingPresensi->update([
                'status' => $request->jenis,
                'keterangan' => $request->keterangan,
            ]);
        } else {
            Presensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tanggal' => $request->tanggal,
                'status' => $request->jenis,
                'keterangan' => $request->keterangan . ($buktiFoto ? " (Bukti: $buktiFoto)" : ""),
                'diinput_oleh' => $user->id,
            ]);
        }

        return redirect()->route('siswa.sia.presensi.index')
            ->with('success', 'Pengajuan izin berhasil diajukan. Menunggu validasi wali kelas.');
    }

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