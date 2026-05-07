<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Siswa;
use App\Models\TenagaPendidik;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\GuruPengajarKelas;
use App\Models\JadwalPelajaran;

class DashboardController extends Controller
{
    /**
     * Fallback dashboard - redirect based on role
     */
    public function index()
    {
        $user = auth()->user();

        // Redirect to appropriate dashboard based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isKetuaPKBM()) {
            return redirect()->route('ketua.dashboard');
        } elseif ($user->isWakilKepalaSekolah()) {
            return redirect()->route('waka.dashboard');
        } elseif ($user->isSekretaris()) {
            return redirect()->route('sekretaris.dashboard');
        } elseif ($user->isBendahara()) {
            return redirect()->route('bendahara.dashboard');
        } elseif ($user->isWaliKelas()) {
            return redirect()->route('wali.dashboard');
        } elseif ($user->isGuruPengajar()) {
            return redirect()->route('guru.dashboard');
        } elseif ($user->isSiswa()) {
            return redirect()->route('siswa.dashboard');
        } elseif ($user->isOrangTua()) {
            return redirect()->route('orang-tua.dashboard');
        }

        // Default fallback
        abort(403, 'Role Anda tidak memiliki dashboard.');
    }

    /**
     * Admin Dashboard
     */
    public function admin(): View
    {
        $data = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::count(),
            'totalUser' => User::where('is_active', true)->count(),
            'siswaBaruBulanIni' => Siswa::whereMonth('tanggal_masuk', now()->month)
                ->whereYear('tanggal_masuk', now()->year)
                ->count(),
        ];

        return view('dashboard.admin', $data);
    }

    /**
     * Ketua PKBM Dashboard
     */
    public function ketua(): View
    {
        $data = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'totalGuru' => TenagaPendidik::count(),
            'totalKelas' => Kelas::count(),
            'pendingDispensasi' => \App\Models\PengajuanRaporKetua::where('status', 'menunggu')->count(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('role', '!=', 'admin') // exclude admin
                ->with('roleRelation')
                ->orderBy('last_login_at', 'desc')
                ->take(5)
                ->get(),
        ];

        return view('dashboard.ketua', $data);
    }

    /**
     * Sekretaris Dashboard
     */
    public function sekretaris(): View
    {
        $data = [
            'totalSiswa' => Siswa::where('status', 'aktif')->count(),
            'siswaBaru' => Siswa::whereMonth('tanggal_masuk', now()->month)
                ->whereYear('tanggal_masuk', now()->year)
                ->get(),
        ];

        return view('dashboard.sekretaris', $data);
    }

    /**
     * Bendahara Dashboard
     */
    public function bendahara(): View
    {
        $data = [
            'totalTagihan' => Tagihan::sum('jumlah'),
            'totalTerbayar' => Pembayaran::where('status_validasi', 'disetujui')
                ->whereNotNull('tanggal_validasi')
                ->sum('jumlah_bayar'),
            'tagihanBelumLunas' => Tagihan::where('status', 'belum_bayar')->count(),
            'pembayaranBulanIni' => Pembayaran::where('status_validasi', 'disetujui')
                ->whereNotNull('tanggal_validasi')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->sum('jumlah_bayar'),
            'pembayaranMenungguValidasi' => Pembayaran::where('status_validasi', 'pending')->count(),
            'tagihanTerlambat' => Tagihan::where('status', 'terlambat')->count(),
        ];

        return view('dashboard.bendahara', $data);
    }


    /**
     * Guru Pengajar Dashboard
     * 
     * FIXED: Changed view path to 'guru.dashboard.dashboard'
     */
    public function guru(): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->first();
        
        $kelasYangDiajar = collect();
        $totalSiswa = 0;
        $jadwalHariIni = collect();
        
        if ($tenagaPendidik) {
            // Filter scope ke TA aktif — supaya pasca aktivasi TA baru,
            // dashboard tidak nampilkan kelas/jadwal/mapel dari TA lama.
            $taAktifId = \App\Models\TahunAjaran::where('is_active', true)->value('id');

            // Get all kelas yang diajar guru ini di TA aktif
            $guruKelasRaw = GuruPengajarKelas::with(['kelas', 'mataPelajaran'])
                ->where('tenaga_pendidik_id', $tenagaPendidik->id)
                ->when($taAktifId, fn($q) => $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $taAktifId)))
                ->get()
                ->groupBy('kelas_id');

            // Transform data untuk view
            $kelasYangDiajar = collect();
            foreach ($guruKelasRaw as $kelasId => $items) {
                $kelas = $items->first()->kelas;
                $kelasYangDiajar->push([
                    'kelas' => $kelas,
                    'jumlah_siswa' => Siswa::where('kelas_id', $kelasId)->where('status', 'aktif')->count(),
                    'jumlah_mapel' => $items->count(),
                ]);
            }

            $kelasIds = $guruKelasRaw->keys();
            $totalSiswa = Siswa::whereIn('kelas_id', $kelasIds)
                ->where('status', 'aktif')
                ->count();

            $hariIni = $this->getHariIndonesia(now()->dayOfWeek);
            $jadwalHariIni = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $tenagaPendidik->id)
                ->when($taAktifId, fn($q) => $q->where('tahun_ajaran_id', $taAktifId))
                ->where('hari', $hariIni)
                ->orderBy('jam_mulai')
                ->get();

            // Mapping mapel yang valid (assigned) untuk lookup cepat — scope TA aktif
            $assignedMapels = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
                ->when($taAktifId, fn($q) => $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $taAktifId)))
                ->get()
                ->map(function($gpk) {
                    return $gpk->kelas_id . '-' . $gpk->mata_pelajaran_id;
                })
                ->flip();

            // Mapping by name untuk handle kasus ID mapel di jadwal beda dengan di assignment (e.g. duplikat mapel)
            $assignedMapelsByName = GuruPengajarKelas::with('mataPelajaran')
                ->where('tenaga_pendidik_id', $tenagaPendidik->id)
                ->when($taAktifId, fn($q) => $q->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $taAktifId)))
                ->get()
                ->mapWithKeys(function($gpk) {
                    if ($gpk->mataPelajaran) {
                        return [$gpk->kelas_id . '-' . $gpk->mataPelajaran->nama_mapel => $gpk->mata_pelajaran_id];
                    }
                    return [];
                });

            $jadwalHariIniProcessed = collect();
            
            foreach ($jadwalHariIni as $jadwal) {
                 // Check if jadwal has classes (multi-class support)
                 if ($jadwal->kelas->count() > 0) {
                     foreach ($jadwal->kelas as $kls) {
                         // We create a "view object" or clone the jadwal for this specific class
                         // to ensure the view can treat it as a single class entry
                         $jadwalItem = clone $jadwal;
                         $jadwalItem->kelas_via_pivot = $kls; // Store specific class
                         
                         $key = $kls->id . '-' . $jadwal->mata_pelajaran_id;
                         
                         if ($assignedMapels->has($key)) {
                             $jadwalItem->link_mapel_id = $jadwal->mata_pelajaran_id;
                         } else {
                             // Coba cari berdasarkan nama mapel jika ID tidak cocok
                             if ($jadwal->mataPelajaran) {
                                 $nameKey = $kls->id . '-' . $jadwal->mataPelajaran->nama_mapel;
                                 if (isset($assignedMapelsByName[$nameKey])) {
                                     $jadwalItem->link_mapel_id = $assignedMapelsByName[$nameKey];
                                 } else {
                                     $jadwalItem->link_mapel_id = $jadwal->mata_pelajaran_id;
                                 }
                             } else {
                                 $jadwalItem->link_mapel_id = $jadwal->mata_pelajaran_id;
                             }
                         }
                         
                         // Fix the relationship for the view to use
                         $jadwalItem->setRelation('kelas', $kls); 
                         $jadwalItem->kel_id = $kls->id; // Helper for view

                         $jadwalHariIniProcessed->push($jadwalItem);
                     }
                 }
            }
            
            // Re-sort if needed (e.g. by time then class name)
            $jadwalHariIni = $jadwalHariIniProcessed->sortBy([
                ['jam_mulai', 'asc'],
                ['kelas.nama_kelas', 'asc'],
            ]);
        }
        
        $data = [
            'guru' => $tenagaPendidik,
            'kelasYangDiajar' => $kelasYangDiajar,
            'totalSiswa' => $totalSiswa,
            'jadwalHariIni' => $jadwalHariIni,
        ];

        return view('dashboard.guru', $data);
    }

    private function getHariIndonesia($dayOfWeek)
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        
        return $hari[$dayOfWeek] ?? 'Senin';
    }

}