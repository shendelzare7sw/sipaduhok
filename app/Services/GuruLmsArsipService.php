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
     * Ambil semua konten LMS milik guru, dengan filter optional.
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
            $q = Materi::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('guru_id', $guruId);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_materi', 'like', "%{$search}%");
            $materi = $q->orderByDesc('tanggal_upload')->orderByDesc('id')->get();
        }

        if ($type === null || $type === 'tugas') {
            $q = Tugas::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('guru_id', $guruId)
                ->where('jenis_tugas', Tugas::JENIS_TUGAS);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_tugas', 'like', "%{$search}%");
            $tugas = $q->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        }

        if ($type === null || $type === 'latihan') {
            $q = Ujian::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('guru_id', $guruId)
                ->where('tipe_ujian', Ujian::TIPE_LATIHAN);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_ujian', 'like', "%{$search}%");
            $latihan = $q->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        }

        if ($type === null || $type === 'ujian') {
            $q = Ujian::with(['kelas.tahunAjaran', 'mataPelajaran'])
                ->where('guru_id', $guruId)
                ->where('tipe_ujian', '!=', Ujian::TIPE_LATIHAN);
            $applyTaFilter($q);
            if ($mapelId) $q->where('mata_pelajaran_id', $mapelId);
            if ($search) $q->where('judul_ujian', 'like', "%{$search}%");
            $ujian = $q->orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        }

        return compact('materi', 'tugas', 'latihan', 'ujian');
    }

    /**
     * Tahun ajaran yang punya konten milik guru ini.
     */
    public function getTahunAjaranDenganKonten(int $guruId)
    {
        $taIdsMateri = Materi::where('guru_id', $guruId)
            ->join('kelas', 'materi.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $taIdsTugas = Tugas::where('guru_id', $guruId)
            ->join('kelas', 'tugas.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $taIdsUjian = Ujian::where('guru_id', $guruId)
            ->join('kelas', 'ujian.kelas_id', '=', 'kelas.id')
            ->pluck('kelas.tahun_ajaran_id');

        $ids = $taIdsMateri->merge($taIdsTugas)->merge($taIdsUjian)->unique()->filter()->values();

        return TahunAjaran::whereIn('id', $ids)->orderByDesc('tanggal_mulai')->get();
    }

    /**
     * Mata pelajaran yang pernah diisi guru ini (lintas TA) — untuk dropdown filter.
     */
    public function getMataPelajaranDenganKonten(int $guruId)
    {
        $ids = collect()
            ->merge(Materi::where('guru_id', $guruId)->pluck('mata_pelajaran_id'))
            ->merge(Tugas::where('guru_id', $guruId)->pluck('mata_pelajaran_id'))
            ->merge(Ujian::where('guru_id', $guruId)->pluck('mata_pelajaran_id'))
            ->unique()
            ->filter()
            ->values();

        return MataPelajaran::whereIn('id', $ids)->orderBy('nama_mapel')->get();
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
        $sumber = Materi::where('guru_id', $guruId)->findOrFail($sumberId);

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
        $sumber = Tugas::where('guru_id', $guruId)->findOrFail($sumberId);

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
        $sumber = Ujian::where('guru_id', $guruId)->findOrFail($sumberId);

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
                'acak_soal' => $sumber->acak_soal,
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
