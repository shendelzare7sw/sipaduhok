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

        $ujianList = Ujian::where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->where('guru_id', $tenagaPendidik->id)
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);

        return view('guru.lms.ujian.index', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'ujianList' => $ujianList,
            'guru' => $tenagaPendidik,
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
        $pertemuanId = $request->get('pertemuan_id');

        return view('guru.lms.ujian.create', [
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
            'pertemuanId' => $pertemuanId,
        ]);
    }

    /**
     * Simpan ujian baru
     */
    public function store(Request $request, $kelasId, $mapelId): RedirectResponse
    {
        $tenagaPendidik = TenagaPendidik::where('user_id', auth()->id())->firstOrFail();
        $this->verifyAccess($tenagaPendidik->id, $kelasId, $mapelId);

        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:harian,uts,uas',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'required|integer|min:1',
            'pertemuan_id' => 'nullable|exists:pertemuans,id',
        ]);

        $ujian = Ujian::create([
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mapelId,
            'pertemuan_id' => $validated['pertemuan_id'] ?? null,
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

        if (!empty($validated['pertemuan_id'])) {
            return redirect()
                ->route('guru.lms.pertemuan.show', [$kelasId, $mapelId, $validated['pertemuan_id']])
                ->with('success', 'Ujian berhasil ditambahkan ke pertemuan');
        }

        return redirect()
            ->route('guru.lms.ujian.index', [$kelasId, $mapelId])
            ->with('success', 'Ujian berhasil ditambahkan');
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

        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = MataPelajaran::findOrFail($mapelId);

        return view('guru.lms.ujian.edit', [
            'ujian' => $ujian,
            'kelas' => $kelas,
            'mapel' => $mataPelajaran,
            'guru' => $tenagaPendidik,
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

        $validated = $request->validate([
            'judul_ujian' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_ujian' => 'required|in:harian,uts,uas',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'durasi_menit' => 'required|integer|min:1',
        ]);

        $ujian->update($validated);

        return redirect()
            ->route('guru.lms.ujian.index', [$kelasId, $mapelId])
            ->with('success', 'Ujian berhasil diperbarui');
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
            'ujian' => $ujian,
            'results' => $results,
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
            'bobot' => $validated['bobot'],
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
     * Verifikasi akses guru
     */
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