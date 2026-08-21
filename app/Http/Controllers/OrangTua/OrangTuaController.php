<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use App\Models\Rapor;
use App\Models\Presensi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\PaywuzPaymentStatusService;
use App\Services\PaywuzService;
use App\Models\RequestDownloadRapor;

class OrangTuaController extends Controller
{
    /**
     * Helper untuk mengurutkan jenis tagihan sesuai ketentuan
     */
    private function sortTagihanGroup($groupedTagihan)
    {
        $order = [
            'uang_pendaftaran', // Formulir
            'uang_pangkal',
            'kegiatan',         // Uang Kegiatan
            'buku',             // Buku Paket
            'seragam',
            'rapor_foto',
            'ujian',            // Ujian & Wisuda
            'akm',
            'spp'
        ];

        // Sort keys by priority index, then by original key
        return $groupedTagihan->sort(function ($itemsA, $itemsB) use ($order) {
            $keyA = $itemsA[0]->jenis_tagihan ?? '';
            $keyB = $itemsB[0]->jenis_tagihan ?? '';

            $indexA = 999;
            $indexB = 999;

            // Find index for A
            foreach ($order as $i => $orderKey) {
                if ($keyA === $orderKey || str_starts_with($keyA, $orderKey . '_')) {
                    $indexA = $i;
                    break;
                }
            }

            // Find index for B
            foreach ($order as $i => $orderKey) {
                if ($keyB === $orderKey || str_starts_with($keyB, $orderKey . '_')) {
                    $indexB = $i;
                    break;
                }
            }

            if ($indexA === $indexB) {
                // If same group (e.g. both SPP), sort by jatuh tempo
                $dateA = $itemsA[0]->tanggal_jatuh_tempo ?? '';
                $dateB = $itemsB[0]->tanggal_jatuh_tempo ?? '';
                return $dateA <=> $dateB;
            }

            return $indexA <=> $indexB;
        });
    }
    /**
     * Dashboard Wali Siswa - Menampilkan ringkasan semua anak
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Ambil semua siswa yang wali siswanya adalah user ini
        $children = $user->children()->with(['kelas', 'cabang'])->get();

        if ($children->isEmpty()) {
            return view('wali-siswa.dashboard', [
                'children' => collect(),
                'message' => 'Belum ada data anak yang terhubung dengan akun Anda.'
            ]);
        }

        $childIds = $children->pluck('id')->toArray();
        $this->syncPendingDigitalPayments($childIds);

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

        return view('wali-siswa.dashboard', compact('children', 'summary'));
    }

    /**
     * Detail Tagihan dan Pembayaran Anak
     * wali siswa bisa melihat dan melakukan pembayaran
     */
    public function tagihanAnak($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari wali siswa yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $this->syncPendingDigitalPayments([$siswa->id]);

        // Info tahun ajaran aktif
        $activeYear = \App\Models\TahunAjaran::where('is_active', true)->first();

        // Ambil semua tagihan siswa dengan relasi tahun ajaran + tagihan asal/alihan untuk carryover
        $tagihanAll = Tagihan::where('siswa_id', $siswa->id)
            ->with(['tahunAjaran', 'tagihanAsal.tahunAjaran', 'tagihanAlihan.tahunAjaran'])
            ->orderBy('tanggal_jatuh_tempo', 'desc')
            ->get();

        // Calculate sisa_tagihan for each item
        foreach ($tagihanAll as $item) {
            $terbayar = Pembayaran::where('tagihan_id', $item->id)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');
            $item->sisa_tagihan = $item->jumlah - $terbayar;
        }

        // Pisahkan tagihan berdasarkan tahun ajaran
        // CURRENT: TA aktif (termasuk tagihan carryover dari TA lama yang sudah dialihkan)
        $tagihanCurrent = $tagihanAll->where('tahun_ajaran_id', $activeYear->id ?? 0);

        // ARREARS DIALIHKAN: tagihan TA lama yang sudah dialihkan ke TA aktif (audit-trail)
        $arrearsDialihkan = $tagihanAll->where('tahun_ajaran_id', '!=', $activeYear->id ?? 0)
            ->whereNotNull('dialihkan_ke_id');

        // ARREARS BELUM DIALIHKAN: tagihan TA lama yang masih original & belum lunas (perlu diurus sekolah)
        $arrearsBelumDialihkan = $tagihanAll->where('tahun_ajaran_id', '!=', $activeYear->id ?? 0)
            ->whereNull('dialihkan_ke_id')
            ->where('status', '!=', 'sudah_bayar');

        // Group tagihan CURRENT berdasarkan jenis
        $tagihanGroup = $tagihanCurrent->groupBy('jenis_tagihan');
        $tagihanGroup = $this->sortTagihanGroup($tagihanGroup);

        // Group ARREARS berdasarkan tahun ajaran (untuk both section)
        $arrearsDialihkanGroup = $arrearsDialihkan->groupBy('tahun_ajaran_id');
        $arrearsBelumDialihkanGroup = $arrearsBelumDialihkan->groupBy('tahun_ajaran_id');

        // Calculate Totals Separately
        $totalTagihanCurrent = $tagihanCurrent->sum('jumlah');

        // Total Tunggakan: hanya yang BELUM DIALIHKAN (yang dialihkan sudah jadi tagihan baru di current)
        $totalTunggakan = $arrearsBelumDialihkan->sum('sisa_tagihan');

        // Sisa tagihan CURRENT (Use calculated sisa_tagihan)
        $sisaTagihanCurrent = $tagihanCurrent->where('status', '!=', 'sudah_bayar')->sum('sisa_tagihan');

        // Total Yang Harus Dibayar (Current Sisa + Tunggakan belum dialihkan)
        $grandTotalUnpaid = $sisaTagihanCurrent + $totalTunggakan;

        // Total sudah dibayar (Current only)
        $totalBayarCurrent = $totalTagihanCurrent - $sisaTagihanCurrent;

        // Riwayat pembayaran
        $riwayatPembayaran = Pembayaran::where('siswa_id', $siswa->id)
            ->with('tagihan')
            ->orderBy('created_at', 'desc')
            ->get();

        // Info pembayaran
        $infoPembayaran = \App\Models\InfoPembayaran::getInstance();
        $paymentMethods = [];
        if ($infoPembayaran->isPaywuzEnabled()) {
            try {
                $paymentMethods = app(PaywuzService::class)->paymentMethods();
            } catch (\Throwable $exception) {
                \Log::warning('Metode pembayaran digital tidak dapat dimuat.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return view('wali-siswa.tagihan.index', [
            'siswa' => $siswa,
            'tagihan' => $tagihanAll,
            'tagihanGroup' => $tagihanGroup,
            'arrearsDialihkanGroup' => $arrearsDialihkanGroup,
            'arrearsBelumDialihkanGroup' => $arrearsBelumDialihkanGroup,
            'totalTagihanCurrent' => $totalTagihanCurrent, // Tagihan Tahun Ini
            'totalTunggakan' => $totalTunggakan, // Tunggakan Masa Lalu
            'sisaTagihanCurrent' => $sisaTagihanCurrent,
            'grandTotalUnpaid' => $grandTotalUnpaid,
            'totalBayarCurrent' => $totalBayarCurrent,
            'riwayatPembayaran' => $riwayatPembayaran,
            'infoPembayaran' => $infoPembayaran,
            'paymentMethods' => $paymentMethods,
            'activeYear' => $activeYear
        ]);
    }

    /**
     * Lihat Rapor Anak
     */
    public function raporAnak($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari wali siswa yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        // Ambil semua rapor siswa yang sudah diterbitkan
        $rapor = Rapor::where('siswa_id', $siswa->id)
            ->where('status', 'diterbitkan')
            ->with('tahunAjaran')
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Cek Validasi Akses Rapor (3-level: Bendahara, Wali Kelas, Ketua PKBM)
        if (!$siswa->hasFullRaporAccess()) {
             return view('wali-siswa.rapor.index', [
                'siswa' => $siswa,
                'rapor' => collect(),
                'locked' => true, // Pass locked status to view
                'message' => 'Akses rapor belum dibuka. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.'
             ]);
        }

        return view('wali-siswa.rapor.index', compact('siswa', 'rapor'));
    }

    /**
     * Detail Rapor Anak
     */
    public function detailRapor($raporId)
    {
        $user = Auth::user();

        // Ambil rapor dan pastikan itu milik anak dari wali siswa yang login
        $rapor = Rapor::with(['siswa.kelas', 'tahunAjaran', 'raporNilai.mataPelajaran', 'kegiatanEkstra'])
            ->findOrFail($raporId);

        // Cek apakah siswa ini adalah anak dari wali siswa yang login
        $isMyChild = $user->children()->where('siswa.id', $rapor->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke rapor ini.');
        }

        // Cek Validasi Akses Rapor (3-level: Bendahara, Wali Kelas, Ketua PKBM)
        $siswa = $rapor->siswa;
        if (!$siswa->hasFullRaporAccess()) {
            return redirect()->route('wali-siswa.rapor.anak', $siswa->id)
                ->with('error', 'Akses rapor untuk siswa ini belum dibuka. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.');
        }

        return view('wali-siswa.rapor.detail', compact('rapor'));
    }

    /**
     * Lihat Presensi Anak
     */
    public function presensiAnak($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari wali siswa yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
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
            ->groupBy(function ($item) {
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

        return view('wali-siswa.presensi.index', compact('siswa', 'presensi', 'rekap'));
    }

    /**
     * Tampilkan form pengajuan izin untuk anak
     * wali siswa yang mengajukan izin untuk anak mereka
     */
    public function ajukanIzin($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari wali siswa yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        return view('wali-siswa.presensi.ajukan-izin', compact('siswa'));
    }

    /**
     * Proses pengajuan izin untuk anak
     * Pengajuan dilakukan oleh wali siswa sebagai bentuk pendampingan
     */
    public function storeIzin(Request $request, $siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari wali siswa yang login
        $siswa = $user->children()->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        // Block izin for Alumni
        if ($siswa->status === 'lulus') {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Siswa yang sudah lulus tidak dapat mengajukan izin.');
        }

        if (!$siswa->kelas_id) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Siswa belum memiliki kelas aktif, pengajuan izin belum dapat dibuat.');
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

        // Simpan atau update presensi sebagai pengajuan wali siswa.
        // Riwayat dan validasi wali kelas membaca status_validasi, bukan teks keterangan.
        $presensi = null;
        if ($existingPresensi) {
            $dataToUpdate = [
                'kelas_id' => $siswa->kelas_id,
                'status' => $validated['jenis'],
                'keterangan' => $validated['keterangan'],
                'status_validasi' => 'pending',
                'diinput_oleh' => $user->id,
            ];

            if ($buktiFoto) {
                $dataToUpdate['bukti_file'] = $buktiFoto;
            }

            $existingPresensi->update($dataToUpdate);
            $presensi = $existingPresensi;
        } else {
            $presensi = Presensi::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $siswa->kelas_id,
                'tanggal' => $validated['tanggal'],
                'status' => $validated['jenis'],
                'keterangan' => $validated['keterangan'],
                'bukti_file' => $buktiFoto,
                'status_validasi' => 'pending',
                'diinput_oleh' => $user->id,
            ]);
        }

        // Notify wali kelas about new izin request
        if ($presensi) {
            $presensi->load('siswa');
            app(\App\Services\NotificationService::class)->notifyIzinBaru($presensi);
        }

        return redirect()->route('wali-siswa.presensi.anak', $siswa->id)
            ->with('success', "Pengajuan izin untuk {$siswa->nama_lengkap} berhasil diajukan. Menunggu validasi wali kelas.");
    }

    /**
     * Lihat riwayat pengajuan izin untuk anak
     */
    public function riwayatIzin($siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari wali siswa yang login
        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        // Ambil pengajuan izin yang diajukan oleh wali siswa ini.
        // Gunakan field terstruktur agar pengajuan baru tetap tampil walau keterangan tidak berisi teks khusus.
        $pengajuanIzin = Presensi::where('siswa_id', $siswa->id)
            ->whereIn('status', ['sakit', 'izin', 'alpha'])
            ->where('diinput_oleh', $user->id)
            ->where(function ($query) {
                $query->whereIn('status_validasi', ['pending', 'disetujui', 'ditolak'])
                    ->orWhere('keterangan', 'LIKE', '%Diajukan oleh wali siswa%');
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('wali-siswa.presensi.riwayat-izin', compact('siswa', 'pengajuanIzin'));
    }

    /**
     * Lihat riwayat presensi harian anak.
     */
    public function riwayatPresensi(Request $request, $siswaId)
    {
        $user = Auth::user();

        $siswa = $user->children()->with(['kelas', 'cabang'])->find($siswaId);

        if (!$siswa) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $baseQuery = Presensi::where('siswa_id', $siswa->id)
            ->when($request->filled('tanggal_mulai'), function ($query) use ($request) {
                $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
            })
            ->when($request->filled('tanggal_akhir'), function ($query) use ($request) {
                $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
            });

        $riwayat = (clone $baseQuery)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(15)
            ->withQueryString();

        $rekap = [
            'hadir' => (clone $baseQuery)->where('status', 'hadir')->count(),
            'sakit' => (clone $baseQuery)->where('status', 'sakit')->count(),
            'izin' => (clone $baseQuery)->where('status', 'izin')->count(),
            'alpha' => (clone $baseQuery)->where('status', 'alpha')->count(),
        ];

        return view('wali-siswa.presensi.riwayat-presensi', compact('siswa', 'riwayat', 'rekap'));
    }

    /**
     * Form edit pengajuan izin
     */
    public function editIzin($presensiId)
    {
        $user = Auth::user();

        $presensi = Presensi::with('siswa.kelas')->findOrFail($presensiId);

        // Pastikan siswa adalah anak dari wali siswa yang login
        $isMyChild = $user->children()->where('siswa.id', $presensi->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        // Cek apakah sudah divalidasi
        if ($presensi->status_validasi && $presensi->status_validasi !== 'pending') {
            return redirect()->route('wali-siswa.presensi.riwayat-izin', $presensi->siswa_id)
                ->with('error', 'Pengajuan yang sudah divalidasi tidak dapat diedit.');
        }

        return view('wali-siswa.presensi.edit-izin', compact('presensi'));
    }

    /**
     * Update pengajuan izin
     */
    public function updateIzin(Request $request, $presensiId)
    {
        $user = Auth::user();

        $presensi = Presensi::with('siswa')->findOrFail($presensiId);

        // Pastikan siswa adalah anak dari wali siswa yang login
        $isMyChild = $user->children()->where('siswa.id', $presensi->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('wali-siswa.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data ini.');
        }

        // Cek apakah sudah divalidasi
        if ($presensi->status_validasi && $presensi->status_validasi !== 'pending') {
            return redirect()->route('wali-siswa.presensi.riwayat-izin', $presensi->siswa_id)
                ->with('error', 'Pengajuan yang sudah divalidasi tidak dapat diedit.');
        }

        if (!$presensi->siswa->kelas_id) {
            return redirect()->route('wali-siswa.presensi.riwayat-izin', $presensi->siswa_id)
                ->with('error', 'Siswa belum memiliki kelas aktif, pengajuan izin belum dapat diperbarui.');
        }

        $validated = $request->validate([
            'jenis' => 'required|in:sakit,izin',
            'keterangan' => 'required|string|max:500',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'hapus_bukti' => 'nullable|boolean',
        ]);

        // Handle bukti
        $oldBuktiPath = $presensi->bukti_file;
        $buktiFoto = $oldBuktiPath; // Keep old bukti by default

        // Hapus bukti lama jika diminta
        if ($request->hapus_bukti) {
            if ($oldBuktiPath && Storage::disk('public')->exists($oldBuktiPath)) {
                Storage::disk('public')->delete($oldBuktiPath);
            }
            $buktiFoto = null;
        }

        // Upload bukti baru jika ada
        if ($request->hasFile('bukti')) {
            // Hapus file lama jika ada
            if ($oldBuktiPath && Storage::disk('public')->exists($oldBuktiPath)) {
                Storage::disk('public')->delete($oldBuktiPath);
            }
            $buktiFoto = $request->file('bukti')->store('presensi/bukti', 'public');
        }

        // Update presensi
        $presensi->update([
            'kelas_id' => $presensi->siswa->kelas_id,
            'status' => $validated['jenis'],
            'keterangan' => $validated['keterangan'],
            'bukti_file' => $buktiFoto,
            'status_validasi' => 'pending',
            'diinput_oleh' => $user->id,
        ]);

        return redirect()->route('wali-siswa.presensi.riwayat-izin', $presensi->siswa_id)
            ->with('success', 'Pengajuan izin berhasil diperbarui.');
    }

    /**
     * Cetak Invoice Pembayaran Digital
     */
    public function cetakInvoice($id)
    {
        $user = Auth::user();

        // Cari pembayaran UTAMA berdasarkan ID
        $mainPayment = Pembayaran::with(['tagihan.tahunAjaran', 'siswa.kelas', 'siswa.cabang'])
            ->find($id);

        if (!$mainPayment) {
            abort(404);
        }

        // Pastikan akses
        $siswa = $user->children()->find($mainPayment->siswa_id);
        if (!$siswa) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil SEMUA pembayaran dalam satu Order ID yang sama (jika ada)
        // Jika order_id null/kosong, maka hanya ambil diri sendiri
        $items = collect([$mainPayment]);

        if (!empty($mainPayment->order_id)) {
            $items = Pembayaran::with(['tagihan.tahunAjaran'])
                ->where('order_id', $mainPayment->order_id)
                ->where('siswa_id', $mainPayment->siswa_id)
                ->where('payment_gateway', $mainPayment->payment_gateway)
                ->get();
        }

        // Dynamic school info based on student's branch
        $cabang = $siswa->cabang;
        $schoolInfo = [
            'nama' => $cabang ? $cabang->nama_cabang : config('app.name', 'PKBM INKLUSI SIPADUHOK'),
            'alamat' => $cabang ? $cabang->alamat : 'Jl. Pendidikan No. 123, Jakarta',
            'telepon' => $cabang ? $cabang->telepon : '021-12345678',
            'email' => 'info@sipaduhok.sch.id',
        ];

        return view('wali-siswa.tagihan.invoice', [
            'pembayaran' => $mainPayment, // Menggunakan payment pertama sebagai header info
            'items' => $items,            // Mengirim collection items untuk tabel
            'siswa' => $siswa,
            'schoolInfo' => $schoolInfo
        ]);
    }

    /**
     * Request download rapor (wali siswa).
     */
    public function requestDownloadRapor(Request $request, $raporId)
    {
        $request->validate([
            'alasan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $rapor = Rapor::findOrFail($raporId);

        // Verify parent-child relationship
        $isMyChild = $user->children()->where('siswa.id', $rapor->siswa_id)->exists();
        if (!$isMyChild) {
            return back()->with('error', 'Anda tidak memiliki akses ke rapor ini.');
        }

        // Check if already has pending request
        $existing = RequestDownloadRapor::where('rapor_id', $raporId)
            ->where('user_id', $user->id)
            ->where('status', 'menunggu')
            ->exists();

        if ($existing) {
            return back()->with('info', 'Anda sudah memiliki permintaan download yang sedang diproses.');
        }

        // Check if already has active download token
        $activeToken = RequestDownloadRapor::where('rapor_id', $raporId)
            ->where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where('download_expired_at', '>', now())
            ->exists();

        if ($activeToken) {
            return back()->with('info', 'Anda masih memiliki link download yang aktif.');
        }

        $downloadRequest = RequestDownloadRapor::create([
            'rapor_id' => $raporId,
            'user_id' => $user->id,
            'siswa_id' => $rapor->siswa_id,
            'alasan' => $request->alasan,
            'tanggal_request' => now(),
        ]);

        // Notify Wali Kelas
        $downloadRequest->load(['siswa.kelas', 'user']);
        app(\App\Services\NotificationService::class)->notifyRequestDownloadRapor($downloadRequest);

        return back()->with('success', 'Permintaan download rapor berhasil dikirim. Menunggu persetujuan.');
    }

    /**
     * Download rapor via approved token.
     */
    public function downloadRapor($token)
    {
        $user = Auth::user();

        $request = RequestDownloadRapor::where('download_token', $token)
            ->where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->with('rapor.siswa')
            ->first();

        if (!$request) {
            return redirect()->route('wali-siswa.dashboard')->with('error', 'Link download tidak valid.');
        }

        if ($request->isExpired()) {
            return redirect()->route('wali-siswa.dashboard')->with('error', 'Link download sudah kadaluarsa.');
        }

        $rapor = $request->rapor;

        // Render rapor to PDF (reuse existing preview view)
        $viewName = $rapor->jenis_rapor === 'tengah_semester'
            ? 'wali-kelas.rapor.print-pts'
            : 'wali-kelas.rapor.print-pas';

        $rapor->load(['siswa.kelas.waliKelas', 'tahunAjaran', 'raporNilai.mataPelajaran', 'raporNilai.nilai', 'kegiatanEkstra']);

        // Return printable view (user can Ctrl+P from browser)
        return view($viewName, compact('rapor'));
    }

    /** @param list<int> $studentIds */
    private function syncPendingDigitalPayments(array $studentIds): void
    {
        $orderIds = Pembayaran::query()
            ->whereIn('siswa_id', $studentIds)
            ->where('payment_gateway', 'paywuz')
            ->where('status_validasi', 'pending')
            ->whereNotNull('order_id')
            ->distinct()
            ->pluck('order_id');

        if ($orderIds->isEmpty()) {
            return;
        }

        $service = app(PaywuzPaymentStatusService::class);
        foreach ($orderIds as $orderId) {
            $service->sync((string) $orderId);
        }
    }
}
