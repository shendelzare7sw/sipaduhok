<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\UjianSiswa;
use App\Models\MataPelajaran;

$id = 36;
$mataPelajaran = MataPelajaran::find(11);

$results = UjianSiswa::with('siswa')
    ->where('ujian_id', $id)
    ->get();

echo "Total Raw Results: " . $results->count() . "\n";

foreach($results as $r) {
    $hasSiswa = $r->siswa ? "YES" : "NO";
    $canAccess = $r->siswa ? ($r->siswa->canAccessMapel($mataPelajaran) ? "TRUE" : "FALSE") : "N/A";
    echo "ID: {$r->id} | Siswa: {$hasSiswa} | Can Access: {$canAccess} | Status: {$r->status}\n";
}

$filtered = $results->filter(function($result) use ($mataPelajaran) {
    if (!$result->siswa) return true;
    return $result->siswa->canAccessMapel($mataPelajaran);
});

echo "Total Filtered Results: " . $filtered->count() . "\n";
