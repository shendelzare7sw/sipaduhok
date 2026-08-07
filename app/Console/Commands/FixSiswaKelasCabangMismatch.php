<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Console\Command;

class FixSiswaKelasCabangMismatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:fix-kelas-cabang-mismatch {--dry-run : Tampilkan yang akan diperbaiki tanpa mengubah data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki siswa yang kelas_id-nya menunjuk ke kelas milik cabang lain (terjadi jika admin salah pilih kelas di form edit/create siswa karena label kelas tidak menampilkan nama cabang)';

    public function handle()
    {
        $dryRun = (bool) $this->option('dry-run');

        $mismatched = Siswa::termasukNonaktif()
            ->whereNotNull('kelas_id')
            ->with('kelas')
            ->get()
            ->filter(fn ($s) => $s->kelas && $s->cabang_id != $s->kelas->cabang_id);

        $fixedCount = 0;
        $unresolvedCount = 0;

        foreach ($mismatched as $siswa) {
            $oldKelas = $siswa->kelas;

            $newKelas = Kelas::where('tahun_ajaran_id', $oldKelas->tahun_ajaran_id)
                ->where('cabang_id', $siswa->cabang_id)
                ->where('nama_kelas', $oldKelas->nama_kelas)
                ->where('jenjang', $oldKelas->jenjang)
                ->first();

            if (!$newKelas) {
                $this->warn("Siswa '{$siswa->nama_lengkap}' (cabang={$siswa->cabang_id}): kelas '{$oldKelas->nama_kelas}' (id={$oldKelas->id}, cabang={$oldKelas->cabang_id}) tidak punya padanan di cabang sendiri. Dilewati, perlu dicek manual.");
                $unresolvedCount++;
                continue;
            }

            $this->line("Siswa '{$siswa->nama_lengkap}': kelas '{$oldKelas->nama_kelas}' id={$oldKelas->id} (cabang={$oldKelas->cabang_id}) -> id={$newKelas->id} (cabang={$newKelas->cabang_id})");

            if (!$dryRun) {
                $siswa->update(['kelas_id' => $newKelas->id]);
            }

            $fixedCount++;
        }

        $prefix = $dryRun ? '[DRY RUN] ' : '';
        $this->info("{$prefix}Selesai. {$fixedCount} siswa diperbaiki, {$unresolvedCount} tidak ditemukan padanannya.");

        return self::SUCCESS;
    }
}
