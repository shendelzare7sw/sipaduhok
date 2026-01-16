<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Kelas;
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
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        // Default bulan dan tahun ini
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $kelasId = $request->get('kelas_id');
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
        }

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $pembayaran = $query->orderBy('tanggal_bayar', 'desc')->paginate(20)->appends($request->query());

        // Statistik bulanan
        $totalPembayaran = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->when($metode, function($q) use ($metode) {
                return $q->where('metode_pembayaran', $metode);
            })
            ->sum('jumlah_bayar');
        
        $jumlahTransaksi = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->when($metode, function($q) use ($metode) {
                return $q->where('metode_pembayaran', $metode);
            })
            ->count();
        
        // Per metode pembayaran
        $totalTunai = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('metode_pembayaran', 'tunai')
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->sum('jumlah_bayar');
        
        $jumlahTunai = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('metode_pembayaran', 'tunai')
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->count();
        
        $totalTransfer = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('metode_pembayaran', 'transfer')
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->sum('jumlah_bayar');
        
        $jumlahTransfer = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('metode_pembayaran', 'transfer')
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->count();
        
        $totalMidtrans = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('metode_pembayaran', 'midtrans')
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->sum('jumlah_bayar');
        
        $jumlahMidtrans = Pembayaran::where('status_validasi', 'disetujui')
            ->whereMonth('tanggal_bayar', $bulan)
            ->whereYear('tanggal_bayar', $tahun)
            ->where('metode_pembayaran', 'midtrans')
            ->when($kelasId, function($q) use ($kelasId) {
                return $q->whereHas('siswa', function($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->count();

        $totalNonTunai = $totalTransfer + $totalMidtrans;
        $jumlahNonTunai = $jumlahTransfer + $jumlahMidtrans;

        // Pembayaran per hari (untuk grafik)
        $pembayaranPerHari = collect();
        $daysInMonth = Carbon::create($tahun, $bulan, 1)->daysInMonth;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $total = Pembayaran::where('status_validasi', 'disetujui')
                ->whereDate('tanggal_bayar', Carbon::create($tahun, $bulan, $day))
                ->when($kelasId, function($q) use ($kelasId) {
                    return $q->whereHas('siswa', function($q2) use ($kelasId) {
                        $q2->where('kelas_id', $kelasId);
                    });
                })
                ->when($metode, function($q) use ($metode) {
                    return $q->where('metode_pembayaran', $metode);
                })
                ->sum('jumlah_bayar');
            
            $pembayaranPerHari->put($day, $total);
        }

        // Generate daftar bulan untuk dropdown
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('bendahara.laporan.index', [
            'pembayaran' => $pembayaran,
            'kelasList' => $kelasList,
            'bulanList' => $bulanList,
            'bulan' => (int)$bulan, // Pastikan integer
            'tahun' => (int)$tahun, // Pastikan integer
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

            $tagihan = Tagihan::whereIn('siswa_id', $siswaIds)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->get();

            $totalTagihan = $tagihan->sum('jumlah');
            // Sisa tagihan berdasarkan status tagihan (lebih robust)
            $sisaTagihan = $tagihan->where('status', '!=', 'sudah_bayar')->sum('jumlah');
            $totalBayar = $totalTagihan - $sisaTagihan;

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

            $tagihan = Tagihan::whereIn('siswa_id', $siswaIds)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->get();

            $totalTagihan = $tagihan->sum('jumlah');
            // Sisa tagihan berdasarkan status tagihan (lebih robust)
            $sisaTagihan = $tagihan->where('status', '!=', 'sudah_bayar')->sum('jumlah');
            $totalBayar = $totalTagihan - $sisaTagihan;

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
            $tagihan = Tagihan::where('siswa_id', $siswa->id)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->get();

            $totalTagihan = $tagihan->sum('jumlah');
            // Sisa tagihan berdasarkan status tagihan (lebih robust)
            $sisaTagihan = $tagihan->where('status', '!=', 'sudah_bayar')->sum('jumlah');
            $totalBayar = $totalTagihan - $sisaTagihan;

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
            $tagihan = Tagihan::where('siswa_id', $siswa->id)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->get();

            $totalTagihan = $tagihan->sum('jumlah');
            // Sisa tagihan berdasarkan status tagihan (lebih robust)
            $sisaTagihan = $tagihan->where('status', '!=', 'sudah_bayar')->sum('jumlah');
            $totalBayar = $totalTagihan - $sisaTagihan;

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