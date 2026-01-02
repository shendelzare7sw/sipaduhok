<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Rapor;
use App\Models\Presensi;

class OrangTuaController extends Controller
{
    /**
     * Dashboard Orang Tua - Menampilkan ringkasan semua anak
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Ambil semua siswa yang orang tuanya adalah user ini
        $children = $user->children()->with(['kelas', 'cabang'])->get();

        if ($children->isEmpty()) {
            return view('orang-tua.dashboard', [
                'children' => collect(),
                'message' => 'Belum ada data anak yang terhubung dengan akun Anda.'
            ]);
        }

        // Hitung total tagihan dan pembayaran untuk semua anak
        $summary = [];
        foreach ($children as $child) {
            $totalTagihan = Tagihan::where('siswa_id', $child->id)->sum('jumlah');
            $totalBayar = Pembayaran::where('siswa_id', $child->id)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            $summary[$child->id] = [
                'total_tagihan' => $totalTagihan,
                'total_bayar' => $totalBayar,
                'sisa_tagihan' => $totalTagihan - $totalBayar,
            ];
        }

        return view('orang-tua.dashboard', compact('children', 'summary'));
    }

    /**
     * Detail Tagihan dan Pembayaran Anak
     * Orang tua bisa melihat dan melakukan pembayaran
     */
    public function tagihanAnak($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        // Ambil semua tagihan siswa
        $tagihan = Tagihan::where('siswa_id', $siswa->id)
            ->orderBy('tanggal_jatuh_tempo', 'desc')
            ->get();

        // Group tagihan berdasarkan jenis
        $tagihanGroup = $tagihan->groupBy('jenis_tagihan');

        // Total tagihan
        $totalTagihan = $tagihan->sum('jumlah');

        // Total sudah dibayar
        $totalBayar = Pembayaran::where('siswa_id', $siswa->id)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');

        // Sisa tagihan
        $sisaTagihan = $totalTagihan - $totalBayar;

        // Riwayat pembayaran
        $riwayatPembayaran = Pembayaran::where('siswa_id', $siswa->id)
            ->with('tagihan')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        return view('orang-tua.tagihan.index', compact(
            'siswa',
            'tagihan',
            'tagihanGroup',
            'totalTagihan',
            'totalBayar',
            'sisaTagihan',
            'riwayatPembayaran'
        ));
    }

    /**
     * Proses Pembayaran untuk Anak
     * Hanya orang tua yang bisa melakukan pembayaran
     */
    public function prosesBayar(Request $request, $siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $validated = $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:tunai,transfer,ewallet',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string',
        ]);

        $validated['siswa_id'] = $siswa->id;
        $validated['tanggal_bayar'] = now();
        $validated['status_validasi'] = 'pending';

        if ($request->hasFile('bukti_bayar')) {
            $validated['bukti_bayar'] = $request->file('bukti_bayar')
                ->store('pembayaran/bukti', 'public');
        }

        Pembayaran::create($validated);

        return redirect()->route('orang-tua.tagihan.anak', $siswa->id)
            ->with('success', 'Pembayaran berhasil diajukan. Menunggu validasi dari bendahara.');
    }

    /**
     * Lihat Rapor Anak
     */
    public function raporAnak($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        // Ambil semua rapor siswa
        $rapor = Rapor::where('siswa_id', $siswa->id)
            ->with('tahunAjaran')
            ->orderBy('semester', 'desc')
            ->get();

        return view('orang-tua.rapor.index', compact('siswa', 'rapor'));
    }

    /**
     * Detail Rapor Anak
     */
    public function detailRapor($raporId)
    {
        $user = Auth::user();

        // Ambil rapor dan pastikan itu milik anak dari orang tua yang login
        $rapor = Rapor::with(['siswa.kelas', 'tahunAjaran', 'nilai.mataPelajaran'])
            ->findOrFail($raporId);

        // Cek apakah siswa ini adalah anak dari orang tua yang login
        $isMyChild = $user->children()->where('siswa.id', $rapor->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke rapor ini.');
        }

        return view('orang-tua.rapor.detail', compact('rapor'));
    }

    /**
     * Lihat Presensi Anak
     */
    public function presensiAnak($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
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
                return \Carbon\Carbon::parse($item->tanggal)->weekOfMonth;
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

        return view('orang-tua.presensi.index', compact('siswa', 'presensi', 'rekap'));
    }

    /**
     * Tampilkan form pengajuan izin untuk anak
     * Orang tua yang mengajukan izin untuk anak mereka
     */
    public function ajukanIzin($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        return view('orang-tua.presensi.ajukan-izin', compact('siswa'));
    }

    /**
     * Proses pengajuan izin untuk anak
     * Pengajuan dilakukan oleh orang tua sebagai bentuk pendampingan
     */
    public function storeIzin(Request $request, $siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'jenis' => 'required|in:sakit,izin',
            'keterangan' => 'required|string|max:500',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Cek apakah sudah ada presensi di tanggal tersebut
        $existingPresensi = Presensi::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', $validated['tanggal'])
            ->first();

        if ($existingPresensi && $existingPresensi->status === 'hadir') {
            return back()->with('error', 'Anak Anda sudah hadir pada tanggal tersebut.');
        }

        // Upload bukti jika ada
        $buktiFoto = null;
        if ($request->hasFile('bukti')) {
            $buktiFoto = $request->file('bukti')->store('presensi/bukti', 'public');
        }

        $keterangan = $validated['keterangan'];
        if ($buktiFoto) {
            $keterangan .= " (Bukti: $buktiFoto)";
        }
        $keterangan .= " - Diajukan oleh orang tua ({$user->name})";

        // Simpan atau update presensi
        if ($existingPresensi) {
            $existingPresensi->update([
                'status' => $validated['jenis'],
                'keterangan' => $keterangan,
            ]);
        } else {
            Presensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tanggal' => $validated['tanggal'],
                'status' => $validated['jenis'],
                'keterangan' => $keterangan,
                'diinput_oleh' => $user->id,
            ]);
        }

        return redirect()->route('orang-tua.dashboard')
            ->with('success', "Pengajuan izin untuk {$siswa->nama_lengkap} berhasil diajukan. Menunggu validasi wali kelas.");
    }

    /**
     * Lihat riwayat pengajuan izin untuk anak
     */
    public function riwayatIzin($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        // Ambil pengajuan izin yang diajukan oleh orang tua ini
        $pengajuanIzin = Presensi::where('siswa_id', $siswa->id)
            ->whereIn('status', ['sakit', 'izin', 'alpha'])
            ->where('keterangan', 'LIKE', '%Diajukan oleh orang tua%')
            ->where('diinput_oleh', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('orang-tua.presensi.riwayat-izin', compact('siswa', 'pengajuanIzin'));
    }

    /**
     * Form edit pengajuan izin
     */
    public function editIzin($presensiId)
    {
        $user = Auth::user();

        $presensi = Presensi::with('siswa.kelas')->findOrFail($presensiId);

        // Pastikan siswa adalah anak dari orang tua yang login
        $isMyChild = $user->children()->where('siswa.id', $presensi->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        // Cek apakah sudah divalidasi
        if (str_contains($presensi->keterangan, 'Divalidasi')) {
            return redirect()->route('orang-tua.presensi.riwayat-izin', $presensi->siswa_id)
                ->with('error', 'Pengajuan yang sudah divalidasi tidak dapat diedit.');
        }

        return view('orang-tua.presensi.edit-izin', compact('presensi'));
    }

    /**
     * Update pengajuan izin
     */
    public function updateIzin(Request $request, $presensiId)
    {
        $user = Auth::user();

        $presensi = Presensi::with('siswa')->findOrFail($presensiId);

        // Pastikan siswa adalah anak dari orang tua yang login
        $isMyChild = $user->children()->where('siswa.id', $presensi->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        // Cek apakah sudah divalidasi
        if (str_contains($presensi->keterangan, 'Divalidasi')) {
            return redirect()->route('orang-tua.presensi.riwayat-izin', $presensi->siswa_id)
                ->with('error', 'Pengajuan yang sudah divalidasi tidak dapat diedit.');
        }

        $validated = $request->validate([
            'jenis' => 'required|in:sakit,izin',
            'keterangan' => 'required|string|max:500',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'hapus_bukti' => 'nullable|boolean',
        ]);

        // Extract old bukti path
        $oldBuktiPath = null;
        if (preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan, $matches)) {
            $oldBuktiPath = $matches[1];
        }

        // Handle bukti
        $buktiFoto = $oldBuktiPath; // Keep old bukti by default

        // Hapus bukti lama jika diminta
        if ($request->hapus_bukti) {
            if ($oldBuktiPath && \Storage::disk('public')->exists($oldBuktiPath)) {
                \Storage::disk('public')->delete($oldBuktiPath);
            }
            $buktiFoto = null;
        }

        // Upload bukti baru jika ada
        if ($request->hasFile('bukti')) {
            // Hapus file lama jika ada
            if ($oldBuktiPath && \Storage::disk('public')->exists($oldBuktiPath)) {
                \Storage::disk('public')->delete($oldBuktiPath);
            }
            $buktiFoto = $request->file('bukti')->store('presensi/bukti', 'public');
        }

        // Build keterangan
        $keterangan = $validated['keterangan'];
        if ($buktiFoto) {
            $keterangan .= " (Bukti: $buktiFoto)";
        }
        $keterangan .= " - Diajukan oleh orang tua ({$user->name})";

        // Update presensi
        $presensi->update([
            'status' => $validated['jenis'],
            'keterangan' => $keterangan,
        ]);

        return redirect()->route('orang-tua.presensi.riwayat-izin', $presensi->siswa_id)
            ->with('success', 'Pengajuan izin berhasil diperbarui.');
    }
}
