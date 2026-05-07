<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Services\GuruLmsArsipService;
use Illuminate\Http\Request;

class GuruLmsArsipController extends Controller
{
    public function __construct(
        protected GuruLmsArsipService $service
    ) {
    }

    /**
     * Resolve TenagaPendidik (guru) ID dari user yang login.
     * Abort 403 kalau bukan guru.
     */
    protected function guruId(): int
    {
        $tp = auth()->user()?->tenagaPendidik;
        abort_if(!$tp, 403, 'Akun Anda tidak terhubung dengan data tenaga pendidik.');
        return (int) $tp->id;
    }

    /**
     * Halaman daftar arsip dengan filter.
     */
    public function index(Request $request)
    {
        $guruId = $this->guruId();

        $filters = [
            'tahun_ajaran_id' => $request->integer('tahun_ajaran_id') ?: null,
            'mapel_id' => $request->integer('mapel_id') ?: null,
            'type' => $request->input('type'), // null = semua, atau 'materi'/'tugas'/'latihan'/'ujian'
            'search' => $request->input('search'),
        ];

        $arsip = $this->service->getArsipByGuru(
            $guruId,
            $filters['tahun_ajaran_id'],
            $filters['mapel_id'],
            $filters['type'],
            $filters['search']
        );

        $tahunAjarans = $this->service->getTahunAjaranDenganKonten($guruId);
        $mataPelajarans = $this->service->getMataPelajaranDenganKonten($guruId);

        $kelasMapelTujuan = $this->service->getKelasMapelTujuan($guruId);

        return view('guru.lms.arsip.index', array_merge(
            compact('arsip', 'tahunAjarans', 'mataPelajarans', 'filters', 'kelasMapelTujuan'),
            ['totalKonten' => $arsip['materi']->count() + $arsip['tugas']->count() + $arsip['latihan']->count() + $arsip['ujian']->count()]
        ));
    }

    /**
     * Preview konten arsip (read-only).
     */
    public function preview(string $type, int $id)
    {
        $guruId = $this->guruId();

        $konten = $this->resolveKonten($type, $id, $guruId);

        // Reuse view monitoring-lms preview, dengan parameter khusus arsip-mode
        $previewView = match ($type) {
            'materi' => 'guru.lms.arsip.preview-materi',
            'tugas' => 'guru.lms.arsip.preview-tugas',
            'ujian', 'latihan' => 'guru.lms.arsip.preview-ujian',
            default => abort(404),
        };

        return view($previewView, [
            'konten' => $konten,
            'kontenType' => $type,
            'kontenId' => $id,
        ]);
    }

    /**
     * Form pilih kelas+mapel tujuan untuk salin.
     */
    public function formSalin(string $type, int $id)
    {
        $guruId = $this->guruId();
        $konten = $this->resolveKonten($type, $id, $guruId);
        $kelasMapelTujuan = $this->service->getKelasMapelTujuan($guruId);

        $previewTitle = match ($type) {
            'materi' => $konten->judul_materi,
            'tugas' => $konten->judul_tugas,
            'ujian', 'latihan' => $konten->judul_ujian,
            default => 'Konten',
        };

        return view('guru.lms.arsip.form-salin', [
            'konten' => $konten,
            'kontenType' => $type,
            'kontenId' => $id,
            'previewTitle' => $previewTitle,
            'kelasMapelTujuan' => $kelasMapelTujuan,
        ]);
    }

    /**
     * Eksekusi salin.
     */
    public function salin(Request $request)
    {
        $guruId = $this->guruId();

        $validated = $request->validate([
            'type' => 'required|in:materi,tugas,latihan,ujian',
            'sumber_id' => 'required|integer',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'mata_pelajaran_id' => 'required|integer|exists:mata_pelajaran,id',
            'sertakan_soal' => 'nullable|boolean',
        ]);

        try {
            $hasil = match ($validated['type']) {
                'materi' => $this->service->salinMateri(
                    (int) $validated['sumber_id'],
                    (int) $validated['kelas_id'],
                    (int) $validated['mata_pelajaran_id'],
                    $guruId
                ),
                'tugas' => $this->service->salinTugas(
                    (int) $validated['sumber_id'],
                    (int) $validated['kelas_id'],
                    (int) $validated['mata_pelajaran_id'],
                    $guruId
                ),
                'latihan', 'ujian' => $this->service->salinUjian(
                    (int) $validated['sumber_id'],
                    (int) $validated['kelas_id'],
                    (int) $validated['mata_pelajaran_id'],
                    $guruId,
                    (bool) ($validated['sertakan_soal'] ?? true)
                ),
            };
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menyalin konten: ' . $e->getMessage());
        }

        // Redirect ke daftar konten kelas+mapel tujuan
        $route = match ($validated['type']) {
            'materi' => route('guru.lms.materi.index', [$validated['kelas_id'], $validated['mata_pelajaran_id']]),
            'tugas' => route('guru.lms.tugas.index', [$validated['kelas_id'], $validated['mata_pelajaran_id']]),
            'latihan' => route('guru.lms.latihan.index', [$validated['kelas_id'], $validated['mata_pelajaran_id']]),
            'ujian' => route('guru.lms.ujian.index', [$validated['kelas_id'], $validated['mata_pelajaran_id']]),
        };

        return redirect($route)
            ->with('success', 'Konten arsip berhasil disalin ke kelas tujuan. Silakan cek dan sesuaikan jadwal sebelum diaktifkan.');
    }

    protected function resolveKonten(string $type, int $id, int $guruId)
    {
        return match ($type) {
            'materi' => Materi::with(['kelas.tahunAjaran', 'mataPelajaran', 'guru'])
                ->where('guru_id', $guruId)
                ->findOrFail($id),
            'tugas' => Tugas::with(['kelas.tahunAjaran', 'mataPelajaran', 'guru'])
                ->where('guru_id', $guruId)
                ->findOrFail($id),
            'latihan', 'ujian' => Ujian::with(['kelas.tahunAjaran', 'mataPelajaran', 'guru', 'soalUjian'])
                ->where('guru_id', $guruId)
                ->findOrFail($id),
            default => abort(404),
        };
    }
}
