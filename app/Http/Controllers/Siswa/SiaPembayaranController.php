<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;

class SiaPembayaranController extends Controller
{
    /**
     * Tampilkan halaman pembayaran & tagihan
     */
    public function index()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->with('kelas')->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
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

        return view('siswa.sia.pembayaran.index', compact(
            'siswa',
            'tagihan',
            'tagihanGroup',
            'totalTagihan',
            'totalBayar',
            'sisaTagihan'
        ));
    }

    /**
     * Proses pembayaran - DISABLED
     * Pembayaran hanya bisa dilakukan oleh orang tua untuk mencegah
     * siswa menyembunyikan informasi tagihan
     */
    public function prosesBayar(Request $request)
    {
        return redirect()->route('siswa.sia.pembayaran.index')
            ->with('error', 'Pembayaran hanya dapat dilakukan oleh Orang Tua. Silakan hubungi orang tua Anda untuk melakukan pembayaran.');

        /* DISABLED - Pembayaran hanya oleh orang tua
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:tunai,transfer,midtrans',
            'bukti_pembayaran' => 'required_if:metode_pembayaran,tunai,transfer|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan');
        }

        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        // Validasi jumlah bayar tidak melebihi tagihan
        if ($request->jumlah_bayar > $tagihan->jumlah) {
            return back()->with('error', 'Jumlah pembayaran melebihi tagihan');
        }

        // Upload bukti pembayaran
        $buktiFoto = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiFoto = $request->file('bukti_pembayaran')->store('pembayaran/bukti', 'public');
        }

        // Generate kode pembayaran
        $kodePembayaran = 'PAY-' . strtoupper(uniqid());

        // Simpan pembayaran
        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'siswa_id' => $siswa->id,
            'kode_pembayaran' => $kodePembayaran,
            'jumlah_bayar' => $request->jumlah_bayar,
            'tanggal_bayar' => now()->toDateString(),
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_pembayaran' => $buktiFoto,
            'status_validasi' => $request->metode_pembayaran === 'midtrans' ? 'disetujui' : 'pending',
            'catatan' => $request->catatan,
        ]);

        // Update status tagihan jika lunas
        if ($request->jumlah_bayar >= $tagihan->jumlah) {
            $tagihan->update(['status' => 'sudah_bayar']);
        }

        return redirect()->route('siswa.sia.pembayaran.index')
            ->with('success', 'Pembayaran berhasil diajukan. Menunggu validasi bendahara.');
        */
    }

    /**
     * Riwayat pembayaran
     */
    public function riwayat()
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $riwayatPembayaran = Pembayaran::where('siswa_id', $siswa->id)
            ->with(['tagihan', 'validator'])
            ->orderBy('tanggal_bayar', 'desc')
            ->paginate(10);

        return view('siswa.sia.pembayaran.riwayat', compact('siswa', 'riwayatPembayaran'));
    }

    /**
     * Cetak bukti pembayaran
     */
    public function cetakBukti($id)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.sia.dashboard')
                ->with('error', 'Data siswa tidak ditemukan');
        }

        $pembayaran = Pembayaran::where('id', $id)
            ->where('siswa_id', $siswa->id)
            ->with(['tagihan', 'siswa.kelas'])
            ->firstOrFail();

        $pdf = Pdf::loadView('siswa.sia.pembayaran.cetak', compact('pembayaran'));
        
        return $pdf->download('Bukti_Pembayaran_' . $pembayaran->kode_pembayaran . '.pdf');
    }
}