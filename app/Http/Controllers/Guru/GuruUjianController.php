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

        // Determine if this is kuis or ujian based on route
        $isKuis = request()->routeIs('guru.lms.kuis.*');
        $tipeUjian = $isKuis ? 'kuis' : 'ujian';

        $query = Ujian::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id);

        // Filter by type
        if ($isKuis) {
            $query->where('tipe_ujian', 'kuis');
        } else {
            // Ujian includes: ulangan_harian, pts_ganjil, pas_ganjil, pts_genap, pas_genap
            $query->where('tipe_ujian', '!=', 'kuis');
        }

        $ujianList = $query->orderBy('tanggal_mulai', 'desc')
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
        $tipeUjian = request()->routeIs('guru.lms.kuis.*') ? 'kuis' : 'ujian';

        return view('guru.lms.ujian.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
        ]);
    }

    /**
     * Simpan ujian baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $isKuis = request()->routeIs('guru.lms.kuis.*');

        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:ulangan_harian,kuis,uts,uas,pts_ganjil,pas_ganjil,pts_genap,pas_genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'nullable|integer|min:0',
        ]);

        // Jika dari kuis route, pastikan tipe_ujian adalah kuis
        if ($isKuis) {
            $validated['tipe_ujian'] = 'kuis';
        }

        $ujian = Ujian::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'guru_id' => $tenagaPendidik->id,
            'judul_ujian' => $validated['judul_ujian'],
            'deskripsi' => $validated['deskripsi'],
            'tipe_ujian' => $validated['tipe_ujian'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'durasi_menit' => $validated['durasi_menit'],
        ]);

        // Buat UjianSiswa untuk setiap siswa di kelas
        $siswaList = Siswa::where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->get();

        foreach ($siswaList as $siswa) {
            UjianSiswa::create([
                'ujian_id' => $ujian->id,
                'siswa_id' => $siswa->id,
                'status' => 'belum_mulai',
            ]);
        }

        $routeName = $isKuis ? 'guru.lms.kuis.index' : 'guru.lms.ujian.index';
        return redirect()
            ->route($routeName, [$kelasId, $mapelId])
            ->with('success', $isKuis ? 'Kuis berhasil ditambahkan' : 'Ujian berhasil ditambahkan');
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

        $isKuis = request()->routeIs('guru.lms.kuis.*');
        $tipeUjian = $isKuis ? 'kuis' : 'ujian';

        // Verify tipe_ujian matches route
        if ($isKuis && $ujian->tipe_ujian !== 'kuis') {
            abort(404, 'Kuis tidak ditemukan');
        }
        if (!$isKuis && $ujian->tipe_ujian === 'kuis') {
            abort(404, 'Ujian tidak ditemukan');
        }

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        return view('guru.lms.ujian.edit', [
            'ujian' => $ujian,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'tipeUjian' => $tipeUjian,
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

        $isKuis = request()->routeIs('guru.lms.kuis.*');

        // Verify tipe_ujian matches route
        if ($isKuis && $ujian->tipe_ujian !== 'kuis') {
            abort(404, 'Kuis tidak ditemukan');
        }
        if (!$isKuis && $ujian->tipe_ujian === 'kuis') {
            abort(404, 'Ujian tidak ditemukan');
        }

        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:ulangan_harian,kuis,uts,uas,pts_ganjil,pas_ganjil,pts_genap,pas_genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'nullable|integer|min:0',
        ]);

        // Prevent changing tipe_ujian when updating from kuis route
        if ($isKuis) {
            $validated['tipe_ujian'] = 'kuis';
        }

        $ujian->update($validated);

        $routeName = $isKuis ? 'guru.lms.kuis.index' : 'guru.lms.ujian.index';
        return redirect()
            ->route($routeName, [$kelasId, $mapelId])
            ->with('success', $isKuis ? 'Kuis berhasil diperbarui' : 'Ujian berhasil diperbarui');
    }

    /**
     * Hapus ujian
     */
    public function destroy($kelasId, $mapelId, $id): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $ujian = Ujian::where('id', $id)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->firstOrFail();

        $ujian->delete();

        return redirect()
            ->route('guru.lms.ujian.index', [$kelasId, $mapelId])
            ->with('success', 'Ujian berhasil dihapus');
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
            ->get();

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

        return view('guru.lms.ujian.manage_soal', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujian' => $ujian,
            'soalList' => $soalList,
            'guru' => $tenagaPendidik,
        ]);
    }

    /**
     * Simpan Semua Soal (Bulk Update)
     */
    public function storeAllSoal(Request $request, $kelasId, $mapelId, $ujianId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

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

        return redirect()
            ->route('guru.lms.ujian.soal.manage', [$kelasId, $mapelId, $ujianId])
            ->with('success', 'Semua soal berhasil disimpan!');
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

        $status = $ujian->is_active ? 'dirilis' : 'ditarik kembali';
        return back()->with('success', "Ujian berhasil $status.");
    }
    private function verifyAccess($guruId, $kelasId, $mapelId)
    {
        $access = GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->exists();

        if (!$access) {
            abort(403, 'Anda tidak memiliki akses ke mata pelajaran ini');
        }
    }
}
