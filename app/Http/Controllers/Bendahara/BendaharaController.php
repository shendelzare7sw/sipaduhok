<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class BendaharaController extends Controller
{
    /**
     * Dashboard Bendahara
     * Menampilkan ringkasan data keuangan dan tabel siswa
     */
    public function dashboard()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        // Statistik utama
        $data = [
            // Total semua tagihan
            'totalTagihan' => Tagihan::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })->sum('jumlah'),
            
            // Total pembayaran yang sudah disetujui
            'totalTerbayar' => Pembayaran::where('status_validasi', 'disetujui')
                ->whereNotNull('tanggal_validasi')
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->whereHas('tagihan', function($q2) use ($tahunAjaranAktif) {
                        $q2->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                    });
                })
                ->sum('jumlah_bayar'),
            
            // Tagihan belum lunas (count distinct siswa)
            'tagihanBelumLunas' => Tagihan::where('status', 'belum_bayar')
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })->distinct('siswa_id')->count('siswa_id'),
            
            // Pembayaran bulan ini
            'pembayaranBulanIni' => Pembayaran::where('status_validasi', 'disetujui')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->sum('jumlah_bayar'),
            
            // Pembayaran menunggu validasi
            'pembayaranPending' => Pembayaran::where('status_validasi', 'pending')->count(),
            
            // Tagihan terlambat
            'tagihanTerlambat' => Tagihan::where('status', 'terlambat')
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })->distinct('siswa_id')->count('siswa_id'),
        ];
        
        // ================================================================
        // TAB 1: Pembayaran Menunggu Validasi (Prioritas Tindakan)
        // ================================================================
        $pendingPembayaran = Pembayaran::with(['siswa', 'siswa.kelas', 'tagihan'])
            ->where('status_validasi', 'pending')
            ->orderBy('created_at', 'asc') // Yang paling lama menunggu = prioritas
            ->take(7)
            ->get();
        
        // ================================================================
        // TAB 2: Transaksi Terbaru (Kas Masuk Terakhir)
        // ================================================================
        $transaksiTerbaru = Pembayaran::with(['siswa', 'siswa.kelas', 'tagihan', 'validator'])
            ->where('status_validasi', 'disetujui')
            ->orderBy('tanggal_validasi', 'desc')
            ->take(7)
            ->get();

        // ================================================================
        // TAB 3: Dispensasi Kenaikan Kelas Menunggu
        // ================================================================
        $dispensasiPending = DB::table('izin_naik_kelas_khusus')
            ->join('siswa', 'izin_naik_kelas_khusus.siswa_id', '=', 'siswa.id')
            ->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->where('izin_naik_kelas_khusus.status', 'MENUNGGU')
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('izin_naik_kelas_khusus.tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->select(
                'izin_naik_kelas_khusus.*',
                'siswa.nama_lengkap',
                'siswa.nisn',
                'kelas.nama_kelas'
            )
            ->orderBy('izin_naik_kelas_khusus.created_at', 'asc')
            ->take(5)
            ->get();

        // Kas masuk hari ini
        $kasHariIni = Pembayaran::where('status_validasi', 'disetujui')
            ->whereDate('tanggal_validasi', today())
            ->sum('jumlah_bayar');

        $data['pendingPembayaran'] = $pendingPembayaran;
        $data['transaksiTerbaru'] = $transaksiTerbaru;
        $data['dispensasiPending'] = $dispensasiPending;
        $data['kasHariIni'] = $kasHariIni;
        $data['tahunAjaran'] = $tahunAjaranAktif;
        
        return view('dashboard.bendahara', $data);
    }
}