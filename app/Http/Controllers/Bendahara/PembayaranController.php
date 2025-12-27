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
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    /**
     * Menampilkan daftar pembayaran
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        $query = Pembayaran::with(['siswa', 'siswa.kelas', 'tagihan', 'validator']);

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
            $query->whereHas('siswa', function($q) use ($request) {
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
            $query->where(function($q) use ($request) {
                $q->where('kode_pembayaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('siswa', function($q2) use ($request) {
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

        $pembayaranList = Pembayaran::with(['tagihan', 'validator'])
            ->where('siswa_id', $siswaId)
            ->orderBy('tanggal_bayar', 'desc')
            ->paginate(20);

        // Hitung total tagihan dan pembayaran
        $totalTagihan = Tagihan::where('siswa_id', $siswaId)
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->sum('jumlah');

        $totalBayar = Pembayaran::where('siswa_id', $siswaId)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');

        $totalPending = Pembayaran::where('siswa_id', $siswaId)
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
            'totalTerbayar' => $totalBayar,
            'totalPending' => $totalPending,
            'sisaTagihan' => $totalTagihan - $totalBayar,
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
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'status_validasi' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $pembayaran->update([
                'status_validasi' => $request->status_validasi,
                'divalidasi_oleh' => auth()->id(),
                'tanggal_validasi' => now(),
                'catatan' => $request->catatan,
            ]);

            // Jika disetujui, update status tagihan
            if ($request->status_validasi === 'disetujui' && $pembayaran->tagihan) {
                $tagihan = $pembayaran->tagihan;
                
                // Hitung total pembayaran yang disetujui untuk tagihan ini
                $totalBayar = Pembayaran::where('tagihan_id', $tagihan->id)
                    ->where('status_validasi', 'disetujui')
                    ->sum('jumlah_bayar');

                if ($totalBayar >= $tagihan->jumlah) {
                    $tagihan->update(['status' => 'sudah_bayar']);
                }
            }

            DB::commit();
            
            $message = $request->status_validasi === 'disetujui' 
                ? 'Pembayaran berhasil divalidasi.' 
                : 'Pembayaran ditolak.';
            
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

        // Ambil tagihan yang belum lunas
        $tagihanBelumLunas = Tagihan::where('siswa_id', $siswaId)
            ->where('status', '!=', 'sudah_bayar')
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->get();

        // Hitung total tagihan dan sisa tagihan
        $totalTagihan = Tagihan::where('siswa_id', $siswaId)
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->sum('jumlah');

        $totalBayar = Pembayaran::where('siswa_id', $siswaId)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');

        $sisaTagihan = $totalTagihan - $totalBayar;

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
     * Simpan pembayaran manual
     */
    public function store(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:tunai,transfer',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Generate kode pembayaran
            $kodePembayaran = 'PAY-' . strtoupper(Str::random(8)) . '-' . date('Ymd');

            // Upload bukti pembayaran jika ada
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')
                    ->store('bukti_pembayaran/' . date('Y/m'), 'public');
            }

            $pembayaran = Pembayaran::create([
                'tagihan_id' => $request->tagihan_id,
                'siswa_id' => $siswaId,
                'kode_pembayaran' => $kodePembayaran,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_pembayaran' => $buktiPath,
                'status_validasi' => 'pending', // Perlu divalidasi
                'catatan' => $request->catatan,
            ]);

            DB::commit();
            
            return redirect()->route('bendahara.pembayaran.riwayat-siswa', $siswaId)
                ->with('success', 'Pembayaran berhasil dicatat. Kode: ' . $kodePembayaran);
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