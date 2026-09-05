<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Services\NotificationService;
use App\Services\PaywuzPaymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::with('cabang')->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        $query = Pembayaran::with(['siswa', 'siswa.kelas', 'tagihan', 'validator'])
            ->withCount('groupTransactions');

        // Filter status validasi
        if ($request->filled('status')) {
            $query->where('status_validasi', $request->status);
        }

        // Filter metode pembayaran
        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        // Filter tanggal
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_pembayaran', 'like', '%'.$request->search.'%')
                    ->orWhereHas('siswa', function ($q2) use ($request) {
                        $q2->where('nama_lengkap', 'like', '%'.$request->search.'%');
                    });
            });
        }

        $pembayaranList = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // Hitung statistik
        $stats = [
            'pending' => Pembayaran::where('status_validasi', 'pending')->count(),
            'disetujui' => Pembayaran::where('status_validasi', 'disetujui')->count(),
            'ditolak' => Pembayaran::where('status_validasi', 'ditolak')->count(),
        ];

        return view('bendahara.pembayaran.index', [
            'pembayaranList' => $pembayaranList,
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaranAktif,
            'stats' => $stats,
            'filters' => $request->only(['status', 'metode', 'kelas_id', 'tanggal_dari', 'tanggal_sampai', 'search']),
        ]);
    }

    /**
     * Menampilkan riwayat pembayaran per siswa
     */
    public function riwayatSiswa($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $siswa = Siswa::with(['kelas', 'cabang'])->findOrFail($siswaId);

        // Ambil SEMUA tagihan siswa (lintas TA) — supaya tunggakan TA lama yg belum dialihkan
        // tetap muncul di riwayat pembayaran. Tagihan TA aktif + tagihan asal TA lama keduanya inclusive.
        $tagihan = Tagihan::where('siswa_id', $siswaId)->get();

        $tagihanIds = $tagihan->pluck('id');

        $pembayaranList = Pembayaran::with(['tagihan', 'validator'])
            ->withCount('groupTransactions')
            ->where('siswa_id', $siswaId)
            ->whereIn('tagihan_id', $tagihanIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Hitung total tagihan (Total Nominal Awal)
        $totalTagihan = $tagihan->sum('jumlah');

        // Hitung total yang SUDAH dibayar (Disetujui)
        $totalTerbayar = Pembayaran::where('siswa_id', $siswaId)
            ->whereIn('tagihan_id', $tagihanIds)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');

        // Sisa tagihan = Total - Terbayar
        $sisaTagihan = $totalTagihan - $totalTerbayar;

        $totalPending = Pembayaran::where('siswa_id', $siswaId)
            ->whereIn('tagihan_id', $tagihanIds)
            ->where('status_validasi', 'pending')
            ->sum('jumlah_bayar');

        // Get jenis tagihan mapping
        $jenisTagihan = config('sipaduhok.jenis_tagihan', [
            'spp' => 'SPP',
            'daftar_ulang' => 'Daftar Ulang',
            'seragam' => 'Seragam',
            'buku' => 'Buku',
            'kegiatan' => 'Kegiatan',
            'lainnya' => 'Lainnya',
        ]);

        return view('bendahara.pembayaran.riwayat-siswa', [
            'siswa' => $siswa,
            'pembayaran' => $pembayaranList,
            'totalTagihan' => $totalTagihan,
            'totalTerbayar' => $totalTerbayar,
            'totalPending' => $totalPending,
            'sisaTagihan' => $sisaTagihan,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $jenisTagihan,
        ]);
    }

    /**
     * Form validasi pembayaran
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'siswa.kelas', 'siswa.cabang', 'tagihan', 'validator'])
            ->findOrFail($id);

        return view('bendahara.pembayaran.show', [
            'pembayaran' => $pembayaran,
        ]);
    }

    /**
     * Validasi pembayaran (setuju/tolak)
     */
    public function validasi(Request $request, $id)
    {
        $targetPembayaran = Pembayaran::findOrFail($id);

        if ($targetPembayaran->payment_gateway === 'paywuz') {
            app(\App\Services\PaywuzPaymentStatusService::class)->sync((string) $targetPembayaran->order_id);

            return redirect()->back()->with(
                'info',
                'Pembayaran digital diverifikasi otomatis dari penyedia pembayaran dan tidak dapat disetujui atau ditolak manual.',
            );
        }

        $request->validate([
            'status_validasi' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Check if this payment is part of a bulk transaction (has order_id)
            $relatedPayments = collect([$targetPembayaran]);

            if ($targetPembayaran->order_id) {
                $relatedPayments = Pembayaran::where('order_id', $targetPembayaran->order_id)
                    ->where('status_validasi', 'pending')
                    ->get();

                // If for some reason the target ID isn't in the list (e.g. status changed concurrently), ensure it's included or handled
                if (! $relatedPayments->contains('id', $targetPembayaran->id)) {
                    $relatedPayments->push($targetPembayaran);
                }
            }

            $countUpdated = 0;

            foreach ($relatedPayments as $pembayaran) {
                $pembayaran->update([
                    'status_validasi' => $request->status_validasi,
                    'divalidasi_oleh' => auth()->id(),
                    'tanggal_validasi' => now(),
                    'catatan' => $request->catatan,
                ]);
                $countUpdated++;

                // Jika disetujui, update status tagihan dan batalkan pembayaran pending lainnya
                if ($request->status_validasi === 'disetujui' && $pembayaran->tagihan) {
                    $tagihan = $pembayaran->tagihan;

                    // Hitung total pembayaran yang disetujui untuk tagihan ini (termasuk yang baru saja diupdate)
                    $totalBayar = Pembayaran::where('tagihan_id', $tagihan->id)
                        ->where('status_validasi', 'disetujui')
                        ->sum('jumlah_bayar');

                    if ($totalBayar >= $tagihan->jumlah) {
                        $tagihan->update(['status' => 'sudah_bayar']);

                        // PENTING: Batalkan semua pembayaran pending lainnya untuk tagihan yang sama
                        $cancelledCount = Pembayaran::where('tagihan_id', $tagihan->id)
                            ->where('siswa_id', $pembayaran->siswa_id)
                            ->where('id', '!=', $pembayaran->id)
                            ->where('status_validasi', 'pending')
                            ->where(fn ($query) => $query
                                ->whereNull('payment_gateway')
                                ->orWhere('payment_gateway', '!=', 'paywuz'))
                            ->update([
                                'status_validasi' => 'ditolak',
                                'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar via transaksi lain (Kode: '.$pembayaran->kode_pembayaran.')',
                                'divalidasi_oleh' => auth()->id(),
                                'tanggal_validasi' => now(),
                            ]);

                        if ($cancelledCount > 0) {
                            FinancialAuditLog::create([
                                'user_id' => auth()->id(),
                                'action' => 'auto_cancel_duplicates',
                                'model_type' => 'Pembayaran',
                                'model_id' => $pembayaran->id,
                                'old_values' => null,
                                'new_values' => json_encode([
                                    'cancelled_count' => $cancelledCount,
                                    'reason' => 'duplicate_payment_prevention',
                                ]),
                                'description' => "Otomatis membatalkan {$cancelledCount} pembayaran pending lainnya untuk tagihan yang sama setelah pembayaran {$pembayaran->kode_pembayaran} disetujui",
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                            ]);
                        }
                    } elseif ($totalBayar > 0) {
                        // Partial payment - set status cicilan
                        $tagihan->update(['status' => 'cicilan']);
                    }
                }
            }

            DB::commit();

            if ($request->status_validasi === 'disetujui') {
                $this->cancelPendingPaywuzDuplicates($relatedPayments);
            }

            // Auto-validasi akses ujian/rapor jika siswa sudah lunas
            if ($request->status_validasi === 'disetujui') {
                $processedSiswaIds = [];
                foreach ($relatedPayments as $pembayaran) {
                    if ($pembayaran->siswa_id && ! in_array($pembayaran->siswa_id, $processedSiswaIds)) {
                        $siswa = \App\Models\Siswa::find($pembayaran->siswa_id);
                        if ($siswa) {
                            app(\App\Services\ValidasiAksesService::class)->autoValidasiSetelahBayar($siswa);
                        }
                        $processedSiswaIds[] = $pembayaran->siswa_id;
                    }
                }
            }

            // Notify wali siswa about payment validation
            $notificationService = app(NotificationService::class);
            if ($request->status_validasi === 'disetujui') {
                foreach ($relatedPayments as $pembayaran) {
                    $pembayaran->load('siswa.orangTua');
                    $notificationService->notifyPembayaranValidasi($pembayaran);
                }
            } else {
                // Notify rejection
                foreach ($relatedPayments as $pembayaran) {
                    $pembayaran->load('siswa.orangTua');
                    $notificationService->notifyPembayaranDitolak($pembayaran, $request->catatan_validasi);
                }
            }

            $message = $request->status_validasi === 'disetujui'
                ? 'Pembayaran berhasil divalidasi'
                : 'Pembayaran ditolak';

            if ($countUpdated > 1) {
                $message .= " ({$countUpdated} item dalam transaksi bulk).";
            } else {
                $message .= '.';
            }

            return redirect_to_previous('bendahara.pembayaran.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Input pembayaran kasir (tunai)
     */
    public function create($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $siswa = Siswa::with(['kelas', 'cabang'])->findOrFail($siswaId);

        // Ambil tagihan yang belum lunas (status != sudah_bayar)
        // Note: Cicilan masuk di sini
        $tagihanBelumLunas = Tagihan::where('siswa_id', $siswaId)
            ->where('status', '!=', 'sudah_bayar')
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->get();

        // Hitung sisa tagihan per item dan total sisa secara akurat
        // (Mengurangi pembayaran yang sudah masuk untuk tagihan cicilan)
        $sisaTagihan = 0;
        foreach ($tagihanBelumLunas as $tagihan) {
            $terbayar = Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');
            $tagihan->sisa_per_item = $tagihan->jumlah - $terbayar;
            $sisaTagihan += $tagihan->sisa_per_item;
        }

        // Total tagihan untuk tahun ajaran aktif (untuk info)
        $totalTagihan = Tagihan::where('siswa_id', $siswaId)
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->sum('jumlah');

        $totalBayar = $totalTagihan - $sisaTagihan;

        // Get jenis tagihan mapping
        $jenisTagihan = config('sipaduhok.jenis_tagihan', [
            'spp' => 'SPP',
            'daftar_ulang' => 'Daftar Ulang',
            'seragam' => 'Seragam',
            'buku' => 'Buku',
            'kegiatan' => 'Kegiatan',
            'lainnya' => 'Lainnya',
        ]);

        return view('bendahara.pembayaran.create', [
            'siswa' => $siswa,
            'tagihanBelumLunas' => $tagihanBelumLunas,
            'tahunAjaran' => $tahunAjaranAktif,
            'sisaTagihan' => $sisaTagihan,
            'totalTagihan' => $totalTagihan,
            'totalBayar' => $totalBayar,
            'jenisTagihan' => $jenisTagihan,
        ]);
    }

    /**
     * Simpan pembayaran tunai dari loket
     */
    public function store(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        normalisasi_input_rupiah($request, ['jumlah_bayar']);

        $request->validate([
            'tagihan_ids' => 'required|array|min:1',
            'tagihan_ids.*' => 'exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai',
            'catatan' => 'nullable|string|max:500',
        ]);

        $approvedPayments = collect();
        DB::beginTransaction();
        try {
            $validasiLangsung = $request->has('validasi_langsung');

            // Generate a shared Order ID for this transaction batch
            // This links all payments made in this single request together
            $orderId = 'ORD-'.strtoupper(Str::random(10)).'-'.date('YmdHis');

            // Ambil input nominal bayar (cleanup format currency)
            $inputNominals = $request->input('nominal_bayar', []);

            // Proses setiap tagihan yang dipilih
            foreach ($request->tagihan_ids as $tagihanId) {
                $tagihan = Tagihan::findOrFail($tagihanId);

                // Hitung sisa tagihan aktual
                $totalSudahBayar = Pembayaran::where('tagihan_id', $tagihanId)
                    ->where('status_validasi', 'disetujui')
                    ->sum('jumlah_bayar');
                $sisaTagihan = $tagihan->jumlah - $totalSudahBayar;

                // Tentukan jumlah bayar berdasarkan input dan jenis tagihan
                $jumlahBayar = $sisaTagihan; // Default ke sisa tagihan

                // Jika bukan SPP, gunakan input user (jika ada)
                if ($tagihan->jenis_tagihan !== 'spp' && isset($inputNominals[$tagihanId])) {
                    $cleanNominal = preg_replace('/\D/', '', $inputNominals[$tagihanId]);
                    if (is_numeric($cleanNominal) && $cleanNominal > 0) {
                        $jumlahBayar = (int) $cleanNominal;
                    }
                }

                // Safety: Jangan biarkan bayar lebih dari sisa
                // VALIDASI KETAT: Cek jika input melebihi sisa tagihan
                if ($jumlahBayar > $sisaTagihan) {
                    $namaTagihan = $tagihan->keterangan ?: ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan));
                    $formattedInput = number_format($jumlahBayar, 0, ',', '.');
                    $formattedSisa = number_format($sisaTagihan, 0, ',', '.');

                    // Rollback transaksi dan lempar error
                    DB::rollBack();

                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Pembayaran untuk tagihan '{$namaTagihan}' melebihi sisa tagihan! (Input: Rp {$formattedInput}, Sisa: Rp {$formattedSisa})");
                }

                // Generate kode pembayaran unik per tagihan
                $kodePembayaran = 'PAY-'.strtoupper(Str::random(8)).'-'.date('Ymd');

                $pembayaran = Pembayaran::create([
                    'tagihan_id' => $tagihanId,
                    'siswa_id' => $siswaId,
                    'kode_pembayaran' => $kodePembayaran,
                    'order_id' => $orderId, // Link grouping for receipt
                    'jumlah_bayar' => $jumlahBayar,
                    'tanggal_bayar' => $request->tanggal_bayar.' '.now()->format('H:i:s'),
                    'metode_pembayaran' => 'tunai',
                    'status_validasi' => $validasiLangsung ? 'disetujui' : 'pending',
                    'divalidasi_oleh' => $validasiLangsung ? auth()->id() : null,
                    'tanggal_validasi' => $validasiLangsung ? now() : null,
                    'catatan' => $request->catatan,
                ]);

                if ($validasiLangsung) {
                    $approvedPayments->push($pembayaran);
                }

                // Jika validasi langsung, update status tagihan dan batalkan pending lainnya
                if ($validasiLangsung) {
                    $totalBayar = Pembayaran::where('tagihan_id', $tagihanId)
                        ->where('status_validasi', 'disetujui')
                        ->sum('jumlah_bayar');

                    if ($totalBayar >= $tagihan->jumlah) {
                        $tagihan->update(['status' => 'sudah_bayar']);

                        // Batalkan semua pembayaran pending lainnya untuk tagihan ini
                        $cancelledCount = Pembayaran::where('tagihan_id', $tagihanId)
                            ->where('siswa_id', $siswaId)
                            ->where('id', '!=', $pembayaran->id)
                            ->where('status_validasi', 'pending')
                            ->where(fn ($query) => $query
                                ->whereNull('payment_gateway')
                                ->orWhere('payment_gateway', '!=', 'paywuz'))
                            ->update([
                                'status_validasi' => 'ditolak',
                                'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar tunai di loket (Kode: '.$kodePembayaran.')',
                                'divalidasi_oleh' => auth()->id(),
                                'tanggal_validasi' => now(),
                            ]);

                        if ($cancelledCount > 0) {
                            FinancialAuditLog::create([
                                'user_id' => auth()->id(),
                                'action' => 'auto_cancel_duplicates',
                                'model_type' => 'Pembayaran',
                                'model_id' => $pembayaran->id,
                                'old_values' => null,
                                'new_values' => json_encode([
                                    'cancelled_count' => $cancelledCount,
                                    'reason' => 'cash_payment_at_counter',
                                ]),
                                'description' => "Otomatis membatalkan {$cancelledCount} pembayaran pending saat pembayaran tunai di loket untuk tagihan yang sama",
                                'ip_address' => request()->ip(),
                                'user_agent' => request()->userAgent(),
                            ]);
                        }
                    } elseif ($totalBayar > 0) {
                        $tagihan->update(['status' => 'cicilan']);
                    }
                }
            }

            DB::commit();

            if ($validasiLangsung) {
                $this->cancelPendingPaywuzDuplicates($approvedPayments);
            }

            $message = $validasiLangsung
                ? 'Pembayaran tunai berhasil dicatat dan divalidasi.'
                : 'Pembayaran berhasil dicatat. Menunggu validasi.';

            return redirect()->route('bendahara.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Validasi langsung (approve langsung untuk pembayaran tunai)
     */
    public function validasiLangsung(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        normalisasi_input_rupiah($request, ['jumlah_bayar']);

        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $kodePembayaran = 'PAY-'.strtoupper(Str::random(8)).'-'.date('Ymd');

            $pembayaran = Pembayaran::create([
                'tagihan_id' => $request->tagihan_id,
                'siswa_id' => $siswaId,
                'kode_pembayaran' => $kodePembayaran,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar.' '.now()->format('H:i:s'),
                'metode_pembayaran' => 'tunai',
                'status_validasi' => 'disetujui',
                'divalidasi_oleh' => auth()->id(),
                'tanggal_validasi' => now(),
                'catatan' => $request->catatan,
            ]);

            // Update status tagihan jika sudah lunas
            $tagihan = Tagihan::find($request->tagihan_id);
            $totalBayar = Pembayaran::where('tagihan_id', $tagihan->id)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            if ($totalBayar >= $tagihan->jumlah) {
                $tagihan->update(['status' => 'sudah_bayar']);

                // Batalkan semua pembayaran pending lainnya untuk tagihan ini
                $cancelledCount = Pembayaran::where('tagihan_id', $tagihan->id)
                    ->where('siswa_id', $siswaId)
                    ->where('id', '!=', $pembayaran->id)
                    ->where('status_validasi', 'pending')
                    ->where(fn ($query) => $query
                        ->whereNull('payment_gateway')
                        ->orWhere('payment_gateway', '!=', 'paywuz'))
                    ->update([
                        'status_validasi' => 'ditolak',
                        'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar tunai di loket (Kode: '.$kodePembayaran.')',
                        'divalidasi_oleh' => auth()->id(),
                        'tanggal_validasi' => now(),
                    ]);

                if ($cancelledCount > 0) {
                    FinancialAuditLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'auto_cancel_duplicates',
                        'model_type' => 'Pembayaran',
                        'model_id' => $pembayaran->id,
                        'old_values' => null,
                        'new_values' => json_encode([
                            'cancelled_count' => $cancelledCount,
                            'reason' => 'cash_payment_direct_validation',
                        ]),
                        'description' => "Otomatis membatalkan {$cancelledCount} pembayaran pending saat pembayaran tunai langsung divalidasi",
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            } elseif ($totalBayar > 0) {
                $tagihan->update(['status' => 'cicilan']);
            }

            DB::commit();

            $this->cancelPendingPaywuzDuplicates([$pembayaran]);

            return redirect()->route('bendahara.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', 'Pembayaran tunai berhasil dicatat dan divalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    /**
     * Cetak kwitansi pembayaran
     */
    public function cetakKwitansi($id)
    {
        $pembayaran = Pembayaran::with(['siswa', 'siswa.kelas', 'siswa.cabang', 'siswa.studentParents.parent', 'tagihan', 'validator'])
            ->findOrFail($id);

        // Only allow printing for approved payments
        if ($pembayaran->status_validasi !== 'disetujui') {
            return redirect()->back()->with('error', 'Hanya pembayaran yang sudah divalidasi yang dapat dicetak.');
        }

        // Get all related payments if they share the same order_id (bulk payment)
        $relatedPayments = collect([$pembayaran]);
        if ($pembayaran->order_id) {
            $relatedPayments = Pembayaran::with(['tagihan'])
                ->where('order_id', $pembayaran->order_id)
                ->where('status_validasi', 'disetujui')
                ->get();
        }

        // Calculate total from all related payments
        $totalBayar = $relatedPayments->sum('jumlah_bayar');

        // Calculate Sisa Tagihan Current for each item in receipt
        foreach ($relatedPayments as $item) {
            if ($item->tagihan) {
                // Total yang SUDAH dibayar (termasuk pembayaran ini dan lainnya yg disetujui)
                $totalDibayar = Pembayaran::where('tagihan_id', $item->tagihan_id)
                    ->where('status_validasi', 'disetujui')
                    ->sum('jumlah_bayar');

                // Sisa saat ini
                $item->sisa_current = max(0, $item->tagihan->jumlah - $totalDibayar);
                // Flag lunas
                $item->is_lunas = $item->sisa_current <= 0;
            } else {
                $item->sisa_current = 0;
                $item->is_lunas = true;
            }
        }

        // Get school info from student's branch
        $cabang = $pembayaran->siswa->cabang;
        $schoolInfo = [
            'nama' => $cabang ? $cabang->nama_cabang : config('app.name', 'PKBM INKLUSI SIPADUHOK'),
            'alamat' => $cabang ? $cabang->alamat : 'Jl. Pendidikan No. 123, Jakarta',
            'telepon' => $cabang ? $cabang->telepon : '021-12345678',
            'email' => 'info@sipaduhok.sch.id', // Email tetap sama untuk semua cabang
        ];

        // Get primary parent name for signature
        // Using orangTua relationship which maps to the User model of parents
        $parent = $pembayaran->siswa->orangTua->first();

        $parentName = $parent ? $parent->name : '......................';

        // Get jenis tagihan mapping
        $jenisTagihan = config('sipaduhok.jenis_tagihan', [
            'spp' => 'SPP',
            'daftar_ulang' => 'Daftar Ulang',
            'seragam' => 'Seragam',
            'buku' => 'Buku',
            'kegiatan' => 'Kegiatan',
            'lainnya' => 'Lainnya',
        ]);

        return view('bendahara.pembayaran.cetak-kwitansi', [
            'pembayaran' => $pembayaran,
            'relatedPayments' => $relatedPayments,
            'totalBayar' => $totalBayar,
            'schoolInfo' => $schoolInfo,
            'jenisTagihan' => $jenisTagihan,
            'parentName' => $parentName,
        ]);
    }

    /**
     * Pembayaran digital hanya boleh ditutup setelah Paywuz mengonfirmasi
     * pembatalannya. Jika API sedang bermasalah, status lokal tetap pending.
     *
     * @param  iterable<Pembayaran>  $approvedPayments
     */
    private function cancelPendingPaywuzDuplicates(iterable $approvedPayments): void
    {
        $orderIds = collect($approvedPayments)
            ->flatMap(fn (Pembayaran $payment) => Pembayaran::query()
                ->where('tagihan_id', $payment->tagihan_id)
                ->where('siswa_id', $payment->siswa_id)
                ->where('payment_gateway', 'paywuz')
                ->where('status_validasi', 'pending')
                ->when($payment->order_id, fn ($query) => $query->where('order_id', '!=', $payment->order_id))
                ->whereNotNull('order_id')
                ->pluck('order_id'))
            ->filter()
            ->unique();

        $service = app(PaywuzPaymentStatusService::class);
        foreach ($orderIds as $orderId) {
            try {
                $service->cancelIfPending((string) $orderId);
            } catch (\Throwable $exception) {
                Log::critical('Pembayaran Paywuz tetap pending setelah tagihan dilunasi melalui kanal lain.', [
                    'order_id' => $orderId,
                    'message' => $exception->getMessage(),
                ]);
            }
        }
    }
}
