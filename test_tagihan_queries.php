<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TAGIHAN CONTROLLER QUERY TEST ===\n";
echo "Script started at: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Load composer autoload directly
    echo "Step 1: Loading composer autoload...\n";
    require_once __DIR__ . '/vendor/autoload.php';
    echo " Autoload loaded\n\n";
    
    // Load Laravel app
    echo "Step 2: Loading Laravel bootstrap...\n";
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo " App instance created\n";
    
    echo "Step 3: Bootstrapping kernel...\n";
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo " Kernel bootstrapped\n\n";

} catch (Exception $e) {
    echo "Error during bootstrap: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Continuing with tests...\n\n";
}

// Now test queries
try {
    echo "=== RUNNING QUERIES ===\n\n";

    // TEST 1: TahunAjaran
    echo "TEST 1: TahunAjaran - Active Year\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
        echo " Query executed successfully\n";
        if ($tahunAjaranAktif) {
            echo "  ID: " . $tahunAjaranAktif->id . "\n";
            echo "  Nama: " . $tahunAjaranAktif->nama_tahun_ajaran . "\n";
        } else {
            echo "   No active year found\n";
        }
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    // TEST 2: All TahunAjaran
    echo "\nTEST 2: TahunAjaran - All Years\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $allTahunAjaran = \App\Models\TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
        echo " Query executed successfully\n";
        echo "  Found: " . $allTahunAjaran->count() . " records\n";
        if ($allTahunAjaran->count() > 0) {
            echo "  First: " . $allTahunAjaran->first()->nama_tahun_ajaran . "\n";
        }
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    // TEST 3: Kelas
    echo "\nTEST 3: Kelas with Cabang\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $kelasList = \App\Models\Kelas::with('cabang')->limit(1)->get();
        echo " Query executed successfully\n";
        echo "  Found: " . $kelasList->count() . " classes\n";
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    // TEST 4: Siswa Base Query
    echo "\nTEST 4: Siswa - Base Query\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $siswaCount = \App\Models\Siswa::whereIn('status', ['aktif', 'lulus'])->count();
        echo " Query executed successfully\n";
        echo "  Found: " . $siswaCount . " siswa\n";
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    // TEST 5: Siswa with JOIN (Simple test)
    echo "\nTEST 5: Siswa - Query with JOIN to Kelas\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $siswaList = \App\Models\Siswa::whereIn('status', ['aktif', 'lulus'])
            ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
            ->select('siswa.*')
            ->orderBy('kelas.jenjang', 'asc')
            ->orderBy('siswa.nama_lengkap', 'asc')
            ->limit(5)
            ->get();
        echo " Query executed successfully\n";
        echo "  Found: " . $siswaList->count() . " siswa (first 5)\n";
        if ($siswaList->count() > 0) {
            echo "  First siswa: " . $siswaList->first()->nama_lengkap . "\n";
        }
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
        echo "  Message: " . $e->getMessage() . "\n";
    }

    // TEST 6: Tagihan
    echo "\nTEST 6: Tagihan - Query for Student\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $tagihan = \App\Models\Tagihan::where('siswa_id', 1)->limit(10)->get();
        echo " Query executed successfully\n";
        echo "  Found: " . $tagihan->count() . " tagihan for siswa ID=1\n";
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    // TEST 7: Pembayaran
    echo "\nTEST 7: Pembayaran - Query for Student\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $totalTerbayar = \App\Models\Pembayaran::where('siswa_id', 1)
            ->where('status_validasi', 'disetujui')
            ->sum('jumlah_bayar');
        echo " Query executed successfully\n";
        echo "  Total paid for siswa ID=1: Rp " . number_format($totalTerbayar) . "\n";
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    // TEST 8: Tunggakan
    echo "\nTEST 8: Tunggakan Summary\n";
    echo str_repeat("-", 50) . "\n";
    try {
        $tunggakanData = \App\Models\Tagihan::select(
            'tahun_ajaran_id',
            \Illuminate\Support\Facades\DB::raw('COUNT(DISTINCT siswa_id) as jumlah_siswa'),
            \Illuminate\Support\Facades\DB::raw('SUM(jumlah) as total_tunggakan')
        )
        ->whereIn('status', ['belum_bayar', 'cicilan', 'terlambat'])
        ->groupBy('tahun_ajaran_id')
        ->limit(5)
        ->get();
        echo " Query executed successfully\n";
        echo "  Years with unpaid bills: " . $tunggakanData->count() . "\n";
    } catch (Throwable $e) {
        echo " Error: " . $e->getMessage() . "\n";
    }

    echo "\n" . str_repeat("=", 50) . "\n";
    echo "All tests completed at: " . date('Y-m-d H:i:s') . "\n";
    echo " All queries executed without fatal errors!\n";

} catch (Throwable $e) {
    echo "\n FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line " . $e->getLine() . ")\n";
}
?>
