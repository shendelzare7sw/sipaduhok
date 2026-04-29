<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\UjianSiswa;

$m = MataPelajaran::find(11);
$s = Siswa::find(272);
$can = $s->canAccessMapel($m);
echo "Student: {$s->nama_lengkap} | Mapel: {$m->nama_mapel} | Can Access: " . ($can ? "TRUE" : "FALSE") . "\n";

$r = UjianSiswa::where('ujian_id', 36)->where('siswa_id', 272)->first();
if ($r) {
    echo "UjianSiswa Record Found: ID {$r->id} | Status: {$r->status} | Nilai: {$r->nilai}\n";
} else {
    echo "UjianSiswa Record NOT Found for Nathan and Exam 36\n";
}
