<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Nilai;
use App\Models\Siswa;

$n = Nilai::where('kelas_id', 47)->where('mata_pelajaran_id', 11)->get();
foreach($n as $item) {
    $siswa = Siswa::find($item->siswa_id);
    $nama = $siswa ? $siswa->nama_lengkap : 'Unknown';
    echo "Siswa: {$nama} (ID: {$item->siswa_id}) | PTS: {$item->pts} | PAS: {$item->pas} | UH1: {$item->uh_1} | UH2: {$item->uh_2}\n";
}
