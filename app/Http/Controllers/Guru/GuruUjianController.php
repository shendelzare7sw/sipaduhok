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
        ];

        // Buat untuk kelas utama
        $ujian = Ujian::create(array_merge($ujianData, ['kelas_id' => $kelasId]));
        $this->createUjianSiswaForKelas($ujian, $kelasId, $mataPelajaran);

        // Duplikasi ke kelas tambahan
        $kelasTambahan = $request->input('kelas_tambahan', []);
        $jumlahDuplikasi = 0;
        foreach ($kelasTambahan as $kelasLainId) {
            if ($this->hasAccess($tenagaPendidik->id, $kelasLainId, $mapelId)) {
                $ujianDuplikat = Ujian::create(array_merge($ujianData, ['kelas_id' => $kelasLainId]));
                $this->createUjianSiswaForKelas($ujianDuplikat, $kelasLainId, $mataPelajaran);
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
        ]);

        // Prevent changing tipe_ujian when updating from latihan route
        if ($isLatihan) {
            $validated['tipe_ujian'] = 'latihan';
        }

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

        $results = UjianSiswa::with('siswa')
            ->where('ujian_id', $id)
            ->get()
            ->filter(function($result) use ($mataPelajaran) {
                 return $result->siswa && $result->siswa->canAccessMapel($mataPelajaran);
            });

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

        return view('guru.lms.ujian.manage_soal', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'soalList' => $soalList,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
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
            'soal.*.pertanyaan' => 'required|string',
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
                    $pilihanJawaban = $data['pilihan_jawaban_pilgan'] ?? [];
                    // Ensure keys are A, B, C, D, E for storage consistency
                    $kunciJawaban = $data['kunci_jawaban_pilgan'] ?? null;
                    $jawabanBenarStr = $kunciJawaban;
                    break;

                case 'pilihan_ganda_kompleks':
                    $pilihanJawaban = $data['pilihan_jawaban_kompleks'] ?? [];
                    $kunciJawaban = $data['kunci_jawaban_kompleks'] ?? []; // Array
                    // Normalize for complex to include 'jawaban_benar' inside pilihan structure if needed by frontend
                    // But model expects keys: options, jawaban_benar separately usually or standard structure.
                    // Let's stick to standard: pilihan_jawaban = inputs, kunci_jawaban = selected keys
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
                    // Optional: store multiple possibilities in pilihan_jawaban if supported
                    break;

                case 'uraian':
                    // No key
                    break;
            }

            // Prepare Update/Create Data
            $saveData = [
                'ujian_id' => $ujianId,
                'narasi' => $data['narasi'] ?? null,
                'urutan' => $index + 1, // Auto number by loop index
                'tipe_soal' => $data['tipe_soal'],
                'pertanyaan' => $data['pertanyaan'],
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
     * Download template Excel soal
     */
    public function downloadSoalTemplate($kelasId, $mapelId, $ujianId)
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SoalUjianTemplateExport(),
            'template_soal_ujian.xlsx'
        );
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
            $routeName = $isLatihan ? 'guru.lms.latihan.soal.manage' : 'guru.lms.ujian.soal.manage';

            return redirect()
                ->route($routeName, [$kelasId, $mapelId, $ujianId])
                ->with('success', 'Soal berhasil diimport dari Excel!');
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
