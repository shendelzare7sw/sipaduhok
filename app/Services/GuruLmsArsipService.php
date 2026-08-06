<?php

namespace App\Services;

use App\Models\GuruPengajarKelas;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\SoalUjian;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\Ujian;
use Illuminate\Support\Facades\DB;

/**
 * Service untuk arsip LMS guru: lihat materi/tugas/ujian milik guru
 * lintas TA (bypass scope GuruPengajarKelas saat ini), dan menyalinnya
 * ke kelas+mapel yang diampu di TA aktif.
 */
class GuruLmsArsipService
{
    /**
     * Batasi query ke konten milik guru ini SENDIRI (guru_id) ATAU konten di
     * kelas+mapel yang PERNAH ia ampu (lintas TA, lewat GuruPengajarKelas).
     * Klausa kedua ini yang bikin guru PENGGANTI tetap bisa melihat & menyalin
     * arsip peninggalan guru sebelumnya di kelas+mapel yang sekarang ia ampu —
     * tanpa klausa ini, Arsip LMS hanya menampilkan konten milik diri sendiri.
     */
    private function scopeMilikAtauDiampu($query, string $table, int $guruId)
    {
        return $query->where(function ($q) use ($table, $guruId) {
            $q->where('guru_id', $guruId)
                ->orWhereExists(function ($sub) use ($table, $guruId) {
                    $sub->selectRaw('1')
                        ->from('guru_pengajar_kelas')
                        ->whereColumn('guru_pengajar_kelas.kelas_id', "{$table}.kelas_id")
                        ->whereColumn('guru_pengajar_kelas.mata_pelajaran_id', "{$table}.mata_pelajaran_id")
                        ->where('guru_pengajar_kelas.tenaga_pendidik_id', $guruId);
                });
        });
    }

    /**
     * Ambil semua konten LMS milik guru (atau kelas+mapel yang pernah ia
     * ampu), dengan filter optional.
     *
     * @return array{materi: \Illuminate\Support\Collection, tugas: \Illuminate\Support\Collection, ujian: \Illuminate\Support\Collection, latihan: \Illuminate\Support\Collection}
     */
    public function getArsipByGuru(
        int $guruId,
        ?int $tahunAjaranId = null,
        ?int $mapelId = null,
        ?string $type = null,
        ?string $search = null
    ): array {
        $applyTaFilter = function ($query) use ($tahunAjaranId) {
            if ($tahunAjaranId) {
                $query->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $tahunAjaranId));
            }
        };

        $materi = collect();
        $tugas = collect();
        $latihan = collect();
        $ujian = collect();

        if ($type === null || $type === 'materi') {
            $q = Materi::with(['kelas.tahunAjaran', 'mataPelajaran']);
            $this->scopeMilikAtauDiampu($q, 'materi', $guruId);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_materi', 'like', "%{$search}%");
            $materi = $q->orderByDesc('tanggal_upload')->orderByDesc('id')->get();
        }

        if ($type === null || $type === 'tugas') {
            $q = Tugas::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('jenis_tugas', Tugas::JENIS_TUGAS);
            $this->scopeMilikAtauDiampu($q, 'tugas', $guruId);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_tugas', 'like', "%{$search}%");
            $tugas = $q->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        }

        if ($type === null || $type === 'latihan') {
            $q = Ujian::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('tipe_ujian', Ujian::TIPE_LATIHAN);
            $this->scopeMilikAtauDiampu($q, 'ujian', $guruId);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_ujian', 'like', "%{$search}%");
            $latihan = $q->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        }

        if ($type === null || $type === 'ujian') {
            $q = Ujian::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('tipe_ujian', '!=', Ujian::TIPE_LATIHAN);
            $this->scopeMilikAtauDiampu($q, 'ujian', $guruId);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_ujian', 'like', "%{$search}%");
            $ujian = $q->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        }

        return compact('materi', 'tugas', 'latihan', 'ujian');
    }

    /**
     * Tahun ajaran yang punya konten milik guru ini (atau kelas+mapel yang
     * pernah ia ampu).
     */
    public function getTahunAjaranDenganKonten(int $guruId)
    {
        $taIdsMateri = $this->scopeMilikAtauDiampu(Materi::query(), 'materi', $guruId)
            ->join('kelas', 'materi.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $taIdsTugas = $this->scopeMilikAtauDiampu(Tugas::query(), 'tugas', $guruId)
            ->join('kelas', 'tugas.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $taIdsUjian = $this->scopeMilikAtauDiampu(Ujian::query(), 'ujian', $guruId)
            ->join('kelas', 'ujian.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $ids = $taIdsMateri->merge($taIdsTugas)->merge($taIdsUjian)->unique()->filter()->values();

        return TahunAjaran::whereIn('id', $ids)->orderByDesc('tanggal_mulai')->get();
    }

    /**
     * Mata pelajaran yang pernah diisi guru ini, atau di kelas+mapel yang
     * pernah ia ampu (lintas TA) — untuk dropdown filter.
     */
    public function getMataPelajaranDenganKonten(int $guruId)
    {
        $ids = collect()
            ->merge($this->scopeMilikAtauDiampu(Materi::query(), 'materi', $guruId)->pluck('mata_pelajaran_id'))
            ->merge($this->scopeMilikAtauDiampu(Tugas::query(), 'tugas', $guruId)->pluck('mata_pelajaran_id'))
            ->merge($this->scopeMilikAtauDiampu(Ujian::query(), 'ujian', $guruId)->pluck('mata_pelajaran_id'))
            ->unique()
            ->filter()
            ->values();

        return MataPelajaran::whereIn('id', $ids)->orderBy('nama_mapel')->get();
    }

    /**
     * Ambil satu materi arsip (preview/form-salin) — milik guru ini atau di
     * kelas+mapel yang pernah ia ampu. 404 kalau bukan salah satunya.
     */
    public function findArsipMateri(int $id, int $guruId): Materi
    {
        return $this->scopeMilikAtauDiampu(
            Materi::with(['kelas.tahunAjaran', 'mataPelajaran', 'guru']),
            'materi',
            $guruId
        )->findOrFail($id);
    }

    public function findArsipTugas(int $id, int $guruId): Tugas
    {
        return $this->scopeMilikAtauDiampu(
            Tugas::with(['kelas.tahunAjaran', 'mataPelajaran', 'guru']),
            'tugas',
            $guruId
        )->findOrFail($id);
    }

    public function findArsipUjian(int $id, int $guruId): Ujian
    {
        return $this->scopeMilikAtauDiampu(
            Ujian::with(['kelas.tahunAjaran', 'mataPelajaran', 'guru', 'soalUjian']),
            'ujian',
            $guruId
        )->findOrFail($id);
    }

    /**
     * Kelas + mata pelajaran yang sekarang diampu guru di TA aktif.
     * Output: collection of ['kelas' => Kelas, 'mata_pelajaran' => MataPelajaran].
     */
    public function getKelasMapelTujuan(int $guruId)
    {
        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (!$taAktif) {
            return collect();
        }

        return GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $taAktif->id))
            ->with(['kelas', 'mataPelajaran'])
            ->get()
            ->map(fn($gpk) => [
                'kelas' => $gpk->kelas,
                'mata_pelajaran' => $gpk->mataPelajaran,
                'kelas_id' => $gpk->kelas_id,
                'mata_pelajaran_id' => $gpk->mata_pelajaran_id,
            ])
            ->values();
    }

    /**
     * Validasi guru memang ditugaskan ke kelas+mapel tujuan di TA aktif.
     */
    public function validateKelasMapelTujuan(int $guruId, int $kelasId, int $mapelId): bool
    {
        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (!$taAktif) return false;

        return GuruPengajarKelas::where('tenaga_pendidik_id', $guruId)
            ->where('kelas_id', $kelasId)
            ->where('mata_pelajaran_id', $mapelId)
            ->whereHas('kelas', fn($k) => $k->where('tahun_ajaran_id', $taAktif->id))
            ->exists();
    }

    public function salinMateri(int $sumberId, int $kelasTujuanId, int $mapelTujuanId, int $guruId): Materi
    {
        $sumber = $this->scopeMilikAtauDiampu(Materi::query(), 'materi', $guruId)->findOrFail($sumberId);

        if (!$this->validateKelasMapelTujuan($guruId, $kelasTujuanId, $mapelTujuanId)) {
            throw new \RuntimeException('Anda tidak ditugaskan ke kelas + mata pelajaran tujuan di TA aktif.');
        }

        return Materi::create([
            'kelas_id' => $kelasTujuanId,
            'mata_pelajaran_id' => $mapelTujuanId,
            'guru_id' => $guruId,
            'judul_materi' => $sumber->judul_materi,
            'kategori' => $sumber->kategori,
            'deskripsi' => $sumber->deskripsi,
            'file_materi' => $sumber->file_materi, // file path direuse — tidak duplikasi storage
            'url_materi' => $sumber->url_materi,
            'tipe_file' => $sumber->tipe_file,
            'tanggal_upload' => now()->toDateString(),
        ]);
    }

    public function salinTugas(int $sumberId, int $kelasTujuanId, int $mapelTujuanId, int $guruId): Tugas
    {
        $sumber = $this->scopeMilikAtauDiampu(Tugas::query(), 'tugas', $guruId)->findOrFail($sumberId);

        if (!$this->validateKelasMapelTujuan($guruId, $kelasTujuanId, $mapelTujuanId)) {
            throw new \RuntimeException('Anda tidak ditugaskan ke kelas + mata pelajaran tujuan di TA aktif.');
        }

        // urutan akan auto-generate via boot() Tugas model
        return Tugas::create([
            'kelas_id' => $kelasTujuanId,
            'mata_pelajaran_id' => $mapelTujuanId,
            'guru_id' => $guruId,
            'jenis_tugas' => $sumber->jenis_tugas,
            'judul_tugas' => $sumber->judul_tugas,
            'judul_bab' => $sumber->judul_bab,
            'nama_materi' => $sumber->nama_materi,
            'deskripsi' => $sumber->deskripsi,
            'file_tugas' => $sumber->file_tugas, // file path direuse
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_deadline' => $sumber->tanggal_deadline
                ? now()->addDays(now()->diffInDays($sumber->tanggal_deadline, false))->toDateString()
                : now()->addWeek()->toDateString(),
            'tampilkan_nilai' => $sumber->tampilkan_nilai,
            'bisa_diulang' => $sumber->bisa_diulang,
            'batas_pengulangan' => $sumber->batas_pengulangan,
        ]);
    }

    public function salinUjian(
        int $sumberId,
        int $kelasTujuanId,
        int $mapelTujuanId,
        int $guruId,
        bool $sertakanSoal = true
    ): Ujian {
        $sumber = $this->scopeMilikAtauDiampu(Ujian::query(), 'ujian', $guruId)->findOrFail($sumberId);

        if (!$this->validateKelasMapelTujuan($guruId, $kelasTujuanId, $mapelTujuanId)) {
            throw new \RuntimeException('Anda tidak ditugaskan ke kelas + mata pelajaran tujuan di TA aktif.');
        }

        return DB::transaction(function () use ($sumber, $kelasTujuanId, $mapelTujuanId, $guruId, $sertakanSoal) {
            $baru = Ujian::create([
                'kelas_id' => $kelasTujuanId,
                'mata_pelajaran_id' => $mapelTujuanId,
                'guru_id' => $guruId,
                'judul_ujian' => $sumber->judul_ujian,
                'deskripsi' => $sumber->deskripsi,
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(7),
                'durasi_menit' => $sumber->durasi_menit,
                'is_active' => false, // default off — guru aktivasi manual setelah cek
                // Catatan: kolom 'acak_soal' tidak ada di tabel `ujian` (fitur di-drop);
                // jangan disisipkan agar insert tidak gagal (SQLSTATE 42S22).
                'tampilkan_nilai' => $sumber->tampilkan_nilai,
                'bisa_diulang' => $sumber->bisa_diulang,
                'batas_pengulangan' => $sumber->batas_pengulangan,
                'tampilkan_riwayat' => $sumber->tampilkan_riwayat,
                'tipe_ujian' => $sumber->tipe_ujian,
            ]);

            if ($sertakanSoal) {
                $soalSumber = SoalUjian::where('ujian_id', $sumber->id)
                    ->orderBy('urutan')
                    ->orderBy('id')
                    ->get();

                foreach ($soalSumber as $soal) {
                    SoalUjian::create([
                        'ujian_id' => $baru->id,
                        'narasi' => $soal->narasi,
                        'urutan' => $soal->urutan,
                        'image_path' => $soal->image_path, // image path direuse
                        'pertanyaan' => $soal->pertanyaan,
                        'tipe_soal' => $soal->tipe_soal,
                        'jumlah_pilihan' => $soal->jumlah_pilihan,
                        'pilihan_jawaban' => $soal->pilihan_jawaban,
                        'jawaban_benar' => $soal->jawaban_benar,
                        'kunci_jawaban' => $soal->kunci_jawaban,
                        'bobot_nilai' => $soal->bobot_nilai,
                    ]);
                }
            }

            return $baru;
        });
    }
}
