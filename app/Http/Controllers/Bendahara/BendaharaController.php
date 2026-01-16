<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\TahunAjaran;

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
            
            // Tagihan belum lunas
            'tagihanBelumLunas' => Tagihan::where('status', 'belum_bayar')
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })->count(),
            
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
                })->count(),
        ];
        
        // Data siswa terbaru dengan tagihan (10 data)
        $siswaRecent = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->take(10)
            ->get()
            ->map(function($siswa) use ($tahunAjaranAktif) {
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

                return $siswa;
            });
        
        $data['siswaRecent'] = $siswaRecent;
        $data['tahunAjaran'] = $tahunAjaranAktif;
        
        return view('dashboard.bendahara', $data);
    }
}