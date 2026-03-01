<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\Cabang;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class LaporanPembayaranController extends Controller
{
    /**
     * Jenis-jenis tagihan
     */
    protected $jenisTagihan = [
        'uang_pendaftaran' => 'Uang Pendaftaran',
        'uang_pangkal' => 'Uang Pangkal',
        'seragam' => 'Seragam',
        'buku' => 'Buku',
        'ujian' => 'Ujian',
        'akm' => 'AKM',
        'kegiatan' => 'Kegiatan',
        'spp' => 'SPP',
    ];

    /**
     * Halaman utama laporan pembayaran
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $cabangList = Cabang::where('is_active', true)->orderBy('nama_cabang')->get();
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        // Default bulan dan tahun ini
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $kelasId = $request->get('kelas_id');
        $cabangId = $request->get('cabang_id');
        $jenjang = $request->get('jenjang');
        $metode = $request->get('metode');

        // Query pembayaran
        $query = Pembayaran::with(['siswa.kelas', 'tagihan', 'validator'])
            ->where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun);

        if ($kelasId) {
            $query->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        } elseif ($jenjang) {
            $query->whereHas('siswa.kelas', function($q) use ($jenjang, $cabangId) {
                $q->where('jenjang', $jenjang);
                if ($cabangId) {
                    $q->where('cabang_id', $cabangId);
                }
            });
        } elseif ($cabangId) {
            $query->whereHas('siswa.kelas', function($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });
        }

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')->paginate(20)->appends($request->query());

        // Reusable closure for location-based filtering (cabang → jenjang → kelas)
        $applyLocationFilter = function($q) use ($kelasId, $jenjang, $cabangId) {
            if ($kelasId) {
                $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            } elseif ($jenjang) {
                $q->whereHas('siswa.kelas', function($q2) use ($jenjang, $cabangId) {
                    $q2->where('jenjang', $jenjang);
                    if ($cabangId) $q2->where('cabang_id', $cabangId);
                });
            } elseif ($cabangId) {
                $q->whereHas('siswa.kelas', function($q2) use ($cabangId) {
                    $q2->where('cabang_id', $cabangId);
                });
            }
            return $q;
        };

        // Base query builder for approved payments in selected period
        $baseQuery = function() use ($bulan, $tahun, $applyLocationFilter) {
            $q = Pembayaran::where('status_validasi', 'disetujui')
                ->whereMonth('tanggal_bayar', $bulan)
                ->whereYear('tanggal_bayar', $tahun);
            return $applyLocationFilter($q);
        };

        // Statistik bulanan
        $totalPembayaran = (clone $baseQuery())
            ->when($metode, fn($q) => $q->where('metode_pembayaran', $metode))
            ->sum('jumlah_bayar');
        
        $jumlahTransaksi = (clone $baseQuery())
            ->when($metode, fn($q) => $q->where('metode_pembayaran', $metode))
            ->count();
        
        // Per metode pembayaran
        $totalTunai = (clone $baseQuery())->where('metode_pembayaran', 'tunai')->sum('jumlah_bayar');
        $jumlahTunai = (clone $baseQuery())->where('metode_pembayaran', 'tunai')->count();
        $totalTransfer = (clone $baseQuery())->where('metode_pembayaran', 'transfer')->sum('jumlah_bayar');
        $jumlahTransfer = (clone $baseQuery())->where('metode_pembayaran', 'transfer')->count();
        $totalMidtrans = (clone $baseQuery())->where('metode_pembayaran', 'midtrans')->sum('jumlah_bayar');
        $jumlahMidtrans = (clone $baseQuery())->where('metode_pembayaran', 'midtrans')->count();

        $totalNonTunai = $totalTransfer + $totalMidtrans;
        $jumlahNonTunai = $jumlahTransfer + $jumlahMidtrans;

        // Pembayaran per hari (untuk grafik) - Faster Grouped Query
        $pembayaranPerHari = collect();
        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        
        // Inisialisasi semua hari dengan 0
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $pembayaranPerHari->put($day, 0);
        }

        // Ambil data dalam satu query
        $dailyData = (clone $baseQuery())
            ->when($metode, fn($q) => $q->where('metode_pembayaran', $metode))
            ->selectRaw('DAY(tanggal_bayar) as day, SUM(jumlah_bayar) as total')
            ->groupBy('day')
            ->get();

        foreach ($dailyData as $data) {
            $pembayaranPerHari->put((int) $data->day, (float) $data->total);
        }

        // Generate daftar bulan untuk dropdown
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Daftar jenjang unik untuk filter
        $jenjangList = $kelasList->pluck('jenjang')->unique()->sort()->values();

        return view('bendahara.laporan.index', [
            'pembayaran' => $pembayaran,
            'kelasList' => $kelasList,
            'cabangList' => $cabangList,
            'jenjangList' => $jenjangList,
            'bulanList' => $bulanList,
            'bulan' => (int)$bulan,
            'tahun' => (int)$tahun,
            'totalPembayaran' => $totalPembayaran,
            'jumlahTransaksi' => $jumlahTransaksi,
            'totalTunai' => $totalTunai,
            'jumlahTunai' => $jumlahTunai,
            'totalTransfer' => $totalTransfer,
            'jumlahTransfer' => $jumlahTransfer,
            'totalMidtrans' => $totalMidtrans,
            'jumlahMidtrans' => $jumlahMidtrans,
            'totalNonTunai' => $totalNonTunai,
            'jumlahNonTunai' => $jumlahNonTunai,
            'pembayaranPerHari' => $pembayaranPerHari,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $this->jenisTagihan,
        ]);
    }

    /**
     * Cetak laporan pembayaran bulanan
     */
    public function cetak(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $kelasId = $request->get('kelas_id');

        $query = Pembayaran::with(['siswa.kelas', 'siswa.cabang', 'tagihan', 'validator'])
            ->where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun);

        if ($kelasId) {
            $query->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
            $kelas = Kelas::find($kelasId);
        } else {
            $kelas = null;
        }

        $pembayaranList = $query->orderBy('tanggal_bayar', 'desc')->get();
        $totalBulanIni = $pembayaranList->sum('jumlah_bayar');

        $namaBulan = Carbon::create(null, $bulan, 1)->translatedFormat('F');

        return view('bendahara.laporan.cetak', [
            'pembayaranList' => $pembayaranList,
            'totalBulanIni' => $totalBulanIni,
            'namaBulan' => $namaBulan,
            'tahun' => $tahun,
            'kelas' => $kelas,
        ]);
    }

    /**
     * Laporan rekap tagihan per kelas
     */
    public function rekapTagihan(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })
        ->withCount(['siswa as total_siswa' => function($q) {
            $q->where('status', 'aktif');
        }])
        ->orderBy('jenjang')
        ->orderBy('nama_kelas')
        ->get();

        // Hitung total tagihan dan pembayaran per kelas
        $kelasList->transform(function($kelas) use ($tahunAjaranAktif) {
            $siswaIds = Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->pluck('id');

            $totalTagihan = Tagihan::whereIn('siswa_id', $siswaIds)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->sum('jumlah');

            // Hitung total pembayaran yang sudah disetujui (actual payments)
            $tagihanIds = Tagihan::whereIn('siswa_id', $siswaIds)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->pluck('id');

            $totalBayar = Pembayaran::whereIn('tagihan_id', $tagihanIds)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            $sisaTagihan = max(0, $totalTagihan - $totalBayar);

            $kelas->total_tagihan = $totalTagihan;
            $kelas->total_bayar = $totalBayar;
            $kelas->sisa_tagihan = $sisaTagihan;
            $kelas->persentase = $totalTagihan > 0
                ? round(($totalBayar / $totalTagihan) * 100, 1)
                : 0;

            return $kelas;
        });

        // Grand total
        $grandTotal = [
            'tagihan' => $kelasList->sum('total_tagihan'),
            'bayar' => $kelasList->sum('total_bayar'),
            'sisa' => $kelasList->sum('sisa_tagihan'),
        ];

        return view('bendahara.laporan.rekap-tagihan', [
            'kelasList' => $kelasList,
            'grandTotal' => $grandTotal,
            'tahunAjaran' => $tahunAjaranAktif,
        ]);
    }

    /**
     * Cetak rekap tagihan
     */
    public function cetakRekapTagihan(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })
        ->withCount(['siswa as total_siswa' => function($q) {
            $q->where('status', 'aktif');
        }])
        ->orderBy('jenjang')
        ->orderBy('nama_kelas')
        ->get();

        $kelasList->transform(function($kelas) use ($tahunAjaranAktif) {
            $siswaIds = Siswa::where('kelas_id', $kelas->id)
                ->where('status', 'aktif')
                ->pluck('id');

            $totalTagihan = Tagihan::whereIn('siswa_id', $siswaIds)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->sum('jumlah');

            // Hitung total pembayaran yang sudah disetujui (actual payments)
            $tagihanIds = Tagihan::whereIn('siswa_id', $siswaIds)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->pluck('id');

            $totalBayar = Pembayaran::whereIn('tagihan_id', $tagihanIds)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            $sisaTagihan = max(0, $totalTagihan - $totalBayar);

            $kelas->total_tagihan = $totalTagihan;
            $kelas->total_bayar = $totalBayar;
            $kelas->sisa_tagihan = $sisaTagihan;
            $kelas->persentase = $totalTagihan > 0
                ? round(($totalBayar / $totalTagihan) * 100, 1)
                : 0;

            return $kelas;
        });

        $grandTotal = [
            'tagihan' => $kelasList->sum('total_tagihan'),
            'bayar' => $kelasList->sum('total_bayar'),
            'sisa' => $kelasList->sum('sisa_tagihan'),
        ];

        return view('bendahara.laporan.cetak-rekap-tagihan', [
            'kelasList' => $kelasList,
            'grandTotal' => $grandTotal,
            'tahunAjaran' => $tahunAjaranAktif,
        ]);
    }

    /**
     * Laporan siswa yang belum lunas
     */
    public function belumLunas(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        $query = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswaList = $query->orderBy(
            Kelas::select('jenjang')->whereColumn('kelas.id', 'siswa.kelas_id')
        )->orderBy('nama_lengkap')->get();

        // Filter hanya siswa yang belum lunas
        $siswaList = $siswaList->filter(function($siswa) use ($tahunAjaranAktif) {
            $tagihanQuery = Tagihan::where('siswa_id', $siswa->id)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                });

            $totalTagihan = $tagihanQuery->sum('jumlah');

            // Hitung total pembayaran yang sudah disetujui (actual payments)
            $tagihanIds = $tagihanQuery->pluck('id');
            $totalBayar = Pembayaran::whereIn('tagihan_id', $tagihanIds)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            $sisaTagihan = max(0, $totalTagihan - $totalBayar);

            $siswa->total_tagihan = $totalTagihan;
            $siswa->total_bayar = $totalBayar;
            $siswa->sisa_tagihan = $sisaTagihan;

            return $sisaTagihan > 0;
        })->values();

        return view('bendahara.laporan.belum-lunas', [
            'siswaList' => $siswaList,
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaranAktif,
            'filters' => $request->only(['kelas_id']),
        ]);
    }

    /**
     * Cetak laporan siswa belum lunas
     */
    public function cetakBelumLunas(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        $query = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif');

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
            $kelas = Kelas::find($request->kelas_id);
        } else {
            $kelas = null;
        }

        $siswaList = $query->orderBy(
            Kelas::select('jenjang')->whereColumn('kelas.id', 'siswa.kelas_id')
        )->orderBy('nama_lengkap')->get();

        $siswaList = $siswaList->filter(function($siswa) use ($tahunAjaranAktif) {
            $tagihanQuery = Tagihan::where('siswa_id', $siswa->id)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                });

            $totalTagihan = $tagihanQuery->sum('jumlah');

            // Hitung total pembayaran yang sudah disetujui (actual payments)
            $tagihanIds = $tagihanQuery->pluck('id');
            $totalBayar = Pembayaran::whereIn('tagihan_id', $tagihanIds)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            $sisaTagihan = max(0, $totalTagihan - $totalBayar);

            $siswa->total_tagihan = $totalTagihan;
            $siswa->total_bayar = $totalBayar;
            $siswa->sisa_tagihan = $sisaTagihan;

            return $sisaTagihan > 0;
        })->values();

        return view('bendahara.laporan.cetak-belum-lunas', [
            'siswaList' => $siswaList,
            'kelas' => $kelas,
            'tahunAjaran' => $tahunAjaranAktif,
        ]);
    }
}