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

class ValidasiAksesController extends Controller
{
    /**
     * Menampilkan halaman validasi akses ujian dan rapor
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        $query = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif');

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter status validasi ujian
        if ($request->filled('status_ujian')) {
            if ($request->status_ujian === 'valid') {
                $query->where('validasi_ujian_bendahara', true);
            } elseif ($request->status_ujian === 'belum') {
                $query->where('validasi_ujian_bendahara', false);
            }
        }

        // Filter status validasi rapor
        if ($request->filled('status_rapor')) {
            if ($request->status_rapor === 'valid') {
                $query->where('validasi_rapor_bendahara', true);
            } elseif ($request->status_rapor === 'belum') {
                $query->where('validasi_rapor_bendahara', false);
            }
        }

        // Pencarian
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        $siswaList = $query->orderBy(
            Kelas::select('jenjang')->whereColumn('kelas.id', 'siswa.kelas_id')
        )->orderBy('nama_lengkap', 'asc')
        ->paginate(15)
        ->appends($request->query());

        // Hitung status keuangan per siswa
        $siswaList->getCollection()->transform(function($siswa) use ($tahunAjaranAktif) {
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
            $siswa->is_lunas = $sisaTagihan <= 0;

            return $siswa;
        });

        // Statistik
        $stats = [
            'total_siswa' => Siswa::where('status', 'aktif')->count(),
            'ujian_valid' => Siswa::where('status', 'aktif')->where('validasi_ujian_bendahara', true)->count(),
            'rapor_valid' => Siswa::where('status', 'aktif')->where('validasi_rapor_bendahara', true)->count(),
        ];

        return view('bendahara.validasi-akses.index', [
            'siswa' => $siswaList,           // ← ubah ke 'siswa' sesuai view
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaranAktif,
            'totalSiswa' => $stats['total_siswa'],     // ← tambahkan
            'validasiUjian' => $stats['ujian_valid'],  // ← tambahkan
            'validasiRapor' => $stats['rapor_valid'],  // ← tambahkan
            'belumValidasi' => $stats['total_siswa'] - $stats['ujian_valid'] - $stats['rapor_valid'], // ← hitung
            'filters' => $request->only(['kelas_id', 'status_ujian', 'status_rapor', 'search']),
        ]);
    }

    /**
     * Validasi akses ujian untuk siswa tertentu
     */
    public function validasiUjian(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        DB::beginTransaction();
        try {
            $siswa->update([
                'validasi_ujian_bendahara' => true,
                'tanggal_validasi_ujian_bendahara' => now(),
                'validasi_ujian_oleh' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Akses ujian untuk {$siswa->nama_lengkap} berhasil divalidasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan validasi akses ujian
     */
    public function batalkanUjian(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        DB::beginTransaction();
        try {
            $siswa->update([
                'validasi_ujian_bendahara' => false,
                'tanggal_validasi_ujian_bendahara' => null,
                'validasi_ujian_oleh' => null,
                // Reset juga validasi wali kelas
                'validasi_ujian_wali' => false,
                'tanggal_validasi_ujian_wali' => null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Validasi akses ujian untuk {$siswa->nama_lengkap} dibatalkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validasi akses rapor untuk siswa tertentu
     */
    public function validasiRapor(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        DB::beginTransaction();
        try {
            $siswa->update([
                'validasi_rapor_bendahara' => true,
                'tanggal_validasi_rapor_bendahara' => now(),
                'validasi_rapor_oleh' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Akses rapor untuk {$siswa->nama_lengkap} berhasil divalidasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batalkan validasi akses rapor
     */
    public function batalkanRapor(Request $request, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        DB::beginTransaction();
        try {
            $siswa->update([
                'validasi_rapor_bendahara' => false,
                'tanggal_validasi_rapor_bendahara' => null,
                'validasi_rapor_oleh' => null,
                // Reset juga validasi wali kelas
                'validasi_rapor_wali' => false,
                'tanggal_validasi_rapor_wali' => null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', "Validasi akses rapor untuk {$siswa->nama_lengkap} dibatalkan.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk validasi akses ujian (per kelas)
     */
    public function bulkValidasiUjian(Request $request, Kelas $kelas)
    {
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->where('validasi_ujian_bendahara', false)
            ->get();

        if ($siswaList->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada siswa yang perlu divalidasi di kelas ini.');
        }

        DB::beginTransaction();
        try {
            foreach ($siswaList as $siswa) {
                $siswa->update([
                    'validasi_ujian_bendahara' => true,
                    'tanggal_validasi_ujian_bendahara' => now(),
                    'validasi_ujian_oleh' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', "Akses ujian berhasil divalidasi untuk {$siswaList->count()} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Bulk validasi akses rapor (per kelas)
     */
    public function bulkValidasiRapor(Request $request, Kelas $kelas)
    {
        $siswaList = Siswa::where('kelas_id', $kelas->id)
            ->where('status', 'aktif')
            ->where('validasi_rapor_bendahara', false)
            ->get();

        if ($siswaList->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada siswa yang perlu divalidasi di kelas ini.');
        }

        DB::beginTransaction();
        try {
            foreach ($siswaList as $siswa) {
                $siswa->update([
                    'validasi_rapor_bendahara' => true,
                    'tanggal_validasi_rapor_bendahara' => now(),
                    'validasi_rapor_oleh' => auth()->id(),
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', "Akses rapor berhasil divalidasi untuk {$siswaList->count()} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validasi multi siswa yang dipilih
     */
    public function bulkValidasiSelected(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:siswa,id',
            'tipe' => 'required|in:ujian,rapor',
        ]);

        DB::beginTransaction();
        try {
            $field = $request->tipe === 'ujian' 
                ? ['validasi_ujian_bendahara' => true, 'tanggal_validasi_ujian_bendahara' => now(), 'validasi_ujian_oleh' => auth()->id()]
                : ['validasi_rapor_bendahara' => true, 'tanggal_validasi_rapor_bendahara' => now(), 'validasi_rapor_oleh' => auth()->id()];

            Siswa::whereIn('id', $request->siswa_ids)->update($field);

            DB::commit();
            
            $label = $request->tipe === 'ujian' ? 'ujian' : 'rapor';
            return redirect()->back()->with('success', "Akses {$label} berhasil divalidasi untuk " . count($request->siswa_ids) . " siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset semua validasi (untuk tahun ajaran baru)
     */
    public function resetValidasi(Request $request)
    {
        $request->validate([
            'kelas_id' => 'nullable|exists:kelas,id',
            'tipe' => 'required|in:ujian,rapor,semua',
        ]);

        DB::beginTransaction();
        try {
            $query = Siswa::where('status', 'aktif');
            
            if ($request->filled('kelas_id')) {
                $query->where('kelas_id', $request->kelas_id);
            }

            $updateFields = [];
            
            if ($request->tipe === 'ujian' || $request->tipe === 'semua') {
                $updateFields = array_merge($updateFields, [
                    'validasi_ujian_bendahara' => false,
                    'validasi_ujian_wali' => false,
                    'tanggal_validasi_ujian_bendahara' => null,
                    'tanggal_validasi_ujian_wali' => null,
                    'validasi_ujian_oleh' => null,
                ]);
            }
            
            if ($request->tipe === 'rapor' || $request->tipe === 'semua') {
                $updateFields = array_merge($updateFields, [
                    'validasi_rapor_bendahara' => false,
                    'validasi_rapor_wali' => false,
                    'tanggal_validasi_rapor_bendahara' => null,
                    'tanggal_validasi_rapor_wali' => null,
                    'validasi_rapor_oleh' => null,
                ]);
            }

            $count = $query->count();
            $query->update($updateFields);

            DB::commit();
            return redirect()->back()->with('success', "Validasi berhasil direset untuk {$count} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}