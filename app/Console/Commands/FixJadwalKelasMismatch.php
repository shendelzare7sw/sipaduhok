<?php

namespace App\Console\Commands;

use App\Models\GuruPengajarKelas;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use Illuminate\Console\Command;

class FixJadwalKelasMismatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:fix-kelas-mismatch {--dry-run : Tampilkan yang akan diperbaiki tanpa mengubah data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki jadwal_pelajaran yang pivot kelas-nya masih menunjuk ke kelas tahun ajaran lain (terjadi jika form Tambah Jadwal diisi tanpa reload setelah mengganti Tahun Ajaran)';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $jadwalList = JadwalPelajaran::whereNotNull('tahun_ajaran_id')->with('kelas')->get();

        $fixedCount = 0;
        $unresolvedCount = 0;

        foreach ($jadwalList as $jadwal) {
            $mismatched = $jadwal->kelas->filter(fn($k) => $k->tahun_ajaran_id != $jadwal->tahun_ajaran_id);

            foreach ($mismatched as $oldKelas) {
                $newKelas = Kelas::where('tahun_ajaran_id', $jadwal->tahun_ajaran_id)
                    ->where('nama_kelas', $oldKelas->nama_kelas)
                    ->where('jenjang', $oldKelas->jenjang)
                    ->where('cabang_id', $oldKelas->cabang_id)
                    ->first();

                if (!$newKelas) {
                    $this->warn("Jadwal #{$jadwal->id}: kelas '{$oldKelas->nama_kelas}' (id={$oldKelas->id}, TA={$oldKelas->tahun_ajaran_id}) tidak punya padanan di TA #{$jadwal->tahun_ajaran_id}. Dilewati, perlu dicek manual.");
                    $unresolvedCount++;
                    continue;
                }

                $this->line("Jadwal #{$jadwal->id} ({$jadwal->hari}): '{$oldKelas->nama_kelas}' id={$oldKelas->id} (TA={$oldKelas->tahun_ajaran_id}) -> id={$newKelas->id} (TA={$newKelas->tahun_ajaran_id})");

                if (!$dryRun) {
                    $jadwal->kelas()->detach($oldKelas->id);
                    $jadwal->kelas()->syncWithoutDetaching([$newKelas->id]);

                    if ($jadwal->kelas_id == $oldKelas->id) {
                        $jadwal->update(['kelas_id' => $newKelas->id]);
                    }

                    // guru_pengajar_kelas ikut disinkron ulang karena dibuat dari kelas yang sama saat jadwal dibuat.
                    if ($jadwal->guru_id && $jadwal->mata_pelajaran_id) {
                        GuruPengajarKelas::where('tenaga_pendidik_id', $jadwal->guru_id)
                            ->where('kelas_id', $oldKelas->id)
                            ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                            ->delete();

                        GuruPengajarKelas::firstOrCreate([
                            'tenaga_pendidik_id' => $jadwal->guru_id,
                            'kelas_id' => $newKelas->id,
                            'mata_pelajaran_id' => $jadwal->mata_pelajaran_id,
                        ]);
                    }
                }

                $fixedCount++;
            }
        }

        $prefix = $dryRun ? '[DRY RUN] ' : '';
        $this->info("{$prefix}Selesai. {$fixedCount} pivot kelas diperbaiki, {$unresolvedCount} tidak ditemukan padanannya.");

        return self::SUCCESS;
    }
}
