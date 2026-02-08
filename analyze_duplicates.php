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

$targetNames = ['Kegiatan Literasi', 'Life Skill'];

foreach ($targetNames as $name) {
    $mapels = MataPelajaran::where('nama_mapel', 'like', "%$name%")->get();
    
    echo "Analisis untuk '$name':\n";
    foreach ($mapels as $mapel) {
        $materiCount = Materi::where('mata_pelajaran_id', $mapel->id)->count();
        $ujianCount = Ujian::where('mata_pelajaran_id', $mapel->id)->count();
        $tugasCount = Tugas::where('mata_pelajaran_id', $mapel->id)->count();
        $jadwalCount = JadwalPelajaran::where('mata_pelajaran_id', $mapel->id)->count();
        $guruCount = GuruPengajarKelas::where('mata_pelajaran_id', $mapel->id)->count();
        
        echo "  ID: {$mapel->id} | Nama: {$mapel->nama_mapel}\n";
        echo "    - Materi: $materiCount\n";
        echo "    - Ujian: $ujianCount\n";
        echo "    - Tugas: $tugasCount\n";
        echo "    - Jadwal (Admin): $jadwalCount\n";
        echo "    - Guru (Access): $guruCount\n";
        echo "--------------------------------\n";
    }
    echo "\n";
}
