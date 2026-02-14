<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    /**
     * Get route prefix for redirects
     */
    protected function getRoutePrefix()
    {
        return 'bendahara.tagihan';
    }

    /**
     * Jenis-jenis tagihan yang tersedia
     * Note: SPP dihapus karena sudah ada fitur "Generate SPP Bulanan" yang lebih akurat
     */
    protected $jenisTagihan = [
        'uang_pendaftaran' => 'Formulir Pendaftaran/ Daftar Ulang',
        'uang_pangkal' => 'Uang Pangkal',
        'kegiatan' => 'Uang Kegiatan',
        'buku' => 'Buku Paket',
        'seragam' => 'Seragam',
        'rapor_foto' => 'Rapor Foto',
        'ujian' => 'Ujian & Wisuda',
        'akm' => 'AKM',
    ];

    /**
     * Menampilkan daftar tagihan semua siswa
     */
    public function index(Request $request)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $allTahunAjaran = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        // Allow year selection via dropdown (default = active year)
        $selectedYearId = $request->get('tahun_ajaran_id', $tahunAjaranAktif->id ?? null);
        $selectedYear = TahunAjaran::find($selectedYearId) ?? $tahunAjaranAktif;

        $kelasList = Kelas::with('cabang')
            ->when($selectedYear, function ($q) use ($selectedYear) {
                return $q->where('tahun_ajaran_id', $selectedYear->id);
            })
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        // Query siswa dengan filter
        // IMPORTANT: Include alumni (status='lulus') so their outstanding bills remain accessible
        $query = Siswa::with(['kelas', 'cabang'])
            ->whereIn('status', ['aktif', 'lulus']);

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

        // Hitung total tagihan per siswa berdasarkan tahun yang dipilih
        $siswaList->getCollection()->transform(function ($siswa) use ($selectedYear) {
            $tagihan = Tagihan::where('siswa_id', $siswa->id)
                ->when($selectedYear, function ($q) use ($selectedYear) {
                    return $q->where('tahun_ajaran_id', $selectedYear->id);
                })
                ->get();

            $totalTagihan = $tagihan->sum('jumlah');

            // Calculate Total Paid
            $tagihanIds = $tagihan->pluck('id');
            $totalTerbayar = Pembayaran::where('siswa_id', $siswa->id)
                ->whereIn('tagihan_id', $tagihanIds)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');

            // Sisa tagihan = Total - Terbayar
            $sisaTagihan = $totalTagihan - $totalTerbayar;

            $siswa->total_tagihan = $totalTagihan;
            $siswa->tagihan_lunas = $totalTerbayar;
            $siswa->sisa_tagihan = $sisaTagihan;
            $siswa->tagihan_detail = $tagihan;

            return $siswa;
        });

        // Hitung ringkasan tunggakan tahun sebelumnya (hanya tampil saat melihat tahun aktif)
        $tunggakanSummary = null;
        if ($tahunAjaranAktif && $selectedYear && $selectedYear->id === $tahunAjaranAktif->id) {
            $tunggakanData = Tagihan::where('tahun_ajaran_id', '!=', $tahunAjaranAktif->id)
                ->whereIn('status', ['belum_bayar', 'cicilan', 'terlambat'])
                ->select('tahun_ajaran_id', DB::raw('COUNT(DISTINCT siswa_id) as jumlah_siswa'), DB::raw('SUM(jumlah) as total_tunggakan'))
                ->groupBy('tahun_ajaran_id')
                ->get();

            if ($tunggakanData->isNotEmpty()) {
                $tunggakanSummary = [
                    'jumlah_siswa' => $tunggakanData->sum('jumlah_siswa'),
                    'total_tunggakan' => $tunggakanData->sum('total_tunggakan'),
                    'per_tahun' => $tunggakanData->map(function ($item) {
                        $ta = TahunAjaran::find($item->tahun_ajaran_id);
                        return [
                            'tahun_ajaran_id' => $item->tahun_ajaran_id,
                            'nama_tahun' => $ta->nama_tahun_ajaran ?? '-',
                            'jumlah_siswa' => $item->jumlah_siswa,
                            'total' => $item->total_tunggakan,
                        ];
                    }),
                ];
            }
        }

        return view('bendahara.tagihan.index', [
            'siswaList' => $siswaList,
            'kelasList' => $kelasList,
            'tahunAjaran' => $tahunAjaranAktif,
            'selectedYear' => $selectedYear,
            'allTahunAjaran' => $allTahunAjaran,
            'tunggakanSummary' => $tunggakanSummary,
            'jenisTagihan' => $this->jenisTagihan,
            'filters' => $request->only(['kelas_id', 'search', 'tahun_ajaran_id']),
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
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                return $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->get();

        // Custom Sort Order
        $order = array_keys($this->jenisTagihan);

        $tagihan = $tagihan->sortBy(function ($item) use ($order) {
            $key = $item->jenis_tagihan;
            $index = array_search($key, $order);

            if ($index !== false) {
                return $index;
            }

            if (str_starts_with($key, 'spp_')) {
                return 99; // After defined types
            }

            return 999; // Others at end
        });

        $totalTagihan = $tagihan->sum('jumlah');

        // Calculate Real Sisa Tagihan
        $tagihanIds = $tagihan->pluck('id');
        $tagihanLunas = Pembayaran::where('siswa_id', $siswaId)
            ->whereIn('tagihan_id', $tagihanIds)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');
        
        $sisaTagihan = $totalTagihan - $tagihanLunas;

        // Inject sisa_tagihan to each item for view display if needed
        foreach ($tagihan as $item) {
            $paid = Pembayaran::where('tagihan_id', $item->id)
                ->where('status_validasi', 'disetujui')
                ->sum('jumlah_bayar');
            $item->sisa_tagihan = $item->jumlah - $paid;
        }

        return view('bendahara.tagihan.show', [
            'siswa' => $siswa,
            'tagihan' => $tagihan,
            'totalTagihan' => $totalTagihan,
            'tagihanLunas' => $tagihanLunas, // Representing Total Paid Amount
            'sisaTagihan' => $sisaTagihan,
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

        // Ambil SEMUA tagihan yang sudah ada (termasuk SPP & Custom)
        $allTagihan = Tagihan::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->get();

        // Map untuk existing amounts
        $tagihanExist = $allTagihan->pluck('jumlah', 'jenis_tagihan')->toArray();

        // Gabungkan jenis tagihan default dengan yang sudah ada di database
        $jenisTagihanWithExisting = $this->jenisTagihan;

        foreach ($allTagihan as $tagihan) {
            // Jika jenis tagihan tidak ada di default list, tambahkan
            if (!isset($jenisTagihanWithExisting[$tagihan->jenis_tagihan])) {
                // Gunakan keterangan jika ada, atau format jenis_tagihan
                $label = $tagihan->keterangan ?: ucwords(str_replace(['_', '-'], ' ', $tagihan->jenis_tagihan));
                $jenisTagihanWithExisting[$tagihan->jenis_tagihan] = $label;
            }
        }
        
        $allYears = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('bendahara.tagihan.edit', [
            'siswa' => $siswa,
            'tagihanExist' => $tagihanExist,
            'allTagihan' => $allTagihan,
            'tahunAjaran' => $tahunAjaranAktif,
            'jenisTagihan' => $jenisTagihanWithExisting,
            'allYears' => $allYears,
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

        // Sanitize currency inputs BEFORE validation
        // This handles formatted inputs like "200.000" or "1.500.000" 
        // and converts them to pure numbers (200000, 1500000)
        $tagihanInput = $request->input('tagihan', []);
        $sanitizedTagihan = [];
        foreach ($tagihanInput as $key => $value) {
            // Remove all non-digit characters (dots, commas, spaces, Rp, etc.)
            $sanitizedTagihan[$key] = preg_replace('/\D/', '', $value) ?: '0';
        }
        $request->merge(['tagihan' => $sanitizedTagihan]);

        $request->validate([
            'tagihan' => 'required|array',
            'tagihan.*' => 'nullable|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|array',
            'tanggal_jatuh_tempo.*' => 'nullable|date',
            'tahun_ajaran_id' => 'nullable|array', // Optional validation
        ]);

        DB::beginTransaction();
        try {
            // Get all submitted tagihan keys (including custom ones from form)
            $submittedTagihan = $request->input('tagihan', []);

            foreach ($submittedTagihan as $key => $jumlah) {
                $jatuhTempo = $request->input("tanggal_jatuh_tempo.{$key}");
                $targetYearId = $request->input("tahun_ajaran_id.{$key}") ?? $tahunAjaranAktif->id;

                // Cek apakah tagihan sudah ada di TAHUN AKTIF (karena form edit load data tahun aktif)
                // Jika user mengubah tahun, kita update record yang ada di tahun aktif ini ke tahun baru.
                // Jika user membuat baru dengan tahun berbeda, create new.
                
                $tagihan = Tagihan::where('siswa_id', $siswaId)
                    ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                    ->where('jenis_tagihan', $key)
                    ->first();

                if ($tagihan) {
                    // Update jika ada (bisa pindah tahun)
                    $tagihan->update([
                        'jumlah' => $jumlah ?? 0,
                        'tanggal_jatuh_tempo' => $jatuhTempo ?? now()->addMonth(),
                        'tahun_ajaran_id' => $targetYearId,
                    ]);
                } else {
                    // Buat baru jika belum ada
                    Tagihan::create([
                        'siswa_id' => $siswaId,
                        'tahun_ajaran_id' => $targetYearId,
                        'jenis_tagihan' => $key,
                        'jumlah' => $jumlah ?? 0,
                        'tanggal_jatuh_tempo' => $jatuhTempo ?? now()->addMonth(),
                        'status' => 'belum_bayar',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route($this->getRoutePrefix() . '.show', $siswaId)
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
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
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

        $kelasList = Kelas::with('cabang')
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        if ($request->isMethod('post')) {
            $request->validate([
                'kelas_ids' => 'required|array|min:1',
                'kelas_ids.*' => 'exists:kelas,id',
                'tagihan' => 'required|array',
                'tagihan.*' => 'nullable|numeric|min:0',
                'tanggal_jatuh_tempo' => 'required|array',
                'tanggal_jatuh_tempo.*' => 'required|date',
                'custom_tanggal_jatuh_tempo' => 'nullable|array',
                'custom_tanggal_jatuh_tempo.*' => 'nullable|date',
            ]);

            $siswaList = Siswa::whereIn('kelas_id', $request->kelas_ids)
                ->where('status', 'aktif')
                ->get();

            if ($siswaList->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada siswa aktif di kelas yang dipilih.');
            }

            $kelasCount = count($request->kelas_ids);

            DB::beginTransaction();
            try {
                foreach ($siswaList as $siswa) {
                    // Process default tagihan
                    foreach ($this->jenisTagihan as $key => $label) {
                        $jumlah = $request->input("tagihan.{$key}", 0);
                        $jatuhTempo = $request->input("tanggal_jatuh_tempo.{$key}");

                        if ($jumlah > 0 && $jatuhTempo) {
                            Tagihan::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                                    'jenis_tagihan' => $key,
                                ],
                                [
                                    'jumlah' => $jumlah,
                                    'tanggal_jatuh_tempo' => $jatuhTempo,
                                    'status' => 'belum_bayar',
                                ]
                            );
                        }
                    }

                    // Process custom tagihan fields
                    $customJenis = $request->input('custom_jenis_tagihan', []);
                    $customTagihan = $request->input('custom_tagihan', []);
                    $customJatuhTempo = $request->input('custom_tanggal_jatuh_tempo', []);

                    foreach ($customJenis as $index => $jenisNama) {
                        $jumlahCustom = $customTagihan[$index] ?? 0;
                        $jatuhTempoCustom = $customJatuhTempo[$index] ?? now()->addMonth()->format('Y-m-d');

                        if (!empty($jenisNama) && $jumlahCustom > 0) {
                            // Create slug from jenis nama
                            $jenisSlug = str()->slug($jenisNama);

                            Tagihan::updateOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                                    'jenis_tagihan' => $jenisSlug,
                                ],
                                [
                                    'jumlah' => $jumlahCustom,
                                    'tanggal_jatuh_tempo' => $jatuhTempoCustom,
                                    'status' => 'belum_bayar',
                                    'keterangan' => $jenisNama, // Store original name in keterangan
                                ]
                            );
                        }
                    }
                }

                DB::commit();
                return redirect()->route($this->getRoutePrefix() . '.index')
                    ->with('success', "Tagihan berhasil dibuat untuk {$siswaList->count()} siswa dari {$kelasCount} kelas.");
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

    /**
     * Form untuk menambah tagihan custom ke siswa tertentu
     */
    public function createCustom()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $siswaList = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $kelasList = Kelas::with('cabang')
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $cabangList = \App\Models\Cabang::orderBy('nama_cabang')->get();

        return view('bendahara.tagihan.create-custom', [
            'siswaList' => $siswaList,
            'kelasList' => $kelasList,
            'cabangList' => $cabangList,
            'tahunAjaran' => $tahunAjaranAktif,
        ]);
    }

    /**
     * Simpan tagihan custom untuk multiple siswa
     */
    public function storeCustom(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
            'jenis_tagihan' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_jatuh_tempo' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Buat slug dari jenis tagihan (tanpa prefix "custom_")
        $jenisTagihanSlug = str()->slug($request->jenis_tagihan);

        try {
            DB::beginTransaction();

            $count = 0;
            $createdTagihan = [];
            foreach ($request->siswa_ids as $siswaId) {
                $tagihan = Tagihan::create([
                    'siswa_id' => $siswaId,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'jenis_tagihan' => $jenisTagihanSlug,
                    'jumlah' => $request->jumlah,
                    'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                    'status' => 'belum_bayar',
                    'keterangan' => $request->keterangan ?: $request->jenis_tagihan,
                ]);
                $createdTagihan[] = $tagihan;
                $count++;
            }

            DB::commit();

            // Notify orang tua about new tagihan
            foreach ($createdTagihan as $tagihan) {
                $tagihan->load('siswa.orangTua');
                app(\App\Services\NotificationService::class)->notifyTagihanBaru($tagihan);
            }

            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', "Tagihan custom berhasil ditambahkan untuk {$count} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan tagihan: ' . $e->getMessage());
        }
    }


    /**
     * Hapus item tagihan tertentu
     */
    public function destroyItem($tagihanId)
    {
        try {
            $tagihan = Tagihan::findOrFail($tagihanId);

            // Cek apakah sudah ada pembayaran
            if ($tagihan->status === 'sudah_bayar' || $tagihan->status === 'cicilan') {
                return redirect()->back()->with('error', 'Tagihan yang sudah memiliki pembayaran tidak dapat dihapus.');
            }

            $siswaId = $tagihan->siswa_id;
            $tagihan->delete();

            return redirect()->route($this->getRoutePrefix() . '.show', $siswaId)
                ->with('success', 'Tagihan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Form untuk generate SPP bulanan (12 bulan)
     */
    public function generateSppForm()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $kelasList = Kelas::with('cabang')
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $siswaList = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        return view('bendahara.tagihan.generate-spp', [
            'kelasList' => $kelasList,
            'siswaList' => $siswaList,
            'tahunAjaran' => $tahunAjaranAktif,
        ]);
    }

    /**
     * Generate SPP bulanan untuk siswa/kelas
     */
    public function generateSpp(Request $request)
    {
        // Validasi berbeda berdasarkan target_type
        if ($request->target_type === 'siswa') {
            $request->validate([
                'target_type' => 'required|in:kelas,siswa',
                'tipe_spp' => 'required|in:setahun,sebagian',
                'siswa_ids' => 'required|array|min:1',
                'siswa_ids.*' => 'exists:siswa,id',
                'jumlah_spp' => 'required|numeric|min:1',
                'tanggal_jatuh_tempo' => 'required|integer|min:1|max:31',
                'bulan_mulai' => 'required|integer|min:1|max:12',
                'jumlah_bulan' => 'required_if:tipe_spp,sebagian|nullable|integer|min:1|max:12',
            ]);
        } else {
            $request->validate([
                'target_type' => 'required|in:kelas,siswa',
                'tipe_spp' => 'required|in:setahun,sebagian',
                'target_id' => 'required|exists:kelas,id',
                'jumlah_spp' => 'required|numeric|min:1',
                'tanggal_jatuh_tempo' => 'required|integer|min:1|max:31',
                'bulan_mulai' => 'required|integer|min:1|max:12',
                'jumlah_bulan' => 'required_if:tipe_spp,sebagian|nullable|integer|min:1|max:12',
            ]);
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Tentukan daftar siswa berdasarkan target
        if ($request->target_type === 'kelas') {
            $siswaList = Siswa::where('kelas_id', $request->target_id)
                ->where('status', 'aktif')
                ->get();
        } else {
            // Multiple siswa from checkbox
            $siswaList = Siswa::whereIn('id', $request->siswa_ids)
                ->where('status', 'aktif')
                ->get();
        }

        if ($siswaList->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada siswa yang ditemukan.');
        }

        // Nama bulan
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        DB::beginTransaction();
        try {
            $totalCreated = 0;
            $bulanMulai = $request->bulan_mulai;

            // Tentukan jumlah bulan yang akan digenerate
            $jumlahBulanGenerate = $request->tipe_spp === 'setahun' ? 12 : $request->jumlah_bulan;

            foreach ($siswaList as $siswa) {
                // Generate SPP sesuai jumlah bulan
                for ($i = 0; $i < $jumlahBulanGenerate; $i++) {
                    $bulanIndex = (($bulanMulai + $i - 1) % 12) + 1;
                    $tahunSPP = date('Y') + floor(($bulanMulai + $i - 1) / 12);

                    // Hitung tanggal jatuh tempo
                    $tanggalJatuhTempo = date('Y-m-d', strtotime("$tahunSPP-$bulanIndex-{$request->tanggal_jatuh_tempo}"));

                    // Buat atau update tagihan SPP
                    $tagihan = Tagihan::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'tahun_ajaran_id' => $tahunAjaranAktif->id,
                            'jenis_tagihan' => 'spp_' . strtolower($namaBulan[$bulanIndex]),
                        ],
                        [
                            'jumlah' => $request->jumlah_spp,
                            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                            'status' => 'belum_bayar',
                            'keterangan' => 'SPP ' . $namaBulan[$bulanIndex] . ' ' . $tahunSPP,
                        ]
                    );

                    $totalCreated++;
                }
            }

            DB::commit();
            $tipeSppText = $request->tipe_spp === 'setahun' ? '(SPP Setahun)' : "(SPP {$jumlahBulanGenerate} Bulan)";
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', "Berhasil generate $totalCreated tagihan SPP {$tipeSppText} untuk {$siswaList->count()} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal generate SPP: ' . $e->getMessage());
        }
    }


    /**
     * Form untuk duplikasi tagihan dari siswa ke siswa lain
     */
    public function duplicateForm()
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $kelasList = Kelas::with('cabang')
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->orderBy('jenjang')
            ->orderBy('nama_kelas')
            ->get();

        $siswaList = Siswa::with(['kelas', 'cabang'])
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        return view('bendahara.tagihan.duplicate', [
            'kelasList' => $kelasList,
            'siswaList' => $siswaList,
            'tahunAjaran' => $tahunAjaranAktif,
        ]);
    }

    /**
     * Proses duplikasi tagihan
     */
    public function duplicate(Request $request)
    {
        $request->validate([
            'source_siswa_id' => 'required|exists:siswa,id',
            'target_siswa_ids' => 'required|array|min:1',
            'target_siswa_ids.*' => 'exists:siswa,id',
            'replace_existing' => 'required|boolean',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        // Ambil semua tagihan siswa sumber
        $sourceTagihan = Tagihan::where('siswa_id', $request->source_siswa_id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->get();

        if ($sourceTagihan->isEmpty()) {
            return redirect()->back()->with('error', 'Siswa sumber tidak memiliki tagihan.');
        }

        DB::beginTransaction();
        try {
            $totalDuplicated = 0;

            foreach ($request->target_siswa_ids as $targetSiswaId) {
                // Skip jika target sama dengan source
                if ($targetSiswaId == $request->source_siswa_id) {
                    continue;
                }

                foreach ($sourceTagihan as $tagihan) {
                    if ($request->replace_existing) {
                        // Replace existing - update or create
                        Tagihan::updateOrCreate(
                            [
                                'siswa_id' => $targetSiswaId,
                                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                                'jenis_tagihan' => $tagihan->jenis_tagihan,
                            ],
                            [
                                'jumlah' => $tagihan->jumlah,
                                'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
                                'status' => 'belum_bayar',
                                'keterangan' => $tagihan->keterangan,
                            ]
                        );
                        $totalDuplicated++;
                    } else {
                        // Skip if exists - hanya create yang belum ada
                        $exists = Tagihan::where('siswa_id', $targetSiswaId)
                            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                            ->where('jenis_tagihan', $tagihan->jenis_tagihan)
                            ->exists();

                        if (!$exists) {
                            Tagihan::create([
                                'siswa_id' => $targetSiswaId,
                                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                                'jenis_tagihan' => $tagihan->jenis_tagihan,
                                'jumlah' => $tagihan->jumlah,
                                'tanggal_jatuh_tempo' => $tagihan->tanggal_jatuh_tempo,
                                'status' => 'belum_bayar',
                                'keterangan' => $tagihan->keterangan,
                            ]);
                            $totalDuplicated++;
                        }
                    }
                }
            }

            DB::commit();
            $targetCount = count($request->target_siswa_ids);
            return redirect()->route($this->getRoutePrefix() . '.index')
                ->with('success', "Berhasil menduplikasi $totalDuplicated tagihan ke $targetCount siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal duplikasi tagihan: ' . $e->getMessage());
        }
    }

    /**
     * API: Get siswa by kelas (untuk AJAX)
     */
    public function getSiswaByKelas($kelasId)
    {
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'nisn']);

        return response()->json($siswaList);
    }

    /**
     * API: Get preview tagihan siswa (untuk AJAX)
     */
    public function getTagihanPreview($siswaId)
    {
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $tagihan = Tagihan::where('siswa_id', $siswaId)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->get();

        return response()->json($tagihan);
    }
}