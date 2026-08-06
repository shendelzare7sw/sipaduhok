<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
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

    /**
     * Salin BANYAK konten arsip sekaligus ke satu kelas+mapel tujuan.
     * $items berisi string "type:id" (mis. "materi:5", "ujian:12").
     * Melaporkan ringkasan berhasil/gagal; item gagal tidak menggagalkan lainnya.
     */
    public function salinBulk(Request $request)
    {
        $guruId = $this->guruId();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*' => 'required|string',
            'kelas_id' => 'required|integer|exists:kelas,id',
            'mata_pelajaran_id' => 'required|integer|exists:mata_pelajaran,id',
            'sertakan_soal' => 'nullable|boolean',
        ]);

        $kelasId = (int) $validated['kelas_id'];
        $mapelId = (int) $validated['mata_pelajaran_id'];
        $sertakanSoal = (bool) ($validated['sertakan_soal'] ?? true);

        $berhasil = 0;
        $gagal = 0;
        $errorTerakhir = null;

        foreach ($validated['items'] as $item) {
            [$type, $rawId] = array_pad(explode(':', $item, 2), 2, null);

            if (!in_array($type, ['materi', 'tugas', 'latihan', 'ujian'], true) || !ctype_digit((string) $rawId)) {
                $gagal++;
                continue;
            }

            $sumberId = (int) $rawId;

            try {
                match ($type) {
                    'materi' => $this->service->salinMateri($sumberId, $kelasId, $mapelId, $guruId),
                    'tugas' => $this->service->salinTugas($sumberId, $kelasId, $mapelId, $guruId),
                    'latihan', 'ujian' => $this->service->salinUjian($sumberId, $kelasId, $mapelId, $guruId, $sertakanSoal),
                };
                $berhasil++;
            } catch (\Throwable $e) {
                $gagal++;
                $errorTerakhir = $e->getMessage();
            }
        }

        $pesan = "{$berhasil} konten berhasil disalin ke kelas tujuan. Silakan cek & sesuaikan jadwal sebelum diaktifkan.";
        if ($gagal > 0) {
            $pesan = "{$berhasil} berhasil, {$gagal} gagal disalin"
                . ($errorTerakhir ? " ({$errorTerakhir})" : '') . '.';
        }

        // Kembali ke Arsip (pertahankan filter) agar bisa lanjut memilih.
        return redirect()->route('guru.lms.arsip.index', $request->only(['type', 'tahun_ajaran_id', 'mapel_id', 'search']))
            ->with($berhasil === 0 ? 'error' : 'success', $pesan);
    }

    protected function resolveKonten(string $type, int $id, int $guruId)
    {
        return match ($type) {
            'materi' => $this->service->findArsipMateri($id, $guruId),
            'tugas' => $this->service->findArsipTugas($id, $guruId),
            'latihan', 'ujian' => $this->service->findArsipUjian($id, $guruId),
            default => abort(404),
        };
    }
}
