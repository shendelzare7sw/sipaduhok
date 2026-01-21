<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\FinancialAuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
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
                $q->where('kode_pembayaran', 'like', '%' . $request->search . '%')
                    ->orWhereHas('siswa', function ($q2) use ($request) {
                        $q2->where('nama_lengkap', 'like', '%' . $request->search . '%');
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

        // Ambil tagihan untuk tahun ajaran aktif
        $tagihan = Tagihan::where('siswa_id', $siswaId)
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->get();

        $tagihanIds = $tagihan->pluck('id');

        $pembayaranList = Pembayaran::with(['tagihan', 'validator'])
            ->where('siswa_id', $siswaId)
            ->whereIn('tagihan_id', $tagihanIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Hitung total tagihan
        $totalTagihan = $tagihan->sum('jumlah');

        // Sisa tagihan = total tagihan yang belum lunas (berdasarkan status)
        $sisaTagihan = $tagihan->where('status', '!=', 'sudah_bayar')->sum('jumlah');
        $totalTerbayar = $totalTagihan - $sisaTagihan;

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
                if (!$relatedPayments->contains('id', $targetPembayaran->id)) {
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
                            ->update([
                                'status_validasi' => 'ditolak',
                                'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar via transaksi lain (Kode: ' . $pembayaran->kode_pembayaran . ')',
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

            // Notify orang tua about payment validation (only on approval)
            if ($request->status_validasi === 'disetujui') {
                foreach ($relatedPayments as $pembayaran) {
                    $pembayaran->load('siswa.orangTua');
                    app(\App\Services\NotificationService::class)->notifyPembayaranValidasi($pembayaran);
                }
            }

            $message = $request->status_validasi === 'disetujui'
                ? 'Pembayaran berhasil divalidasi'
                : 'Pembayaran ditolak';

            if ($countUpdated > 1) {
                $message .= " ({$countUpdated} item dalam transaksi bulk).";
            } else {
                $message .= ".";
            }

            return redirect()->route('bendahara.pembayaran.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Input pembayaran manual (tunai/transfer manual)
     */
    public function create($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $siswa = Siswa::with(['kelas', 'cabang'])->findOrFail($siswaId);

        // Ambil tagihan yang belum lunas untuk tahun ajaran aktif
        $tagihanBelumLunas = Tagihan::where('siswa_id', $siswaId)
            ->where('status', '!=', 'sudah_bayar')
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->get();

        // Sisa tagihan = total dari tagihan yang belum lunas
        // Ini lebih akurat karena langsung dari status tagihan
        $sisaTagihan = $tagihanBelumLunas->sum('jumlah');

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

        $request->validate([
            'tagihan_ids' => 'required|array|min:1',
            'tagihan_ids.*' => 'exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $validasiLangsung = $request->has('validasi_langsung');

            // Proses setiap tagihan yang dipilih
            foreach ($request->tagihan_ids as $tagihanId) {
                $tagihan = Tagihan::findOrFail($tagihanId);

                // Generate kode pembayaran unik per tagihan
                $kodePembayaran = 'PAY-' . strtoupper(Str::random(8)) . '-' . date('Ymd');

                $pembayaran = Pembayaran::create([
                    'tagihan_id' => $tagihanId,
                    'siswa_id' => $siswaId,
                    'kode_pembayaran' => $kodePembayaran,
                    'jumlah_bayar' => $tagihan->jumlah,
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'metode_pembayaran' => 'tunai',
                    'status_validasi' => $validasiLangsung ? 'disetujui' : 'pending',
                    'divalidasi_oleh' => $validasiLangsung ? auth()->id() : null,
                    'tanggal_validasi' => $validasiLangsung ? now() : null,
                    'catatan' => $request->catatan,
                ]);

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
                            ->update([
                                'status_validasi' => 'ditolak',
                                'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar tunai di loket (Kode: ' . $kodePembayaran . ')',
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

            $message = $validasiLangsung
                ? 'Pembayaran tunai berhasil dicatat dan divalidasi.'
                : 'Pembayaran berhasil dicatat. Menunggu validasi.';

            return redirect()->route('bendahara.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validasi langsung (approve langsung untuk pembayaran tunai)
     */
    public function validasiLangsung(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $kodePembayaran = 'PAY-' . strtoupper(Str::random(8)) . '-' . date('Ymd');

            $pembayaran = Pembayaran::create([
                'tagihan_id' => $request->tagihan_id,
                'siswa_id' => $siswaId,
                'kode_pembayaran' => $kodePembayaran,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
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
                    ->update([
                        'status_validasi' => 'ditolak',
                        'catatan' => 'Otomatis dibatalkan karena tagihan sudah dibayar tunai di loket (Kode: ' . $kodePembayaran . ')',
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

            return redirect()->route('bendahara.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', 'Pembayaran tunai berhasil dicatat dan divalidasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}