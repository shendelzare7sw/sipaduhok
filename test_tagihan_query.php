<?php
// Test tagihan query tanpa students yang punya kelas

require_once __DIR__ . '/bootstrap/app.php';

$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Setup
$query = \App\Models\Siswa::whereIn('status', ['aktif', 'lulus']);

echo "=== Testing LEFT JOIN for students without class ===\n\n";

try {
    // Test 1: Check total siswa tanpa join
    $totalSiswa = $query->count();
    echo "✓ Total siswa aktif/lulus: $totalSiswa\n";
    
    // Test 2: Check siswa yang punya kelas
    $siswaDeganKelas = \App\Models\Siswa::whereIn('status', ['aktif', 'lulus'])->whereNotNull('kelas_id')->count();
    echo "✓ Siswa dengan kelas_id: $siswaDeganKelas\n";
    
    // Test 3: Check siswa tanpa kelas
    $siswaTanpaKelas = \App\Models\Siswa::whereIn('status', ['aktif', 'lulus'])->whereNull('kelas_id')->count();
    echo "✓ Siswa tanpa kelas (kelas_id = null): $siswaTanpaKelas\n";
    
    // Test 4: Test LEFT JOIN query seperti di controller
    echo "\n=== Testing LEFT JOIN Query ===\n";
    $query2 = \App\Models\Siswa::whereIn('status', ['aktif', 'lulus']);
    $siswaList = $query2->leftJoin('kelas', 'siswa.kelas_id', '=', 'kelas.id')
        ->select('siswa.*')
        ->orderByRaw('COALESCE(kelas.jenjang, 999) asc')
        ->orderBy('siswa.nama_lengkap', 'asc')
        ->orderBy('siswa.id', 'asc')
        ->limit(10)
        ->get();
    
    echo "✓ Query berhasil! Returned " . $siswaList->count() . " siswa\n\n";
    
    if ($siswaList->count() > 0) {
        echo "Sample data:\n";
        foreach ($siswaList->take(3) as $siswa) {
            echo "  - ID: {$siswa->id}, Nama: {$siswa->nama_lengkap}, Kelas ID: " . ($siswa->kelas_id ?? 'NULL') . "\n";
        }
    }
    
    echo "\n✅ All tests passed!\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error occurred:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
?>
