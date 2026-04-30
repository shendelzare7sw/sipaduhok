<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\TenagaPendidik;
use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Ujian;
use App\Models\UjianSiswa;
use App\Models\SoalUjian;
use App\Models\Siswa;
use App\Services\NotificationService;

class GuruUjianController extends Controller
{
    /**
     * Tampilkan daftar ujian
     */
    public function index($kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        // Determine if this is latihan or ujian based on route
        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $tipeUjian = $isLatihan ? 'latihan' : 'ujian';

        $query = Ujian::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id);

        // Filter by type
        if ($isLatihan) {
            $query->where('tipe_ujian', 'latihan');
        } else {
            // Ujian includes: ulangan_harian, pts_ganjil, pas_ganjil, pts_genap, pas_genap, to_1, to_2, to_3, upk, ujian_praktek
            $query->where('tipe_ujian', '!=', 'latihan');
        }

        $ujianList = $query->orderBy('tanggal_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('guru.lms.ujian.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujianList' => $ujianList,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
        ]);
    }

    /**
     * Form tambah ujian
     */
    /**
     * Form tambah ujian
     */
    public function create(Request $request, $kelasId, $mapelId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $tipeUjian = $isLatihan ? 'latihan' : 'ujian';
        $isTingkatAkhir = Ujian::isTingkatAkhir($kelas);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.ujian.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
            'isTingkatAkhir' => $isTingkatAkhir,
            'kelasLain' => $kelasLain,
        ]);
    }

    /**
     * Simpan ujian baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);
        
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        $isLatihan = request()->routeIs('guru.lms.latihan.*');

        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:ulangan_harian,latihan,uts,uas,pts_ganjil,pas_ganjil,pts_genap,pas_genap,to_1,to_2,to_3,upk,ujian_praktek',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'nullable|integer|min:0',
            'tampilkan_nilai' => 'nullable|boolean',
            'tampilkan_riwayat' => 'nullable|boolean',
            'bisa_diulang' => 'nullable|boolean',
            'batas_pengulangan' => 'nullable|integer|min:0',
        ]);

        // Jika dari latihan route, pastikan tipe_ujian adalah latihan
        if ($isLatihan) {
            $validated['tipe_ujian'] = 'latihan';
        }

        $ujianData = [
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_ujian' => $validated['judul_ujian'],
            'deskripsi' => $validated['deskripsi'],
            'tipe_ujian' => $validated['tipe_ujian'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'durasi_menit' => $validated['durasi_menit'],
            'is_active' => false,
            'tampilkan_nilai' => $request->has('tampilkan_nilai'),
            'tampilkan_riwayat' => $request->has('tampilkan_riwayat'),
            'bisa_diulang' => $request->has('bisa_diulang'),
            'batas_pengulangan' => $request->has('bisa_diulang') ? $validated['batas_pengulangan'] : null,
        ];

        // Buat untuk kelas utama
        $ujian = Ujian::create(array_merge($ujianData, ['kelas_id' => $kelasId]));
        $this->createUjianSiswaForKelas($ujian, $kelasId, $mataPelajaran);

        // Notify siswa in main class
        $notificationService = app(NotificationService::class);
        $notificationService->notifyUjianNew($ujian);

        // Duplikasi ke kelas tambahan
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        foreach ($kelasTambahan as $kelasLainId) {
            if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                $ujianDuplikat = Ujian::create(array_merge($ujianData, ['kelas_id' => $kelasLainId]));
                $this->createUjianSiswaForKelas($ujianDuplikat, $kelasLainId, $mataPelajaran);
                $notificationService->notifyUjianNew($ujianDuplikat);
                $jumlahDuplikasi++;
            }
        }

        $label = $isLatihan ? 'Latihan' : 'Ujian';
        $msg = "{$label} berhasil ditambahkan";
        if ($jumlahDuplikasi > 0) {
            $msg .= " dan diduplikasi ke {$jumlahDuplikasi} kelas lain";
        }

        $routeName = $isLatihan ? 'guru.lms.latihan.index' : 'guru.lms.ujian.index';
        return redirect()
            ->route($routeName, [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Form edit ujian
     */
    public function edit($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $tipeUjian = $isLatihan ? 'latihan' : 'ujian';

        // Verify tipe_ujian matches route
        if ($isLatihan && $ujian->tipe_ujian !== 'latihan') {
            abort(404, 'Latihan tidak ditemukan');
        }
        if (!$isLatihan && $ujian->tipe_ujian === 'latihan') {
            abort(404, 'Ujian tidak ditemukan');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $isTingkatAkhir = Ujian::isTingkatAkhir($kelas);

        $kelasLain = GuruPengajarKelas::where('tenaga_pendidik_id', $tenagaPendidik->id)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('kelas_id', '!=', $kelasId)
            ->with('kelas')
            ->get();

        return view('guru.lms.ujian.edit', [
            'ujian' => $ujian,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
            'isTingkatAkhir' => $isTingkatAkhir,
            'kelasLain' => $kelasLain,
        ]);
    }

    /**
     * Update ujian
     */
    public function update(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $isLatihan = request()->routeIs('guru.lms.latihan.*');

        // Verify tipe_ujian matches route
        if ($isLatihan && $ujian->tipe_ujian !== 'latihan') {
            abort(404, 'Latihan tidak ditemukan');
        }
        if (!$isLatihan && $ujian->tipe_ujian === 'latihan') {
            abort(404, 'Ujian tidak ditemukan');
        }

        // Capture original state for matching in other classes
        $originalTitle = $ujian->judul_ujian;

        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:ulangan_harian,latihan,uts,uas,pts_ganjil,pas_ganjil,pts_genap,pas_genap,to_1,to_2,to_3,upk,ujian_praktek',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'nullable|integer|min:0',
            'tampilkan_nilai' => 'nullable|boolean',
            'tampilkan_riwayat' => 'nullable|boolean',
            'bisa_diulang' => 'nullable|boolean',
            'batas_pengulangan' => 'nullable|integer|min:0',
        ]);

        // Prevent changing tipe_ujian when updating from latihan route
        if ($isLatihan) {
            $validated['tipe_ujian'] = 'latihan';
        }

        $validated['tampilkan_nilai'] = $request->has('tampilkan_nilai');
        $validated['tampilkan_riwayat'] = $request->has('tampilkan_riwayat');
        $validated['bisa_diulang'] = $request->has('bisa_diulang');
        $validated['batas_pengulangan'] = $request->has('bisa_diulang') ? $validated['batas_pengulangan'] : null;

        $ujian->update($validated);

        // SYNC LOGIC (Update or Create to linked classes)
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        $jumlahUpdate = 0;

        if (!empty($kelasTambahan)) {
            $mataPelajaran = MataPelajaran::findOrFail($mapelId);

            $syncData = [
                'mata_pelajaran_id' => $mapelId,
                'guru_id' => $tenagaPendidik->id,
                'judul_ujian' => $ujian->judul_ujian,
                'deskripsi' => $ujian->deskripsi,
                'tipe_ujian' => $ujian->tipe_ujian,
                'tanggal_mulai' => $ujian->tanggal_mulai,
                'tanggal_selesai' => $ujian->tanggal_selesai,
                'durasi_menit' => $ujian->durasi_menit,
                'is_active' => $ujian->is_active,
                'tampilkan_nilai' => $ujian->tampilkan_nilai,
                'tampilkan_riwayat' => $ujian->tampilkan_riwayat,
                'bisa_diulang' => $ujian->bisa_diulang,
                'batas_pengulangan' => $ujian->batas_pengulangan,
            ];

            foreach ($kelasTambahan as $kelasLainId) {
                if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                    $existing = Ujian::where('kelas_id', $kelasLainId)
                        ->where('guru_id', $tenagaPendidik->id)
                        ->where('mata_pelajaran_id', $mapelId)
                        ->where('judul_ujian', $originalTitle)
                        ->first();

                    if ($existing) {
                        $existing->update($syncData);
                        $jumlahUpdate++;
                    } else {
                        $ujianBaru = Ujian::create(array_merge($syncData, ['kelas_id' => $kelasLainId]));
                        $this->createUjianSiswaForKelas($ujianBaru, $kelasLainId, $mataPelajaran);
                        $jumlahDuplikasi++;
                    }
                }
            }
        }

        $label = $isLatihan ? 'Latihan' : 'Ujian';
        $msg = "{$label} berhasil diperbarui";
        if ($jumlahDuplikasi > 0 || $jumlahUpdate > 0) {
            $msg .= " (Disinkronisasi ke " . ($jumlahDuplikasi + $jumlahUpdate) . " kelas lain)";
        }

        $routeName = $isLatihan ? 'guru.lms.latihan.index' : 'guru.lms.ujian.index';
        return redirect()
            ->route($routeName, [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Hapus ujian
     */
    public function destroy(Request $request, $kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $idsToDelete = [$ujian->id];

        // BULK DELETE LOGIC
        if ($request->has('hapus_terkait')) {
            $relatedUjian = Ujian::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_ujian', $ujian->judul_ujian)
                ->where('tipe_ujian', $ujian->tipe_ujian)
                ->where('id', '!=', $ujian->id)
                ->get();

            foreach ($relatedUjian as $rel) {
                $idsToDelete[] = $rel->id;
            }
        }

        Ujian::whereIn('id', $idsToDelete)->delete();

        $label = $isLatihan ? 'Latihan' : 'Ujian';
        $msg = "{$label} berhasil dihapus";
        if (count($idsToDelete) > 1) {
            $countLain = count($idsToDelete) - 1;
            $msg .= " (termasuk {$countLain} {$label} terkait di kelas lain)";
        }

        $routeName = $isLatihan ? 'guru.lms.latihan.index' : 'guru.lms.ujian.index';
        return redirect()
            ->route($routeName, [$kelasId, $mapelId])
            ->with('success', $msg);
    }

    /**
     * Lihat hasil ujian
     */
    public function hasil($kelasId, $mapelId, $id): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::findOrFail($id);

        // Ambil semua siswa aktif di kelas ini
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get();

        $results = $siswaList->map(function($siswa) use ($id, $mataPelajaran) {
            // Filter berdasarkan akses mata pelajaran (agama/mapel khusus)
            if (!$siswa->canAccessMapel($mataPelajaran)) return null;

            // Cari pengerjaan ujian untuk siswa ini
            $ujianSiswa = UjianSiswa::with('siswa')
                ->where('ujian_id', $id)
                ->where('siswa_id', $siswa->id)
                ->first();

            if (!$ujianSiswa) {
                // Jika belum ada record pengerjaan, buat objek sementara agar muncul di tabel sebagai 'Belum Mulai'
                $dummy = new UjianSiswa();
                $dummy->ujian_id = $id;
                $dummy->siswa_id = $siswa->id;
                $dummy->status = 'belum_mulai';
                $dummy->setRelation('siswa', $siswa); // Set relasi secara manual
                return $dummy;
            }

            return $ujianSiswa;
        })->filter()->values();

        // Statistik
        $stats = [
            'total' => $results->count(),
            'selesai' => $results->where('status', 'selesai')->count(),
            'sedang_mengerjakan' => $results->where('status', 'sedang_mengerjakan')->count(),
            'belum_mulai' => $results->where('status', 'belum_mulai')->count(),
            'rata_rata' => $results->where('status', 'selesai')->avg('nilai') ?? 0,
            'nilai_tertinggi' => $results->where('status', 'selesai')->max('nilai') ?? 0,
            'nilai_terendah' => $results->where('status', 'selesai')->min('nilai') ?? 0,
        ];

        return view('guru.lms.ujian.hasil', [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'results' => $results,
            'hasilUjian' => $results,
            'stats' => $stats,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Daftar soal ujian
     */
    public function soal($kelasId, $mapelId, $ujianId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::findOrFail($ujianId);

        $soalList = SoalUjian::where('ujian_id', $ujianId)
            ->orderBy('urutan', 'asc')
            ->get();

        return view('guru.lms.ujian.soal', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'soalList' => $soalList,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Form tambah soal
     */
    public function createSoal($kelasId, $mapelId, $ujianId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::findOrFail($ujianId);

        return view('guru.lms.ujian.soal-form', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'guru' => $tenagaPendidik,
            'soal' => null // null for create mode
        ]);
    }

    /**
     * Simpan soal baru
     */
    public function storeSoal(Request $request, $kelasId, $mapelId, $ujianId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'tipe_soal' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,uraian',
            'pertanyaan' => 'required|string',
            'pilihan_jawaban' => 'nullable', // JSON handled in controller logic if needed, or passed as array
            'kunci_jawaban' => 'nullable',
            'bobot' => 'required|integer|min:1',
            'urutan' => 'required|integer',
        ]);

        // Mapping input fields based on tipe_soal
        $pilihanJawaban = null;
        $kunciJawaban = null;

        switch ($request->tipe_soal) {
            case 'pilihan_ganda':
                $pilihanJawaban = json_encode($request->pilihan_jawaban_pilgan);
                $kunciJawaban = $request->kunci_jawaban_pilgan;
                break;

            case 'pilihan_ganda_kompleks':
                $pilihanJawaban = json_encode($request->pilihan_jawaban_kompleks);
                $kunciJawaban = json_encode($request->kunci_jawaban_kompleks ?? []);
                break;

            case 'benar_salah':
                $pilihanJawaban = json_encode(array_values($request->pilihan_jawaban_bs ?? []));
                break;

            case 'isian_singkat':
                $kunciJawaban = $request->kunci_jawaban_isian;
                break;

            case 'uraian':
                break;
        }

        SoalUjian::create([
            'ujian_id' => $ujianId,
            'tipe_soal' => $validated['tipe_soal'],
            'pertanyaan' => $validated['pertanyaan'],
            'pilihan_jawaban' => $pilihanJawaban,
            'kunci_jawaban' => $kunciJawaban,
            'bobot_nilai' => $validated['bobot'],
            'urutan' => $validated['urutan'],
        ]);

        return redirect()
            ->route('guru.lms.ujian.soal.index', [$kelasId, $mapelId, $ujianId])
            ->with('success', 'Soal berhasil ditambahkan');
    }

    /**
     * Form edit soal
     */
    public function editSoal($kelasId, $mapelId, $ujianId, $soalId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::findOrFail($ujianId);
        $soal = SoalUjian::findOrFail($soalId);

        return view('guru.lms.ujian.soal-form', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'guru' => $tenagaPendidik,
            'soal' => $soal
        ]);
    }

    /**
     * Update soal
     */
    public function updateSoal(Request $request, $kelasId, $mapelId, $ujianId, $soalId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $soal = SoalUjian::findOrFail($soalId);

        $validated = $request->validate([
            'tipe_soal' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,isian_singkat,uraian',
            'pertanyaan' => 'required|string',
            'pilihan_jawaban' => 'nullable',
            'kunci_jawaban' => 'nullable',
            'bobot' => 'required|integer|min:1',
            'urutan' => 'required|integer',
        ]);

        // Mapping input fields based on tipe_soal
        $pilihanJawaban = null;
        $kunciJawaban = null;

        switch ($request->tipe_soal) {
            case 'pilihan_ganda':
                $pilihanJawaban = json_encode($request->pilihan_jawaban_pilgan);
                $kunciJawaban = $request->kunci_jawaban_pilgan;
                break;

            case 'pilihan_ganda_kompleks':
                $pilihanJawaban = json_encode($request->pilihan_jawaban_kompleks);
                $kunciJawaban = json_encode($request->kunci_jawaban_kompleks ?? []);
                break;

            case 'benar_salah':
                $pilihanJawaban = json_encode(array_values($request->pilihan_jawaban_bs ?? []));
                break;

            case 'isian_singkat':
                $kunciJawaban = $request->kunci_jawaban_isian;
                break;

            case 'uraian':
                break;
        }

        $validated['pilihan_jawaban'] = $pilihanJawaban;
        $validated['kunci_jawaban'] = $kunciJawaban;

        // Rename bobot to bobot_nilai for database
        if (isset($validated['bobot'])) {
            $validated['bobot_nilai'] = $validated['bobot'];
            unset($validated['bobot']);
        }

        $soal->update($validated);

        return redirect()
            ->route('guru.lms.ujian.soal.index', [$kelasId, $mapelId, $ujianId])
            ->with('success', 'Soal berhasil diperbarui');
    }

    /**
     * Hapus soal
     */
    public function destroySoal($kelasId, $mapelId, $ujianId, $soalId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $soal = SoalUjian::findOrFail($soalId);
        $soal->delete();

        return redirect()
            ->route('guru.lms.ujian.soal.index', [$kelasId, $mapelId, $ujianId])
            ->with('success', 'Soal berhasil dihapus');
    }

    /**
     * Halaman Manage Soal (Multi-Soal)
     */
    public function manageSoal($kelasId, $mapelId, $ujianId): View
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::where('id', $ujianId)->firstOrFail();

        $soalList = SoalUjian::where('ujian_id', $ujianId)->orderBy('urutan', 'asc')->get();

        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $tipeUjian = $isLatihan ? 'latihan' : 'ujian';

        $aiSetting = \App\Models\AppSetting::where('key', 'ai_question_generator_enabled')->first();
        $aiQuestionGeneratorEnabled = $aiSetting ? filter_var($aiSetting->value, FILTER_VALIDATE_BOOLEAN) : true;

        return view('guru.lms.ujian.manage_soal', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'soalList' => $soalList,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
            'aiQuestionGeneratorEnabled' => $aiQuestionGeneratorEnabled,
            'relatedUjianCount' => Ujian::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mataPelajaran->id)
                ->where('judul_ujian', $ujian->judul_ujian)
                ->where('tipe_ujian', $ujian->tipe_ujian)
                ->where('id', '!=', $ujian->id)
                ->count(),
        ]);
    }

    /**
     * Simpan Semua Soal (Bulk Update)
     */
    public function storeAllSoal(Request $request, $kelasId, $mapelId, $ujianId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::findOrFail($ujianId);

        // Validasi dasar
        $request->validate([
            'soal' => 'nullable|array',
            'soal.*.tipe_soal' => 'required|string',
            'soal.*.pertanyaan' => 'nullable|string',
            'soal.*.bobot_nilai' => 'required|integer|min:0',
        ]);

        $inputSoals = $request->input('soal', []); // Array of soal data

        // 1. Ambil ID soal yang ada di database
        $existingIds = SoalUjian::where('ujian_id', $ujianId)->pluck('id')->toArray();
        $submittedIds = array_filter(array_column($inputSoals, 'id')); // Filter null/empty IDs

        // 2. Hapus soal yang tidak ada di input (yang di-remove user)
        $idsToDelete = array_diff($existingIds, $submittedIds);
        if (!empty($idsToDelete)) {
            SoalUjian::whereIn('id', $idsToDelete)->delete();
        }

        // 3. Loop update/create
        foreach ($inputSoals as $index => $data) {
            $pilihanJawaban = [];
            $kunciJawaban = null;
            $jawabanBenarStr = null;

            // Proses sesuai tipe soal (Logic mirip storeSoal tapi disederhanakan untuk bulk)
            switch ($data['tipe_soal']) {
                case 'pilihan_ganda':
                    $rawPilihan = $data['pilihan_jawaban_pilgan'] ?? [];
                    // Filter out empty options (teacher may have used only A,B,C)
                    $pilihanJawaban = array_filter($rawPilihan, fn($v) => $v !== null && $v !== '');
                    $kunciJawaban = $data['kunci_jawaban_pilgan'] ?? null;
                    $jawabanBenarStr = $kunciJawaban;
                    break;

                case 'pilihan_ganda_kompleks':
                    $rawPilihan = $data['pilihan_jawaban_kompleks'] ?? [];
                    $pilihanJawaban = array_filter($rawPilihan, fn($v) => $v !== null && $v !== '');
                    $kunciJawaban = $data['kunci_jawaban_kompleks'] ?? []; // Array
                    // Store jawaban_benar inside pilihan_jawaban for model checkPilihanGandaKompleks()
                    $pilihanJawaban['jawaban_benar'] = array_map('strtoupper', $kunciJawaban);
                    $kunciJawaban = json_encode($kunciJawaban);
                    break;

                case 'benar_salah':
                    // Reconstruct structure: [{pernyataan: "...", kunci: "B"}, ...]
                    $rawBS = $data['pilihan_jawaban_bs'] ?? [];
                    $formattedBS = [];
                    foreach ($rawBS as $row) {
                        if (!empty($row['pernyataan'])) {
                            $formattedBS[] = [
                                'pernyataan' => $row['pernyataan'],
                                'kunci' => $row['kunci'] ?? 'B' // Default Benar
                            ];
                        }
                    }
                    $pilihanJawaban = $formattedBS;
                    // For BS, key is actually stored inside the structure usually, or separate.
                    // Model checkBenarSalah uses 'pernyataan' which has 'benar' boolean or 'kunci' char.
                    // Let's adapt to Model: checkBenarSalah reads $pilihanData['pernyataan'] where items have 'benar' => true/false.
                    // We need to map 'B'->true, 'S'->false.
                    $mappedBS = [];
                    foreach ($formattedBS as $fbs) {
                        $mappedBS[] = [
                            'text' => $fbs['pernyataan'], // Model uses 'text'? existing logic used 'pernyataan' or 'text'
                            'benar' => ($fbs['kunci'] === 'B')
                        ];
                    }
                    $pilihanJawaban = ['pernyataan' => $mappedBS];
                    break;

                case 'isian_singkat':
                    $jawabanBenarStr = $data['kunci_jawaban_isian'] ?? '';
                    $kunciJawaban = $jawabanBenarStr; // Store simple string in kunci_jawaban col
                    // Store jawaban_benar inside pilihan_jawaban for model checkIsianSingkat()
                    $pilihanJawaban = ['jawaban_benar' => [$jawabanBenarStr]];
                    break;

                case 'uraian':
                    // No key
                    break;
            }

            // Handle Image Upload
            $imagePath = null;
            $existingImagePath = $data['existing_image'] ?? null;

            // Check if new image uploaded
            if ($request->hasFile("soal.{$index}.image")) {
                $imageFile = $request->file("soal.{$index}.image");

                // Validate image
                $request->validate([
                    "soal.{$index}.image" => 'image|mimes:jpeg,png,jpg,gif|max:2048'
                ]);

                // Delete old image if exists
                if ($existingImagePath && \Storage::disk('public')->exists($existingImagePath)) {
                    \Storage::disk('public')->delete($existingImagePath);
                }

                // Store new image
                $imagePath = $imageFile->store('soal-images', 'public');
            } elseif ($existingImagePath) {
                // Keep existing image if no new upload and existing_image has value
                $imagePath = $existingImagePath;
            } elseif (empty($existingImagePath) && !empty($data['id'])) {
                // If existing_image is empty but soal has ID, delete old image
                $existingSoal = SoalUjian::find($data['id']);
                if ($existingSoal && $existingSoal->image_path) {
                    if (\Storage::disk('public')->exists($existingSoal->image_path)) {
                        \Storage::disk('public')->delete($existingSoal->image_path);
                    }
                }
            }

            // Prepare Update/Create Data
            $jumlahPilihan = count(array_filter(
                $data['pilihan_jawaban_pilgan'] ?? $data['pilihan_jawaban_kompleks'] ?? [],
                fn($v, $k) => preg_match('/^[A-E]$/', $k) && $v !== null && $v !== '',
                ARRAY_FILTER_USE_BOTH
            ));

            $saveData = [
                'ujian_id' => $ujianId,
                'narasi' => $data['narasi'] ?? null,
                'image_path' => $imagePath,
                'urutan' => $index + 1, // Auto number by loop index
                'tipe_soal' => $data['tipe_soal'],
                'jumlah_pilihan' => in_array($data['tipe_soal'], ['pilihan_ganda', 'pilihan_ganda_kompleks']) ? max($jumlahPilihan, 3) : 5,
                'pertanyaan' => $data['pertanyaan'] ?? '',
                'bobot_nilai' => $data['bobot_nilai'],
                'pilihan_jawaban' => $pilihanJawaban,
                'kunci_jawaban' => $kunciJawaban, // Array or String
                'jawaban_benar' => $jawabanBenarStr, // String columns
            ];

            if (!empty($data['id'])) {
                SoalUjian::where('id', $data['id'])->update($saveData);
            } else {
                SoalUjian::create($saveData);
            }
        }

        // ... existing logic ...

        // SYNC LOGIC HERE
        // Periksa apakah user mencentang 'sync_kelas'
        if ($request->has('sync_kelas') && $request->sync_kelas == '1') {
            $relatedUjian = Ujian::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_ujian', $request->input('original_judul', $ujian->judul_ujian)) // Fallback if not passed
                ->where('tipe_ujian', $ujian->tipe_ujian)
                ->where('id', '!=', $ujian->id)
                ->get();

            $syncedCount = 0;
            foreach ($relatedUjian as $rel) {
                // 1. Hapus semua soal lama di ujian terkait
                SoalUjian::where('ujian_id', $rel->id)->delete();

                // 2. Clone soal baru ke ujian terkait
                $newSoals = SoalUjian::where('ujian_id', $ujianId)->orderBy('urutan', 'asc')->get();
                foreach ($newSoals as $soalSource) {
                    $newSoal = $soalSource->replicate();
                    $newSoal->ujian_id = $rel->id;
                    $newSoal->save();
                }
                $syncedCount++;
            }
            
            if ($syncedCount > 0) {
                 $request->session()->flash('info', "Soal juga berhasil disinkronisasi ke $syncedCount kelas lain.");
            }
        }

        $label = ($ujian->tipe_ujian === 'latihan') ? 'Latihan' : 'Ujian';
        $routeName = ($ujian->tipe_ujian === 'latihan') ? 'guru.lms.latihan.soal.manage' : 'guru.lms.ujian.soal.manage';
        
        return redirect()
            ->route($routeName, [$kelasId, $mapelId, $ujianId])
            ->with('success', "Semua soal $label berhasil disimpan!");
    }

    /**
     * Toggle Status Ujian (Rilis/Tarik)
     */
    public function toggleStatus(Request $request, $kelasId, $mapelId, $ujianId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::findOrFail($ujianId);
        // Toggle is_active
        $ujian->is_active = !$ujian->is_active;
        $ujian->save();

        $syncedCount = 0;
        if ($request->has('sync_kelas') && $request->sync_kelas == '1') {
             $relatedUjian = Ujian::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_ujian', $ujian->judul_ujian)
                ->where('tipe_ujian', $ujian->tipe_ujian)
                ->where('id', '!=', $ujian->id)
                ->get();
            
            foreach($relatedUjian as $rel) {
                $rel->is_active = $ujian->is_active;
                $rel->save();
                $syncedCount++;
            }
        }

        $label = ($ujian->tipe_ujian === 'latihan') ? 'Latihan' : 'Ujian';
        $status = $ujian->is_active ? 'dirilis' : 'ditarik kembali';
        $msg = "$label berhasil $status.";
        if ($syncedCount > 0) {
            $msg .= " (Status disinkronisasi ke $syncedCount kelas lain)";
        }

        return back()->with('success', $msg);
    }

    /**
     * Toggle Result Visibility
     */
    public function toggleResultVisibility(Request $request, $kelasId, $mapelId, $ujianId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::findOrFail($ujianId);
        $ujian->tampilkan_nilai = !$ujian->tampilkan_nilai;
        $ujian->save();

        $syncedCount = 0;
        if ($request->has('sync_kelas') && $request->sync_kelas == '1') {
            $relatedUjian = Ujian::where('guru_id', $tenagaPendidik->id)
                ->where('mata_pelajaran_id', $mapelId)
                ->where('judul_ujian', $ujian->judul_ujian)
                ->where('tipe_ujian', $ujian->tipe_ujian)
                ->where('id', '!=', $ujian->id)
                ->get();

            foreach($relatedUjian as $rel) {
                $rel->tampilkan_nilai = $ujian->tampilkan_nilai;
                $rel->save();
                $syncedCount++;
            }
        }

        $label = ($ujian->tipe_ujian === 'latihan') ? 'Latihan' : 'Ujian';
        $status = $ujian->tampilkan_nilai ? 'ditampilkan' : 'disembunyikan';
        $msg = "Nilai $label berhasil $status ke siswa.";
        if ($syncedCount > 0) {
            $msg .= " (Pengaturan disinkronisasi ke $syncedCount kelas lain)";
        }

        return back()->with('success', $msg);
    }

    /**
     * Tampilkan halaman koreksi jawaban siswa
     */
    public function koreksiShow($kelasId, $mapelId, $ujianId, $ujianSiswaId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);
        $ujian = Ujian::findOrFail($ujianId);
        
        $ujianSiswa = UjianSiswa::with(['siswa', 'jawabanSiswa.soalUjian'])
            ->where('id', $ujianSiswaId)
            ->where('ujian_id', $ujianId)
            ->firstOrFail();

        // Ambil semua soal untuk ditampilkan (termasuk yang tidak dijawab siswa)
        $soalList = SoalUjian::where('ujian_id', $ujianId)
            ->orderBy('urutan', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('guru.lms.ujian.koreksi', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'ujianSiswa' => $ujianSiswa,
            'soalList' => $soalList,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Simpan hasil koreksi manual guru
     */
    public function koreksiStore(Request $request, $kelasId, $mapelId, $ujianId, $ujianSiswaId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $request->validate([
            'nilai' => 'array',
            'nilai.*' => 'numeric|min:0',
            'feedback' => 'array',
            'feedback.*' => 'nullable|string',
        ]);

        $ujianSiswa = UjianSiswa::findOrFail($ujianSiswaId);
        $totalNilai = 0;

        // Loop semua soal untuk update nilai & feedback
        if ($request->has('nilai')) {
            foreach ($request->nilai as $soalId => $nilai) {
                $feedback = $request->feedback[$soalId] ?? null;

                // Cari jawaban yang sudah ada atau siapkan yang baru
                $jawabanSiswa = \App\Models\JawabanSiswa::firstOrNew([
                    'ujian_siswa_id' => $ujianSiswa->id,
                    'soal_ujian_id' => $soalId,
                ]);

                // Jika ini record baru (siswa tidak menjawab), beri nilai default '-'
                if (!$jawabanSiswa->exists) {
                    $jawabanSiswa->jawaban = '-';
                }

                // Update nilai hasil koreksi guru
                $jawabanSiswa->nilai_soal = $nilai;
                $jawabanSiswa->feedback = $feedback;
                $jawabanSiswa->save();
            }
        }

        // Hitung ulang total nilai dari DB
        $totalNilai = $ujianSiswa->jawabanSiswa()->sum('nilai_soal');

        // Normalisasi nilai ke skala 0-100
        $totalBobot = SoalUjian::where('ujian_id', $ujianId)->sum('bobot_nilai');
        $nilaiNormalized = $totalBobot > 0 ? round(($totalNilai / $totalBobot) * 100, 1) : 0;

        // Update nilai terbaik jika nilai baru lebih tinggi
        $nilaiTerbaik = $ujianSiswa->nilai_terbaik ?? 0;
        if ($nilaiNormalized > $nilaiTerbaik) {
            $nilaiTerbaik = $nilaiNormalized;
        }

        // Update status ujian siswa menjadi 'dinilai'
        $ujianSiswa->update([
            'nilai' => $nilaiNormalized,
            'nilai_terbaik' => $nilaiTerbaik,
            'status' => 'dinilai',
        ]);

        // Notify siswa about nilai
        $notificationService = app(NotificationService::class);
        $notificationService->notifyUjianNilaiUpdate($ujianSiswa);

        $isLatihan = request()->routeIs('guru.lms.latihan.*');
        $routeName = $isLatihan ? 'guru.lms.latihan.hasil' : 'guru.lms.ujian.hasil';
        $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';

        return redirect()
            ->route($routeName, [$kelasId, $mapelId, $ujianId])
            ->with('success', "Hasil koreksi $tipeLabel berhasil disimpan. Nilai akhir: " . number_format($nilaiNormalized, 1) . '/100');
    }

    /**
     * Download template Excel soal
     */
    public function downloadSoalTemplate($kelasId, $mapelId, $ujianId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::findOrFail($ujianId);
        $filename = ($ujian->tipe_ujian === 'latihan') ? 'template_soal_latihan.xlsx' : 'template_soal_ujian.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SoalUjianTemplateExport(),
            $filename
        );
    }

    /**
     * Get AI Suggestion for Grading
     */
    public function getAiSuggestion(Request $request, $kelasId, $mapelId, $ujianId, $soalId)
    {
        $request->validate([
            'answer' => 'required|string',
        ]);

        // Verify access
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $soal = SoalUjian::findOrFail($soalId);
        
        // Determine correct answer/key context
        // Priority: kunci_jawaban > jawaban_benar > narasi (for context)
        $kunciJawaban = $soal->kunci_jawaban ?? $soal->jawaban_benar;
        
        if (empty($kunciJawaban)) {
            // For Essay/Uraian, if no key is provided, use Narasi or Question itself as context
            if (in_array($soal->tipe_soal, ['uraian', 'essay']) && !empty($soal->narasi)) {
                $kunciJawaban = "Gunakan konteks dari narasi berikut: \n" . $soal->narasi;
            } elseif (in_array($soal->tipe_soal, ['uraian', 'essay'])) {
                // If absolutely no key or narration, guide AI to be subjective/generative based on question logic
                $kunciJawaban = "Tidak ada kunci jawaban spesifik. Analisis logika dan relevansi jawaban siswa terhadap pertanyaan.";
            } else {
                return response()->json([
                    'error' => true,
                    'feedback' => 'Soal ini tidak memiliki Kunci Jawaban yang tersimpan. AI membutuhkan kunci jawaban sebagai acuan penilaian.'
                ]);
            }
        }
        
        $aiService = new \App\Services\AiGradingService();
        $result = $aiService->evaluate(
            $soal->pertanyaan,
            $request->answer,
            $kunciJawaban,
            $soal->bobot_nilai
        );

        return response()->json($result);
    }

    /**
     * Import soal dari Excel
     */
    public function importSoal(Request $request, $kelasId, $mapelId, $ujianId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $request->validate([
            'file_soal' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\SoalUjianImport($ujianId),
                $request->file('file_soal')
            );

            $isLatihan = request()->routeIs('guru.lms.latihan.*');
            $label = $isLatihan ? 'Latihan' : 'Ujian';
            $routeName = $isLatihan ? 'guru.lms.latihan.soal.manage' : 'guru.lms.ujian.soal.manage';

            return redirect()
                ->route($routeName, [$kelasId, $mapelId, $ujianId])
                ->with('success', "Soal $label berhasil diimport dari Excel!");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Import gagal: ' . implode(' | ', array_slice($errors, 0, 5)));
        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * AI Question Bank Generator
     * Generate questions using AI based on topic, type, difficulty
     */
    public function aiGenerateQuestions(Request $request, $kelasId, $mapelId, $ujianId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'topic' => 'required|string|max:200',
            'type' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,uraian,isian_singkat',
            'difficulty' => 'required|in:easy,medium,hard',
            'count' => 'required|integer|min:1|max:10',
            'custom_instructions' => 'nullable|string|max:500',
            'generate_narasi' => 'nullable|boolean',
        ]);

        try {
            $ujian = Ujian::findOrFail($ujianId);
            $mataPelajaran = MataPelajaran::findOrFail($mapelId);
            $kelas = Kelas::findOrFail($kelasId);

            // Extract kelas number from nama_kelas (e.g., "X-A" -> 10, "7-B" -> 7)
            preg_match('/^(\d+|[IVX]+)/', $kelas->nama_kelas, $matches);
            $kelasNumber = isset($matches[1]) ? $this->romanToNumber($matches[1]) : 10;

            // Call AI Question Generator Service
            $generator = app(\App\Services\AiQuestionGeneratorService::class);
            $result = $generator->generateQuestions(
                $validated['topic'],
                $validated['type'],
                $validated['difficulty'],
                $validated['count'],
                $mataPelajaran->nama_mapel,
                $kelasNumber,
                $validated['custom_instructions'] ?? null,
                $validated['generate_narasi'] ?? false
            );

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'] ?? 'Gagal generate soal.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'questions' => $result['questions'],
                'metadata' => $result['metadata'] ?? [],
                'message' => 'Berhasil generate ' . count($result['questions']) . ' soal!',
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Question Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk Store AI-Generated Questions
     * Save multiple questions at once from AI generator
     */
    public function bulkStoreSoal(Request $request, $kelasId, $mapelId, $ujianId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'soal' => 'required|array|min:1|max:10',
            'soal.*.tipe_soal' => 'required|in:pilihan_ganda,pilihan_ganda_kompleks,benar_salah,uraian,isian_singkat',
            'soal.*.pertanyaan' => 'required|string',
            'soal.*.bobot' => 'required|integer|min:1',
            'soal.*.kunci_jawaban' => 'nullable',
            'soal.*.pilihan_a' => 'nullable|string',
            'soal.*.pilihan_b' => 'nullable|string',
            'soal.*.pilihan_c' => 'nullable|string',
            'soal.*.pilihan_d' => 'nullable|string',
            'soal.*.pilihan_e' => 'nullable|string',
            'soal.*.rubrik_penilaian' => 'nullable|string',
            'soal.*.alternatif_jawaban' => 'nullable|array',
        ]);

        \DB::beginTransaction();
        try {
            $ujian = Ujian::findOrFail($ujianId);
            $currentMaxUrutan = Soal::where('ujian_id', $ujianId)->max('urutan') ?? 0;
            $createdCount = 0;

            foreach ($validated['soal'] as $index => $soalData) {
                $urutan = $currentMaxUrutan + $index + 1;

                // Prepare pilihan_jawaban based on tipe_soal
                $pilihanJawaban = null;
                $kunciJawaban = $soalData['kunci_jawaban'] ?? null;

                if ($soalData['tipe_soal'] === 'pilihan_ganda') {
                    $pilihanJawaban = [
                        'A' => $soalData['pilihan_a'] ?? '',
                        'B' => $soalData['pilihan_b'] ?? '',
                        'C' => $soalData['pilihan_c'] ?? '',
                        'D' => $soalData['pilihan_d'] ?? '',
                        'E' => $soalData['pilihan_e'] ?? '',
                    ];
                } elseif ($soalData['tipe_soal'] === 'benar_salah') {
                    // For true/false, kunci_jawaban is stored as 'B' or 'S'
                    if (in_array(strtolower($kunciJawaban), ['benar', 'true', '1'])) {
                        $kunciJawaban = 'B';
                    } else {
                        $kunciJawaban = 'S';
                    }
                }

                Soal::create([
                    'ujian_id' => $ujian->id,
                    'urutan' => $urutan,
                    'tipe_soal' => $soalData['tipe_soal'],
                    'pertanyaan' => $soalData['pertanyaan'],
                    'pilihan_jawaban' => $pilihanJawaban,
                    'kunci_jawaban' => $kunciJawaban,
                    'bobot' => $soalData['bobot'],
                    'rubrik_penilaian' => $soalData['rubrik_penilaian'] ?? null,
                ]);

                $createdCount++;
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$createdCount} soal berhasil ditambahkan ke ujian!",
                'created_count' => $createdCount,
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Bulk Store Soal Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan soal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Convert Roman numerals to number (for kelas like "X" -> 10)
     */
    private function romanToNumber($roman)
    {
        if (is_numeric($roman)) {
            return (int) $roman;
        }

        $romanMap = [
            'I' => 1, 'II' => 2, 'III' => 3, 'IV' => 4, 'V' => 5,
            'VI' => 6, 'VII' => 7, 'VIII' => 8, 'IX' => 9, 'X' => 10,
            'XI' => 11, 'XII' => 12,
        ];

        return $romanMap[strtoupper($roman)] ?? 10; // Default to 10 if not found
    }

    private function createUjianSiswaForKelas($ujian, $kelasId, $mataPelajaran)
    {
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get()
            ->filter(fn($siswa) => $siswa->canAccessMapel($mataPelajaran));

        foreach ($siswaList as $siswa) {
            UjianSiswa::create([
                'ujian_id' => $ujian->id,
                'siswa_id' => $siswa->id,
                'status' => 'belum_mulai',
            ]);
        }
    }

    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        if (!$this->hasAccess($guruId, $kelasId, $mapelId)) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }

    private function hasAccess($guruId, $kelasId, $mapelId): bool
    {
        return GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();
    }
}
