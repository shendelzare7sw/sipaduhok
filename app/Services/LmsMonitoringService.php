<?php

namespace App\Services;

use App\Models\CatatanMonitoring;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\Ujian;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LmsMonitoringService
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Daftar kelas + jumlah konten LMS, di-scope opsional per cabang (untuk WAKA).
     * tahunAjaranId === 0 berarti "semua TA"; null = default ke TA aktif.
     */
    public function getKelasList(?int $cabangId = null, ?string $search = null, ?int $tahunAjaranId = null, bool $onlyWithContent = false): Collection
    {
        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas'])
            ->withCount(['materi', 'tugas'])
            ->withCount([
                'ujian as latihan_count' => fn($q) => $q->where('tipe_ujian', Ujian::TIPE_LATIHAN),
                'ujian as ujian_count' => fn($q) => $q->where('tipe_ujian', '!=', Ujian::TIPE_LATIHAN),
            ]);

        if ($cabangId) {
            $query->where('cabang_id', $cabangId);
        }

        if ($tahunAjaranId !== null && $tahunAjaranId > 0) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('kode_kelas', 'like', "%{$search}%");
            });
        }

        $kelas = $query->orderBy('jenjang')->orderBy('nama_kelas')->get();

        if ($onlyWithContent) {
            $kelas = $kelas->filter(fn($k) => ($k->materi_count + $k->tugas_count + $k->latihan_count + $k->ujian_count) > 0)->values();
        }

        // Sort: kelas berisi konten dulu, lalu kelas kosong
        return $kelas->sortByDesc(fn($k) => ($k->materi_count + $k->tugas_count + $k->latihan_count + $k->ujian_count) > 0 ? 1 : 0)->values();
    }

    /**
     * Pastikan kelas dapat diakses (cabang scoping). Throw 404 kalau tidak.
     */
    public function findKelasOrFail(int $kelasId, ?int $cabangId = null): Kelas
    {
        $query = Kelas::with(['cabang', 'tahunAjaran', 'waliKelas']);
        if ($cabangId) {
            $query->where('cabang_id', $cabangId);
        }
        return $query->findOrFail($kelasId);
    }

    /**
     * Konten LMS di sebuah kelas, dipisah jadi 4 bucket: materi, tugas, latihan (Ujian tipe=latihan), ujian.
     *
     * @return array{
     *   materi: Collection,
     *   tugas: Collection,
     *   latihan: Collection,
     *   ujian: Collection,
     *   mapelOptions: Collection
     * }
     */
    public function getKontenByKelas(int $kelasId, ?int $mapelId = null, ?string $search = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $materiQ = Materi::with(['guru', 'mataPelajaran'])->where('kelas_id', $kelasId);
        $tugasQ = Tugas::with(['guru', 'mataPelajaran'])->where('kelas_id', $kelasId);
        $ujianQ = Ujian::with(['guru', 'mataPelajaran'])->withCount('soalUjian')->where('kelas_id', $kelasId);

        if ($mapelId) {
            $materiQ->where('mata_pelajaran_id', $mapelId);
            $tugasQ->where('mata_pelajaran_id', $mapelId);
            $ujianQ->where('mata_pelajaran_id', $mapelId);
        }

        if ($search) {
            $like = "%{$search}%";
            $materiQ->where('judul_materi', 'like', $like);
            $tugasQ->where('judul_tugas', 'like', $like);
            $ujianQ->where('judul_ujian', 'like', $like);
        }

        if ($dateFrom) {
            $materiQ->whereDate('tanggal_upload', '>=', $dateFrom);
            $tugasQ->whereDate('tanggal_mulai', '>=', $dateFrom);
            $ujianQ->whereDate('tanggal_mulai', '>=', $dateFrom);
        }

        if ($dateTo) {
            $materiQ->whereDate('tanggal_upload', '<=', $dateTo);
            $tugasQ->whereDate('tanggal_mulai', '<=', $dateTo);
            $ujianQ->whereDate('tanggal_mulai', '<=', $dateTo);
        }

        $materi = $materiQ->orderByDesc('tanggal_upload')->orderByDesc('created_at')->get();
        $tugas = $tugasQ->orderByDesc('tanggal_mulai')->orderByDesc('created_at')->get();
        $ujianAll = $ujianQ->orderByDesc('tanggal_mulai')->orderByDesc('created_at')->get();

        $latihan = $ujianAll->where('tipe_ujian', Ujian::TIPE_LATIHAN)->values();
        $ujian = $ujianAll->where('tipe_ujian', '!=', Ujian::TIPE_LATIHAN)->values();

        // Daftar mapel yang punya konten di kelas ini (untuk dropdown filter)
        // Only show mapel that match the kelas's jenjang to avoid confusing duplicates
        $kelas = Kelas::find($kelasId);
        $mapelIds = Materi::where('kelas_id', $kelasId)->pluck('mata_pelajaran_id')
            ->merge(Tugas::where('kelas_id', $kelasId)->pluck('mata_pelajaran_id'))
            ->merge(Ujian::where('kelas_id', $kelasId)->pluck('mata_pelajaran_id'))
            ->unique()->values();

        $mapelOptionsQuery = MataPelajaran::whereIn('id', $mapelIds)->orderBy('nama_mapel');

        // If kelas has a jenjang, prefer mapel matching that jenjang
        // but also include mismatched ones (they have content) with a label
        $mapelOptions = $mapelOptionsQuery->get();

        // Add jenjang label to disambiguate duplicates
        $nameCounts = $mapelOptions->groupBy('nama_mapel')->filter(fn($g) => $g->count() > 1)->keys();
        if ($nameCounts->isNotEmpty()) {
            $mapelOptions->each(function ($mp) use ($nameCounts) {
                if ($nameCounts->contains($mp->nama_mapel)) {
                    $mp->nama_mapel = $mp->nama_mapel . ' (' . $mp->jenjang . ')';
                }
            });
        }

        return [
            'materi' => $materi,
            'tugas' => $tugas,
            'latihan' => $latihan,
            'ujian' => $ujian,
            'mapelOptions' => $mapelOptions,
        ];
    }

    public function findMateriOrFail(int $id, ?int $cabangId = null): Materi
    {
        $materi = Materi::with(['guru.user', 'mataPelajaran', 'kelas.cabang'])->findOrFail($id);
        $this->assertCabangAccess($materi->kelas, $cabangId);
        return $materi;
    }

    public function findTugasOrFail(int $id, ?int $cabangId = null): Tugas
    {
        $tugas = Tugas::with(['guru.user', 'mataPelajaran', 'kelas.cabang'])->findOrFail($id);
        $this->assertCabangAccess($tugas->kelas, $cabangId);
        return $tugas;
    }

    public function findUjianOrFail(int $id, ?int $cabangId = null): Ujian
    {
        $ujian = Ujian::with(['guru.user', 'mataPelajaran', 'kelas.cabang', 'soalUjian'])->findOrFail($id);
        $this->assertCabangAccess($ujian->kelas, $cabangId);
        return $ujian;
    }

    private function assertCabangAccess(?Kelas $kelas, ?int $cabangId): void
    {
        if ($cabangId && $kelas && (int) $kelas->cabang_id !== $cabangId) {
            abort(403, 'Konten ini di luar cabang yang Anda kelola.');
        }
    }

    /**
     * Buat catatan + kirim notifikasi ke guru pemilik konten.
     */
    public function kirimCatatan(User $pengirim, string $kontenType, int $kontenId, string $isi, ?int $cabangId = null): CatatanMonitoring
    {
        [$guruId, $kelasId, $mapelId] = $this->resolveKontenContext($kontenType, $kontenId, $cabangId);

        $catatan = DB::transaction(function () use ($pengirim, $kontenType, $kontenId, $guruId, $kelasId, $mapelId, $isi) {
            return CatatanMonitoring::create([
                'pengirim_id' => $pengirim->id,
                'pengirim_role' => $pengirim->role ?? 'unknown',
                'guru_id' => $guruId,
                'konten_type' => $kontenType,
                'konten_id' => $kontenId,
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mapelId,
                'isi_catatan' => $isi,
            ]);
        });

        $catatan->load('guru.user', 'pengirim');
        $this->notificationService->notifyCatatanMonitoring($catatan);

        return $catatan;
    }

    /**
     * @return array{0:int,1:int,2:int} [guru_id, kelas_id, mapel_id]
     */
    private function resolveKontenContext(string $type, int $id, ?int $cabangId): array
    {
        return match ($type) {
            CatatanMonitoring::KONTEN_MATERI => (function () use ($id, $cabangId) {
                $m = $this->findMateriOrFail($id, $cabangId);
                return [(int) $m->guru_id, (int) $m->kelas_id, (int) $m->mata_pelajaran_id];
            })(),
            CatatanMonitoring::KONTEN_TUGAS => (function () use ($id, $cabangId) {
                $t = $this->findTugasOrFail($id, $cabangId);
                return [(int) $t->guru_id, (int) $t->kelas_id, (int) $t->mata_pelajaran_id];
            })(),
            CatatanMonitoring::KONTEN_UJIAN => (function () use ($id, $cabangId) {
                $u = $this->findUjianOrFail($id, $cabangId);
                return [(int) $u->guru_id, (int) $u->kelas_id, (int) $u->mata_pelajaran_id];
            })(),
            default => abort(404, 'Tipe konten tidak dikenal.'),
        };
    }
}
