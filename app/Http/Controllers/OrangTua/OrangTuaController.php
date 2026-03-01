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
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;
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

        // Info tahun ajaran aktif
        $activeYear = \App\Models\TahunAjaran::where('is_active', true)->first();

        // Ambil semua tagihan siswa dengan relasi tahun ajaran
        $tagihanAll = Tagihan::where('siswa_id', $siswa->id)
            ->with('tahunAjaran')
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
        $tagihanCurrent = $tagihanAll->where('tahun_ajaran_id', $activeYear->id ?? 0);
        $tagihanArrears = $tagihanAll->where('tahun_ajaran_id', '!=', $activeYear->id ?? 0)->where('status', '!=', 'sudah_bayar');
        
        // Group tagihan CURRENT berdasarkan jenis
        $tagihanGroup = $tagihanCurrent->groupBy('jenis_tagihan');
        $tagihanGroup = $this->sortTagihanGroup($tagihanGroup);

        // Group ARREARS berdasarkan tahun ajaran lalu jenis
        $arrearsGroup = $tagihanArrears->groupBy('tahun_ajaran_id');

        // Calculate Totals Separately
        $totalTagihanCurrent = $tagihanCurrent->sum('jumlah');
        
        // Total Tunggakan (Use calculated sisa_tagihan)
        $totalTunggakan = $tagihanArrears->sum('sisa_tagihan'); 
        
        // Sisa tagihan CURRENT (Use calculated sisa_tagihan)
        $sisaTagihanCurrent = $tagihanCurrent->where('status', '!=', 'sudah_bayar')->sum('sisa_tagihan');

        // Total Yang Harus Dibayar (Current Sisa + Tunggakan)
        $grandTotalUnpaid = $sisaTagihanCurrent + $totalTunggakan;
        
        // Total sudah dibayar (Current only)
        // Kalkulasi: Total Awal - Sisa Sekarang
        $totalBayarCurrent = $totalTagihanCurrent - $sisaTagihanCurrent;

        // Riwayat pembayaran
        $riwayatPembayaran = Pembayaran::where('siswa_id', $siswa->id)
            ->with('tagihan')
            ->orderBy('created_at', 'desc')
            ->get();

        // Info pembayaran
        $infoPembayaran = \App\Models\InfoPembayaran::getInstance();

        return view('orang-tua.tagihan.index', [
            'siswa' => $siswa,
            'tagihan' => $tagihanAll,
            'tagihanGroup' => $tagihanGroup,
            'arrearsGroup' => $arrearsGroup,
            'totalTagihanCurrent' => $totalTagihanCurrent, // Tagihan Tahun Ini
            'totalTunggakan' => $totalTunggakan, // Tunggakan Masa Lalu
            'sisaTagihanCurrent' => $sisaTagihanCurrent,
            'grandTotalUnpaid' => $grandTotalUnpaid,
            'totalBayarCurrent' => $totalBayarCurrent,
            'riwayatPembayaran' => $riwayatPembayaran,
            'infoPembayaran' => $infoPembayaran,
            'activeYear' => $activeYear
        ]);
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

        // Cek jika user mencoba submit dengan metode tunai (bypass JS)
        if ($request->metode_pembayaran === 'tunai') {
            return redirect()->back()
                ->with('error', 'Pembayaran tunai tidak dapat diajukan secara online. Silakan datang langsung ke loket pembayaran sekolah.')
                ->withInput();
        }

        // Validation rules dengan conditional untuk bukti_bayar
        // Note: Orang tua hanya bisa pilih 'transfer' atau 'midtrans'
        // 'tunai' hanya bisa diinput oleh admin/bendahara
        $rules = [
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1000',
            'metode_pembayaran' => 'required|in:transfer,midtrans',
            'catatan' => 'nullable|string',
        ];

        // Bukti bayar WAJIB untuk transfer, OPSIONAL untuk midtrans
        if ($request->metode_pembayaran === 'transfer') {
            $rules['bukti_bayar'] = 'required|image|mimes:jpeg,png,jpg|max:10240';
        } else {
            $rules['bukti_bayar'] = 'nullable|image|mimes:jpeg,png,jpg|max:10240';
        }

        $validated = $request->validate($rules);

        // Generate kode pembayaran unik
        $validated['kode_pembayaran'] = $this->generateKodePembayaran();
        $validated['siswa_id'] = $siswa->id;
        $validated['tanggal_bayar'] = now();
        $validated['status_validasi'] = 'pending';

        // Upload bukti pembayaran jika ada
        if ($request->hasFile('bukti_bayar')) {
            $validated['bukti_pembayaran'] = $request->file('bukti_bayar')
                ->store('pembayaran/bukti', 'public');
        }

        // Untuk metode midtrans, redirect ke payment gateway
        if ($validated['metode_pembayaran'] === 'midtrans') {
            try {
                $midtransService = new MidtransService();

                // Check if Midtrans is configured
                if (!$midtransService->isConfigured()) {
                    return redirect()->route('orang-tua.tagihan.anak', $siswa->id)
                        ->with('error', 'Pembayaran digital belum dikonfigurasi. Silakan gunakan metode pembayaran lainnya.');
                }

                // Get tagihan detail
                $tagihan = Tagihan::findOrFail($validated['tagihan_id']);

                // Cek apakah sudah ada transaksi pending untuk tagihan ini yang belum expired (24 jam)
                $existingPendingPayment = Pembayaran::where('tagihan_id', $validated['tagihan_id'])
                    ->where('siswa_id', $siswa->id)
                    ->where('metode_pembayaran', 'midtrans')
                    ->where('status_validasi', 'pending')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->first();

                if ($existingPendingPayment) {
                    // Gunakan transaksi yang sudah ada, tapi generate order_id BARU
                    // Midtrans tidak mengizinkan reuse order_id yang sudah pernah disubmit
                    $newOrderId = $existingPendingPayment->kode_pembayaran . '-R' . time();

                    // Update pembayaran dengan order_id baru
                    $existingPendingPayment->update(['order_id' => $newOrderId]);

                    // Build customer details
                    $customerDetails = [
                        'first_name' => $siswa->nama_lengkap,
                        'email' => $user->email ?? 'noreply@sipaduhok.sch.id',
                        'phone' => $user->no_hp ?? '08123456789',
                    ];

                    // Build item details
                    $itemDetails = [
                        [
                            'id' => 'TAGIHAN-' . $tagihan->id,
                            'price' => (int) $existingPendingPayment->jumlah_bayar,
                            'quantity' => 1,
                            'name' => $tagihan->keterangan ?: ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan)),
                        ]
                    ];

                    // Build transaction params dengan order_id baru
                    $transactionParams = $midtransService->buildTransactionParams(
                        $newOrderId,
                        (int) $existingPendingPayment->jumlah_bayar,
                        $customerDetails,
                        $itemDetails
                    );

                    // Get Snap token
                    $snapToken = $midtransService->createSnapToken($transactionParams);

                    // Redirect to existing payment
                    return redirect()->route('orang-tua.pembayaran.snap', [
                        'pembayaran' => $existingPendingPayment->id,
                        'token' => encrypt($snapToken)
                    ]);
                }

                // Tidak ada transaksi pending, buat baru
                $orderId = $validated['kode_pembayaran'];

                // Build customer details
                $customerDetails = [
                    'first_name' => $siswa->nama_lengkap,
                    'email' => $user->email ?? 'noreply@sipaduhok.sch.id',
                    'phone' => $user->no_hp ?? '08123456789',
                ];

                // Build item details
                $itemDetails = [
                    [
                        'id' => 'TAGIHAN-' . $tagihan->id,
                        'price' => (int) $validated['jumlah_bayar'],
                        'quantity' => 1,
                        'name' => $tagihan->keterangan ?: ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan)),
                    ]
                ];

                // Build transaction params
                $transactionParams = $midtransService->buildTransactionParams(
                    $orderId,
                    (int) $validated['jumlah_bayar'],
                    $customerDetails,
                    $itemDetails
                );

                // Get Snap token
                $snapToken = $midtransService->createSnapToken($transactionParams);

                // Save pembayaran with pending status and additional fields
                $validated['payment_gateway'] = 'midtrans';
                $validated['order_id'] = $orderId;
                $validated['paid_by_parent_id'] = $user->id;

                $pembayaran = Pembayaran::create($validated);

                // Redirect to Snap payment page with token as query parameter (encrypted)
                return redirect()->route('orang-tua.pembayaran.snap', [
                    'pembayaran' => $pembayaran->id,
                    'token' => encrypt($snapToken)
                ]);

            } catch (\Exception $e) {
                \Log::error('Midtrans Payment Error: ' . $e->getMessage());

                return redirect()->route('orang-tua.tagihan.anak', $siswa->id)
                    ->with('error', 'Gagal memproses pembayaran digital: ' . $e->getMessage());
            }
        }

        // Untuk metode manual (tunai & transfer)
        Pembayaran::create($validated);

        return redirect()->route('orang-tua.tagihan.anak', $siswa->id)
            ->with('success', 'Pembayaran berhasil diajukan. Menunggu validasi dari bendahara.');
    }

    /**
     * Proses Pembayaran Massal (Bulk Payment)
     */
    public function processBulkPay(Request $request, $siswaId)
    {
        $user = Auth::user();

        // Pastikan siswa ini adalah anak dari orang tua yang login
        $siswa = $user->children()->find($siswaId);

        if (!$siswa) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke data siswa ini.');
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.tagihan_id' => 'required|exists:tagihan,id',
            'items.*.jumlah_bayar' => 'required|numeric|min:1',
            'total_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:transfer,midtrans',
            'bukti_bayar' => 'required_if:metode_pembayaran,transfer|image|mimes:jpeg,png,jpg|max:10240',
        ]);

        $orderId = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(10)) . '-' . date('YmdHis'); // Distinct Order ID
        $buktiPath = null;

        if ($request->hasFile('bukti_bayar')) {
            $buktiPath = $request->file('bukti_bayar')->store('pembayaran/bukti', 'public');
        }

        DB::beginTransaction();
        try {
            $pembayaranIds = [];
            $itemDetails = [];

            // Generate base payment code once to ensure sequence
            $basePaymentCode = $this->generateKodePembayaran();

            foreach ($validated['items'] as $index => $item) {
                $tagihan = Tagihan::findOrFail($item['tagihan_id']);

                // Security check: tagihan must belong to siswa
                if ($tagihan->siswa_id != $siswa->id)
                    continue;

                // Generate unique code using base + suffix
                // This prevents DB hit inside loop and ensures consistent naming
                $uniqueKode = $basePaymentCode . '-' . ($index + 1);

                $pembayaran = Pembayaran::create([
                    'kode_pembayaran' => $uniqueKode, // Unique per record
                    'siswa_id' => $siswa->id,
                    'tagihan_id' => $tagihan->id,
                    'tanggal_bayar' => now(),
                    'jumlah_bayar' => $item['jumlah_bayar'],
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'status_validasi' => 'pending',
                    'bukti_pembayaran' => $buktiPath,
                    'payment_gateway' => $validated['metode_pembayaran'] === 'midtrans' ? 'midtrans' : null,
                    'order_id' => $orderId, // Shared Order ID for Bulk Group
                    'paid_by_parent_id' => $user->id,
                ]);

                $pembayaranIds[] = $pembayaran->id;

                if ($validated['metode_pembayaran'] === 'midtrans') {
                    $itemDetails[] = [
                        'id' => 'TAGIHAN-' . $tagihan->id,
                        'price' => (int) $item['jumlah_bayar'],
                        'quantity' => 1,
                        'name' => mb_substr($tagihan->keterangan ?: ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan)), 0, 50),
                    ];
                }
            }

            if (empty($pembayaranIds)) {
                throw new \Exception('Tidak ada tagihan valid yang dipilih.');
            }

            if ($validated['metode_pembayaran'] === 'midtrans') {
                $midtransService = new MidtransService();
                $customerDetails = [
                    'first_name' => $siswa->nama_lengkap,
                    'email' => $user->email ?? 'noreply@sipaduhok.sch.id',
                    'phone' => $user->no_hp ?? '',
                ];

                $transactionParams = $midtransService->buildTransactionParams(
                    $orderId,
                    (int) $validated['total_bayar'],
                    $customerDetails,
                    $itemDetails
                );

                $snapToken = $midtransService->createSnapToken($transactionParams);

                DB::commit();

                // Redirect to snap (using the first pembayaran ID for the route placeholder, but token covers all)
                return redirect()->route('orang-tua.pembayaran.snap', [
                    'pembayaran' => $pembayaranIds[0],
                    'token' => encrypt($snapToken)
                ]);
            }

            DB::commit();

            // Notify Admin & Bendahara
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->notifyNewPayment($pembayaranIds, $user);
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim notifikasi pembayaran: ' . $e->getMessage());
            }

            $count = count($pembayaranIds);
            $message = $count > 1 
                ? "Pembayaran massal ($count tagihan) berhasil diajukan. Menunggu validasi."
                : "Pembayaran berhasil diajukan. Menunggu validasi.";

            return redirect()->route('orang-tua.tagihan.anak', $siswa->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
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

        // Ambil semua rapor siswa yang sudah diterbitkan
        $rapor = Rapor::where('siswa_id', $siswa->id)
            ->where('status', 'diterbitkan')
            ->with('tahunAjaran')
            ->orderBy('tahun_ajaran_id', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Cek Validasi Akses Rapor (3-level: Bendahara, Wali Kelas, Ketua PKBM)
        if (!$siswa->hasFullRaporAccess()) {
             return view('orang-tua.rapor.index', [
                'siswa' => $siswa,
                'rapor' => collect(),
                'locked' => true, // Pass locked status to view
                'message' => 'Akses rapor belum dibuka. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.'
             ]);
        }

        return view('orang-tua.rapor.index', compact('siswa', 'rapor'));
    }

    /**
     * Detail Rapor Anak
     */
    public function detailRapor($raporId)
    {
        $user = Auth::user();

        // Ambil rapor dan pastikan itu milik anak dari orang tua yang login
        $rapor = Rapor::with(['siswa.kelas', 'tahunAjaran', 'raporNilai.mataPelajaran', 'kegiatanEkstra'])
            ->findOrFail($raporId);

        // Cek apakah siswa ini adalah anak dari orang tua yang login
        $isMyChild = $user->children()->where('siswa.id', $rapor->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke rapor ini.');
        }

        // Cek Validasi Akses Rapor (3-level: Bendahara, Wali Kelas, Ketua PKBM)
        $siswa = $rapor->siswa;
        if (!$siswa->hasFullRaporAccess()) {
            return redirect()->route('orang-tua.rapor.anak', $siswa->id)
                ->with('error', 'Akses rapor untuk siswa ini belum dibuka. Rapor harus divalidasi oleh Bendahara, Wali Kelas, dan Ketua PKBM.');
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

        // Block izin for Alumni
        if ($siswa->status === 'lulus') {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Siswa yang sudah lulus tidak dapat mengajukan izin.');
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

        // Simpan atau update presensi
    $presensi = null;
    if ($existingPresensi) {
        $dataToUpdate = [
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
            'diinput_oleh' => $user->id, // FIX: Set diinput_oleh on update too
        ]);

        return redirect()->route('orang-tua.presensi.riwayat-izin', $presensi->siswa_id)
            ->with('success', 'Pengajuan izin berhasil diperbarui.');
    }

    /**
     * Show Midtrans Snap Payment Page
     */
    public function snapPayment(Request $request, $pembayaranId)
    {
        $user = Auth::user();

        // Get pembayaran with relationships
        $pembayaran = Pembayaran::with(['siswa', 'tagihan'])
            ->findOrFail($pembayaranId);

        // Check if user is the parent of the student
        $isMyChild = $user->children()->where('siswa.id', $pembayaran->siswa_id)->exists();

        if (!$isMyChild) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Fetch ALL payments with same order_id (for bulk display)
        $allPayments = Pembayaran::with('tagihan')
            ->where('order_id', $pembayaran->order_id)
            ->get();

        $totalBayar = $allPayments->sum('jumlah_bayar');

        // Check if payment is already rejected/expired or approved
        if ($pembayaran->status_validasi === 'ditolak') {
            return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                ->with('error', 'Sesi pembayaran telah kadaluarsa. Silakan ajukan pembayaran baru.');
        }

        if ($pembayaran->status_validasi === 'disetujui') {
            return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                ->with('success', 'Pembayaran ini sudah berhasil diproses.');
        }

        // Get snap token from query parameter or regenerate
        $snapToken = null;

        if ($request->has('token')) {
            try {
                $snapToken = decrypt($request->query('token'));
            } catch (\Exception $e) {
                \Log::error('Failed to decrypt snap token', ['error' => $e->getMessage()]);
            }
        }

        // If no token or decryption failed, regenerate token
        if (!$snapToken) {
            try {
                $midtransService = new MidtransService();

                // Build transaction params again
                $customerDetails = [
                    'first_name' => $pembayaran->siswa->nama_lengkap,
                    'email' => $user->email ?? 'noreply@sipaduhok.sch.id',
                    'phone' => $user->no_hp ?? '08123456789',
                ];

                // Build item details from ALL payments for BULK
                $itemDetails = [];
                foreach ($allPayments as $item) {
                    $itemDetails[] = [
                        'id' => 'TAGIHAN-' . $item->tagihan_id,
                        'price' => (int) $item->jumlah_bayar,
                        'quantity' => 1,
                        'name' => mb_substr($item->tagihan->keterangan ?: ucwords(str_replace('_', ' ', $item->tagihan->jenis_tagihan)), 0, 50),
                    ];
                }

                $transactionParams = $midtransService->buildTransactionParams(
                    $pembayaran->order_id,
                    (int) $totalBayar,
                    $customerDetails,
                    $itemDetails
                );

                $snapToken = $midtransService->createSnapToken($transactionParams);
            } catch (\Exception $e) {
                \Log::error('Failed to regenerate snap token', [
                    'error' => $e->getMessage(),
                    'order_id' => $pembayaran->order_id,
                    'pembayaran_id' => $pembayaran->id,
                ]);

                // If token generation fails (possibly due to expiration), redirect with clear message
                return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                    ->with('error', 'Sesi pembayaran telah kadaluarsa atau tidak valid. Silakan ajukan pembayaran baru.');
            }
        }

        $midtransService = new MidtransService();
        $clientKey = $midtransService->isConfigured()
            ? \App\Models\InfoPembayaran::getInstance()->midtrans_client_key
            : null;

        return view('orang-tua.pembayaran.snap', [
            'pembayaran' => $pembayaran,
            'allPayments' => $allPayments,
            'totalBayar' => $totalBayar,
            'snapToken' => $snapToken,
            'clientKey' => $clientKey
        ]);
    }

    /**
     * Handle Midtrans Finish/Success Redirect
     */
    public function snapFinish(Request $request)
    {
        $orderId = $request->query('order_id');
        $statusCode = $request->query('status_code');
        $transactionStatus = $request->query('transaction_status');

        \Log::info('Midtrans Snap Finish Redirect', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'transaction_status' => $transactionStatus,
        ]);

        // Find ALL pembayaran by order_id
        $payments = Pembayaran::where('order_id', $orderId)->get();

        if ($payments->isEmpty()) {
            \Log::error('Pembayaran (Bulk) not found in snapFinish', ['order_id' => $orderId]);

            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Data pembayaran tidak ditemukan. Order ID: ' . $orderId);
        }

        // Use the first record to identify student and generic details
        $firstPayment = $payments->first();

        // Update status for ALL payments in this order if webhook hasn't been called yet (fallback)
        $midtransService = new MidtransService();
        $newStatus = $midtransService->mapTransactionStatus($transactionStatus);

        foreach ($payments as $pembayaran) {
            $oldStatus = $pembayaran->status_validasi;

            // Only update if webhook hasn't updated it yet
            if ($oldStatus === 'pending' && $newStatus !== 'pending') {
                $pembayaran->update([
                    'status_validasi' => $newStatus,
                    'tanggal_validasi' => $newStatus === 'disetujui' ? now() : null,
                ]);

                // Create audit log for bendahara tracking
                \App\Models\FinancialAuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'update_status',
                    'model_type' => 'Pembayaran',
                    'model_id' => $pembayaran->id,
                    'old_values' => json_encode(['status_validasi' => $oldStatus]),
                    'new_values' => json_encode(['status_validasi' => $newStatus]),
                    'description' => "Pembayaran digital {$orderId} status updated from redirect (fallback): {$transactionStatus} → {$newStatus}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                // Auto-update status tagihan if payment approved
                if ($newStatus === 'disetujui') {
                    $pembayaran->tagihan->updateStatusBayar();

                    \Log::info('Tagihan status auto-updated from snapFinish', [
                        'tagihan_id' => $pembayaran->tagihan_id,
                        'new_tagihan_status' => $pembayaran->tagihan->fresh()->status,
                    ]);

                    // PENTING: Batalkan semua pembayaran pending lainnya untuk tagihan yang sama
                    $cancelledCount = Pembayaran::where('tagihan_id', $pembayaran->tagihan_id)
                        ->where('siswa_id', $pembayaran->siswa_id)
                        ->where('id', '!=', $pembayaran->id)
                        ->where('status_validasi', 'pending')
                        ->update([
                            'status_validasi' => 'ditolak',
                            'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar via transaksi lain (Order ID: ' . $orderId . ')',
                        ]);

                    if ($cancelledCount > 0) {
                        \App\Models\FinancialAuditLog::create([
                            'user_id' => Auth::id(),
                            'action' => 'auto_cancel_duplicates',
                            'model_type' => 'Pembayaran',
                            'model_id' => $pembayaran->id,
                            'old_values' => null,
                            'new_values' => json_encode([
                                'cancelled_count' => $cancelledCount,
                                'reason' => 'duplicate_payment_prevention',
                            ]),
                            'description' => "Otomatis membatalkan {$cancelledCount} pembayaran pending lainnya untuk tagihan yang sama",
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                        ]);
                    }
                }
            }
        }

        // Redirect to tagihan page with appropriate message
        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            return redirect()->route('orang-tua.tagihan.anak', $firstPayment->siswa_id)
                ->with('success', 'Pembayaran berhasil! Transaksi telah dikonfirmasi.');
        } elseif ($transactionStatus === 'pending') {
            return redirect()->route('orang-tua.tagihan.anak', $firstPayment->siswa_id)
                ->with('info', 'Pembayaran Anda sedang diproses. Mohon tunggu konfirmasi dari bank.');
        } else {
            return redirect()->route('orang-tua.tagihan.anak', $firstPayment->siswa_id)
                ->with('warning', 'Pembayaran dibatalkan atau gagal. Status: ' . $transactionStatus);
        }
    }

    /**
     * Lanjutkan pembayaran Midtrans yang pending
     */
    public function continuePayment($pembayaranId)
    {
        $user = Auth::user();

        // Get pembayaran with relationships
        $pembayaran = Pembayaran::with(['siswa', 'tagihan'])
            ->findOrFail($pembayaranId);

        // Check if user is the parent of the student
        $isMyChild = $user->children()->where('siswa.id', $pembayaran->siswa_id)->exists();

        if (!$isMyChild) {
            return redirect()->route('orang-tua.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke pembayaran ini.');
        }

        // Validasi: harus metode midtrans dan status pending
        if ($pembayaran->metode_pembayaran !== 'midtrans') {
            return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                ->with('error', 'Pembayaran ini bukan menggunakan metode Midtrans.');
        }

        if ($pembayaran->status_validasi !== 'pending') {
            return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                ->with('error', 'Pembayaran ini sudah tidak dalam status pending.');
        }

        // Cek apakah masih dalam waktu 24 jam
        if ($pembayaran->created_at < now()->subHours(24)) {
            return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                ->with('error', 'Sesi pembayaran telah kadaluarsa. Silakan buat pembayaran baru.');
        }

        try {
            $midtransService = new MidtransService();

            if (!$midtransService->isConfigured()) {
                return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                    ->with('error', 'Pembayaran digital belum dikonfigurasi. Silakan hubungi admin.');
            }

            // Build customer details
            $customerDetails = [
                'first_name' => $pembayaran->siswa->nama_lengkap,
                'email' => $user->email ?? 'noreply@sipaduhok.sch.id',
                'phone' => $user->no_hp ?? '08123456789',
            ];

            // Build item details
            $itemDetails = [
                [
                    'id' => 'TAGIHAN-' . $pembayaran->tagihan_id,
                    'price' => (int) $pembayaran->jumlah_bayar,
                    'quantity' => 1,
                    'name' => $pembayaran->tagihan->jenis_tagihan . ' - ' . ($pembayaran->tagihan->keterangan ?? $pembayaran->tagihan->nama_tagihan),
                ]
            ];

            // Generate NEW order_id untuk continue payment
            // Midtrans tidak mengizinkan reuse order_id yang sudah pernah disubmit
            $newOrderId = $pembayaran->kode_pembayaran . '-R' . time();

            // Update pembayaran dengan order_id baru
            $pembayaran->update(['order_id' => $newOrderId]);

            // Build transaction params dengan order_id baru
            $transactionParams = $midtransService->buildTransactionParams(
                $newOrderId,
                (int) $pembayaran->jumlah_bayar,
                $customerDetails,
                $itemDetails
            );

            // Get new Snap token
            $snapToken = $midtransService->createSnapToken($transactionParams);

            // Redirect to Snap payment page
            return redirect()->route('orang-tua.pembayaran.snap', [
                'pembayaran' => $pembayaran->id,
                'token' => encrypt($snapToken)
            ]);

        } catch (\Exception $e) {
            \Log::error('Continue Payment Error: ' . $e->getMessage(), [
                'pembayaran_id' => $pembayaran->id,
                'order_id' => $pembayaran->order_id,
            ]);

            return redirect()->route('orang-tua.tagihan.anak', $pembayaran->siswa_id)
                ->with('error', 'Gagal melanjutkan pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Generate kode pembayaran unik
     * Format: PAY-YYYYMMDD-XXXXX
     */
    private function generateKodePembayaran()
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        $searchPrefix = "{$prefix}-{$date}";

        // Cari kode terakhir hari ini berdasarkan format string, bukan created_at
        // Ini menangani kasus suffix (-1) dan timestamp offset
        $lastPayment = \App\Models\Pembayaran::where('kode_pembayaran', 'LIKE', "{$searchPrefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPayment && preg_match('/PAY-\d{8}-(\d{5})/', $lastPayment->kode_pembayaran, $matches)) {
            $lastNumber = intval($matches[1]);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $sequence = str_pad($newNumber, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$sequence}";
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

        return view('orang-tua.tagihan.invoice', [
            'pembayaran' => $mainPayment, // Menggunakan payment pertama sebagai header info
            'items' => $items,            // Mengirim collection items untuk tabel
            'siswa' => $siswa,
            'schoolInfo' => $schoolInfo
        ]);
    }

    /**
     * Request download rapor (orang tua).
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
            return redirect()->route('orang-tua.dashboard')->with('error', 'Link download tidak valid.');
        }

        if ($request->isExpired()) {
            return redirect()->route('orang-tua.dashboard')->with('error', 'Link download sudah kadaluarsa.');
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
}
