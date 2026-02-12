<?php
use App\Models\Rapor;

$rapor = Rapor::with('raporNilai.mataPelajaran')->find(1);

if (!$rapor) {
    echo "Rapor ID 1 not found\n";
    exit;
}

echo "Rapor ID: 1\n";
echo "Siswa: " . $rapor->siswa->nama_lengkap . "\n";
echo "Rapor Nilai Count: " . $rapor->raporNilai->count() . "\n";

foreach ($rapor->raporNilai as $rn) {
    echo "Mapel: " . ($rn->mataPelajaran ? $rn->mataPelajaran->nama_mapel : 'NULL') . " | ";
    echo "Kelompok DB: '" . ($rn->mataPelajaran ? $rn->mataPelajaran->kelompok : 'N/A') . "' | ";
    echo "Is A? " . ($rn->mataPelajaran && $rn->mataPelajaran->kelompok === 'A' ? 'YES' : 'NO') . " | ";
    echo "Is B? " . ($rn->mataPelajaran && $rn->mataPelajaran->kelompok === 'B' ? 'YES' : 'NO') . "\n";
}
