<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Jenis-jenis tagihan yang tersedia
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
     * Menampilkan daftar tagihan semua siswa
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kelasList = Kelas::when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
            return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        })->orderBy('jenjang')->orderBy('nama_kelas')->get();

        // Query siswa dengan filter
        $query = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif');

        // Filter berdasarkan kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan pencarian nama
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Urutkan berdasarkan kelas (jenjang) kemudian abjad nama
        $siswaList = $query->orderBy(
            Kelas::select('jenjang')->whereColumn('kelas.id', 'siswa.kelas_id')
        )->orderBy('nama_lengkap', 'asc')
        ->paginate(15)
        ->appends($request->query());

        // Hitung total tagihan per siswa
        $siswaList->getCollection()->transform(function($siswa) use ($tahunAjaranAktif) {
            $tagihan = Tagihan::where('siswa_id', $siswa->id)
                ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                    return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                })
                ->get();

            $totalTagihan = $tagihan->sum('jumlah');
            $tagihanLunas = $tagihan->where('status', 'sudah_bayar')->sum('jumlah');
            
            $siswa->total_tagihan = $totalTagihan;
            $siswa->tagihan_lunas = $tagihanLunas;
            $siswa->sisa_tagihan = $totalTagihan - $tagihanLunas;
            $siswa->tagihan_detail = $tagihan;
            
            return $siswa;
        });

        return view('bendahara.tagihan.index', [
            'siswaList' => $siswaList,
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $this->jenisTagihan,
            'filters' => $request->only(['kelas_id', 'search']),
        ]);
    }

    /**
     * Menampilkan detail tagihan per siswa
     */
    public function show($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        $siswa = Siswa::with(['kelas', 'cabang'])->findOrFail($siswaId);
        
        $tagihan = Tagihan::where('siswa_id', $siswaId)
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->orderBy('jenis_tagihan')
            ->get();

        $totalTagihan = $tagihan->sum('jumlah');
        $tagihanLunas = $tagihan->where('status', 'sudah_bayar')->sum('jumlah');

        return view('bendahara.tagihan.show', [
            'siswa' => $siswa,
            'tagihan' => $tagihan,
            'totalTagihan' => $totalTagihan,
            'tagihanLunas' => $tagihanLunas,
            'sisaTagihan' => $totalTagihan - $tagihanLunas,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $this->jenisTagihan,
        ]);
    }

    /**
     * Form input/edit tagihan siswa
     */
    public function edit($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif. Silakan hubungi Admin.');
        }

        $siswa = Siswa::with(['kelas', 'cabang'])->findOrFail($siswaId);
        
        // Ambil tagihan yang sudah ada
        $tagihanExist = Tagihan::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->pluck('jumlah', 'jenis_tagihan')
            ->toArray();

        return view('bendahara.tagihan.edit', [
            'siswa' => $siswa,
            'tagihanExist' => $tagihanExist,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $this->jenisTagihan,
        ]);
    }

    /**
     * Simpan/update tagihan siswa
     */
    public function update(Request $request, $siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $siswa = Siswa::findOrFail($siswaId);

        $request->validate([
            'tagihan' => 'required|array',
            'tagihan.*' => 'nullable|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|array',
            'tanggal_jatuh_tempo.*' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            foreach ($this->jenisTagihan as $key => $label) {
                $jumlah = $request->input("tagihan.{$key}", 0);
                $jatuhTempo = $request->input("tanggal_jatuh_tempo.{$key}");

                // Cek apakah tagihan sudah ada
                $tagihan = Tagihan::where('siswa_id', $siswaId)
                    ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                    ->where('jenis_tagihan', $key)
                    ->first();

                if ($tagihan) {
                    // Update jika ada
                    $tagihan->update([
                        'jumlah' => $jumlah ?? 0,
                        'tanggal_jatuh_tempo' => $jatuhTempo ?? now()->addMonth(),
                    ]);
                } else {
                    // Buat baru jika belum ada
                    Tagihan::create([
                        'siswa_id' => $siswaId,
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                        'jenis_tagihan' => $key,
                        'jumlah' => $jumlah ?? 0,
                        'tanggal_jatuh_tempo' => $jatuhTempo ?? now()->addMonth(),
                        'status' => 'belum_bayar',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('bendahara.tagihan.show', $siswaId)
                ->with('success', 'Tagihan siswa berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak tagihan siswa
     */
    public function cetak($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        $siswa = Siswa::with(['kelas', 'cabang'])->findOrFail($siswaId);
        
        $tagihan = Tagihan::where('siswa_id', $siswaId)
            ->when($tahunAjaranAktif, function($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->orderBy('jenis_tagihan')
            ->get();

        $totalTagihan = $tagihan->sum('jumlah');
        $tagihanLunas = $tagihan->where('status', 'sudah_bayar')->sum('jumlah');

        return view('bendahara.tagihan.cetak', [
            'siswa' => $siswa,
            'tagihan' => $tagihan,
            'totalTagihan' => $totalTagihan,
            'tagihanLunas' => $tagihanLunas,
            'sisaTagihan' => $totalTagihan - $tagihanLunas,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $this->jenisTagihan,
        ]);
    }

    /**
     * Bulk create tagihan untuk kelas tertentu
     */
    public function bulkCreate(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $kelasList = Kelas::where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'tagihan' => 'required|array',
                'tagihan.*' => 'nullable|numeric|min:0',
                'tanggal_jatuh_tempo' => 'required|date',
            ]);

            $siswaList = Siswa::where('kelas_id', $request->kelas_id)
                ->where('status', 'aktif')
                ->get();

            if ($siswaList->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada siswa aktif di kelas ini.');
            }

            DB::beginTransaction();
            try {
                foreach ($siswaList as $siswa) {
                    foreach ($this->jenisTagihan as $key => $label) {
                        $jumlah = $request->input("tagihan.{$key}", 0);
                        
                        if ($jumlah > 0) {
                            Tagihan::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                                    'jenis_tagihan' => $key,
                                ],
                                [
                                    'jumlah' => $jumlah,
                                    'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                                    'status' => 'belum_bayar',
                                ]
                            );
                        }
                    }
                }

                DB::commit();
                return redirect()->route('bendahara.tagihan.index')
                    ->with('success', "Tagihan berhasil dibuat untuk {$siswaList->count()} siswa.");
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return view('bendahara.tagihan.bulk-create', [
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $this->jenisTagihan,
        ]);
    }
}