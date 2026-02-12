<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MataPelajaran;
use App\Models\Materi;
use App\Models\Ujian;
use App\Models\Tugas;
use App\Models\JadwalPelajaran;
use App\Models\GuruPengajarKelas;
use Illuminate\Support\Facades\DB;

echo "Mulai Perbaikan Data Duplikat...\n";

DB::beginTransaction();

try {
    // 1. Identifikasi Nama Mapel Duplikat
    $duplicates = MataPelajaran::select('nama_mapel', DB::raw('count(*) as count'))
        ->groupBy('nama_mapel')
        ->having('count', '>', 1)
        ->get();

    foreach ($duplicates as $dup) {
        echo "\nMemproses: " . $dup->nama_mapel . "\n";
        
        $allMapels = MataPelajaran::where('nama_mapel', $dup->nama_mapel)->get();
        
        // 2. Tentukan Target ID
        // Prioritas: Yang punya Jadwal Pelajaran -> Yang ID-nya paling besar
        $targetId = null;
        $maxJadwalCount = -1;
        
        foreach ($allMapels as $mapel) {
            $jadwalCount = JadwalPelajaran::where('mata_pelajaran_id', $mapel->id)->count();
            if ($jadwalCount > $maxJadwalCount) {
                $maxJadwalCount = $jadwalCount;
                $targetId = $mapel->id;
            } elseif ($jadwalCount == $maxJadwalCount) {
                // If tie, pick larger ID (assuming newer is better/fixer? or just pick one)
                if ($mapel->id > $targetId) {
                    $targetId = $mapel->id;
                }
            }
        }

        echo "  Target ID Terpilih: $targetId (Jadwal: $maxJadwalCount)\n";

        // 3. Pindahkan Data dari ID lain ke Target ID
        foreach ($allMapels as $mapel) {
            if ($mapel->id == $targetId) continue;

            echo "  Menggabungkan ID {$mapel->id} ke {$targetId}...\n";

            // Pindahkan Materi
            $materiAffected = Materi::where('mata_pelajaran_id', $mapel->id)->update(['mata_pelajaran_id' => $targetId]);
            echo "    - Materi dipindahkan: $materiAffected\n";

            // Pindahkan Ujian
            $ujianAffected = Ujian::where('mata_pelajaran_id', $mapel->id)->update(['mata_pelajaran_id' => $targetId]);
            echo "    - Ujian dipindahkan: $ujianAffected\n";

            // Pindahkan Tugas
            $tugasAffected = Tugas::where('mata_pelajaran_id', $mapel->id)->update(['mata_pelajaran_id' => $targetId]);
            echo "    - Tugas dipindahkan: $tugasAffected\n";

            // Pindahkan Guru Pengajar (Handle Duplicate Entry)
            $guruEntries = GuruPengajarKelas::where('mata_pelajaran_id', $mapel->id)->get();
            foreach ($guruEntries as $entry) {
                // Cek apakah target sudah punya entry ini
                $exists = GuruPengajarKelas::where('tenaga_pendidik_id', $entry->tenaga_pendidik_id)
                    ->where('kelas_id', $entry->kelas_id)
                    ->where('mata_pelajaran_id', $targetId)
                    ->exists();

                if ($exists) {
                    // Jika sudah ada, hapus yang lama agar tidak error unique constraint (jika ada)
                    // Atau biarkan dihapus nanti saat delete mapel? 
                    // Better delete this specific entry now to be clean.
                    $entry->delete();
                    echo "    - Akses Guru (Kelas {$entry->kelas_id}) sudah ada di target, menghapus entry lama.\n";
                } else {
                    // Update ke target
                    $entry->update(['mata_pelajaran_id' => $targetId]);
                    echo "    - Akses Guru (Kelas {$entry->kelas_id}) dipindahkan.\n";
                }
            }

            // Pindahkan Jadwal (jika ada sisa di yang lama, gabung ke baru)
            $jadwalAffected = JadwalPelajaran::where('mata_pelajaran_id', $mapel->id)->update(['mata_pelajaran_id' => $targetId]);
            echo "    - Jadwal sisa dipindahkan: $jadwalAffected\n";

            // 4. Hapus Mapel Lama
            $mapel->delete();
            echo "    - Mapel ID {$mapel->id} dihapus.\n";
        }
    }

    DB::commit();
    echo "\nSelesai! Data berhasil diperbaiki.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\nERROR: Terjadi kesalahan. Rollback dilakukan.\n";
    echo $e->getMessage() . "\n";
}
