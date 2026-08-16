<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk menarik tunggakan TA lama ke TA aktif sebagai tagihan baru,
 * sambil mempertahankan audit-trail (tagihan asal tetap exist, tinggal ditandai dialihkan).
 */
class TunggakanCarryoverService
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    /**
     * Daftar siswa yang punya tunggakan asli (belum dialihkan) di TA selain TA aktif.
     * Hasil di-group per siswa, masing-masing dengan breakdown per-TA.
     *
     * @return \Illuminate\Support\Collection<int, array{siswa: Siswa, totalTunggakan: float, perTahun: array}>
     */
    public function getKandidatTunggakan(?int $cabangId = null)
    {
        $taAktif = TahunAjaran::where('is_active', true)->first();
        if (! $taAktif) {
            return collect();
        }

        // Ambil tagihan original yang belum lunas di TA selain aktif.
        // Hitung sisa per tagihan (jumlah - terbayar).
        $tagihanQuery = Tagihan::query()
            ->belumLunasOriginal()
            ->where('tahun_ajaran_id', '!=', $taAktif->id)
            ->with(['siswa.kelas', 'siswa.cabang', 'tahunAjaran']);

        if ($cabangId) {
            $tagihanQuery->whereHas('siswa', fn ($q) => $q->where('cabang_id', $cabangId));
        }

        $tagihanList = $tagihanQuery->get();

        // Hitung sisa per tagihan via Pembayaran disetujui
        $tagihanIds = $tagihanList->pluck('id');
        $bayarBy = Pembayaran::whereIn('tagihan_id', $tagihanIds)
            ->where('status_validasi', 'disetujui')
            ->select('tagihan_id', DB::raw('SUM(jumlah_bayar) as total'))
            ->groupBy('tagihan_id')
            ->pluck('total', 'tagihan_id');

        $tagihanList = $tagihanList->map(function ($t) use ($bayarBy) {
            $terbayar = (float) ($bayarBy[$t->id] ?? 0);
            $t->setRelation('sisa', max(0, (float) $t->jumlah - $terbayar));

            return $t;
        })->filter(fn ($t) => $t->sisa > 0)->values();

        // Group per siswa
        $grouped = $tagihanList->groupBy('siswa_id')->map(function ($items) {
            $siswa = $items->first()->siswa;
            $perTahun = $items->groupBy('tahun_ajaran_id')->map(function ($g) {
                return [
                    'tahun_ajaran' => $g->first()->tahunAjaran,
                    'tagihan' => $g->values(),
                    'total' => $g->sum('sisa'),
                    'jumlah_item' => $g->count(),
                ];
            })->values();

            return [
                'siswa' => $siswa,
                'totalTunggakan' => $items->sum('sisa'),
                'jumlahItem' => $items->count(),
                'perTahun' => $perTahun,
            ];
        })->sortByDesc('totalTunggakan')->values();

        return $grouped;
    }

    /**
     * Pratinjau tagihan baru yang akan dibuat (tanpa menulis ke DB).
     *
     * @param  int[]  $siswaIds
     */
    public function previewCarryover(array $siswaIds, int $taTujuan): array
    {
        $tagihanAsal = $this->ambilTagihanAsalUntukCarryover($siswaIds, $taTujuan);

        $items = $tagihanAsal->map(function ($t) use ($taTujuan) {
            return [
                'siswa_id' => $t->siswa_id,
                'siswa_nama' => $t->siswa->nama_lengkap ?? '-',
                'tagihan_asal_id' => $t->id,
                'jenis_tagihan' => $t->jenis_tagihan,
                'keterangan_baru' => $this->bangunKeterangan($t),
                'jumlah' => (float) $t->sisa,
                'asal_tahun_ajaran' => $t->tahunAjaran->nama_tahun_ajaran ?? '-',
                'tujuan_tahun_ajaran_id' => $taTujuan,
            ];
        });

        return [
            'items' => $items->values(),
            'total_siswa' => $items->pluck('siswa_id')->unique()->count(),
            'total_tagihan' => $items->count(),
            'grand_total' => (float) $items->sum('jumlah'),
        ];
    }

    /**
     * Eksekusi: buat tagihan baru di TA tujuan + tandai tagihan asal sebagai dialihkan.
     * Notifikasi wali siswa dikirim per tagihan baru.
     *
     * @param  int[]  $siswaIds
     */
    public function executeCarryover(array $siswaIds, int $taTujuan, User $eksekutor): array
    {
        $taTujuanModel = TahunAjaran::findOrFail($taTujuan);
        $tagihanAsal = $this->ambilTagihanAsalUntukCarryover($siswaIds, $taTujuan);

        if ($tagihanAsal->isEmpty()) {
            return [
                'success' => false,
                'created' => 0,
                'message' => 'Tidak ada tagihan tunggakan yang valid untuk dialihkan.',
            ];
        }

        $created = 0;
        $skipped = 0;
        $createdTagihanIds = [];

        DB::beginTransaction();
        try {
            foreach ($tagihanAsal as $asal) {
                // Skip kalau ternyata sudah pernah dialihkan (race condition)
                if ($asal->dialihkan_ke_id !== null) {
                    $skipped++;

                    continue;
                }

                // Buat tagihan baru di TA tujuan
                $baru = Tagihan::create([
                    'siswa_id' => $asal->siswa_id,
                    'tahun_ajaran_id' => $taTujuan,
                    'tagihan_asal_id' => $asal->id,
                    'jenis_tagihan' => $asal->jenis_tagihan,
                    'keterangan' => $this->bangunKeterangan($asal),
                    'jumlah' => $asal->sisa,
                    'tanggal_jatuh_tempo' => $taTujuanModel->getDefaultTagihanDueDate()->toDateString(),
                    'status' => 'belum_bayar',
                ]);

                // Tandai tagihan asal
                $asal->tandaiDialihkan($baru->id);

                $createdTagihanIds[] = $baru->id;
                $created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TunggakanCarryover gagal: '.$e->getMessage(), [
                'siswa_ids' => $siswaIds,
                'ta_tujuan' => $taTujuan,
                'eksekutor' => $eksekutor->id,
            ]);

            return [
                'success' => false,
                'created' => 0,
                'message' => 'Gagal mengeksekusi carryover: '.$e->getMessage(),
            ];
        }

        // Kirim notifikasi (di luar transaction agar tidak rollback kalau notif gagal)
        foreach ($createdTagihanIds as $id) {
            try {
                $tagihanBaru = Tagihan::with('siswa.orangTua', 'tagihanAsal.tahunAjaran')->find($id);
                if ($tagihanBaru) {
                    $this->notifications->notifyTunggakanDialihkan($tagihanBaru);
                }
            } catch (\Throwable $e) {
                Log::warning('Notif carryover gagal untuk tagihan #'.$id.': '.$e->getMessage());
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'skipped' => $skipped,
            'message' => "Berhasil mengalihkan {$created} tagihan tunggakan ke TA ".$taTujuanModel->nama_tahun_ajaran
                .($skipped > 0 ? " ({$skipped} dilewati karena sudah pernah dialihkan)" : '').'.',
        ];
    }

    /**
     * Ambil tagihan asal yang valid untuk carryover (siswa terpilih, TA selain tujuan,
     * masih original/belum dialihkan, masih ada sisa).
     */
    protected function ambilTagihanAsalUntukCarryover(array $siswaIds, int $taTujuan)
    {
        $tagihanQuery = Tagihan::query()
            ->whereIn('siswa_id', $siswaIds)
            ->where('tahun_ajaran_id', '!=', $taTujuan)
            ->whereNull('dialihkan_ke_id')
            ->whereIn('status', ['belum_bayar', 'cicilan', 'terlambat'])
            ->with(['siswa', 'tahunAjaran']);

        $tagihanList = $tagihanQuery->get();

        $bayarBy = Pembayaran::whereIn('tagihan_id', $tagihanList->pluck('id'))
            ->where('status_validasi', 'disetujui')
            ->select('tagihan_id', DB::raw('SUM(jumlah_bayar) as total'))
            ->groupBy('tagihan_id')
            ->pluck('total', 'tagihan_id');

        return $tagihanList->map(function ($t) use ($bayarBy) {
            $terbayar = (float) ($bayarBy[$t->id] ?? 0);
            $t->setRelation('sisa', max(0, (float) $t->jumlah - $terbayar));

            return $t;
        })->filter(fn ($t) => $t->sisa > 0)->values();
    }

    protected function bangunKeterangan(Tagihan $asal): string
    {
        $namaTa = $asal->tahunAjaran->nama_tahun_ajaran ?? 'TA Lama';
        $base = $asal->keterangan ?: ucwords(str_replace('_', ' ', $asal->jenis_tagihan));

        return "Tunggakan {$namaTa}: {$base}";
    }
}
