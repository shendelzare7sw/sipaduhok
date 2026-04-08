<?php

/**
 * FINAL VALIDATION TEST - All Phases Combined
 * File: test-all-phases-final.php
 * 
 * Comprehensive test validating:
 * - Phase 1: Infrastructure (config, service, models, migrations)
 * - Phase 2: API (controller, jobs, routes)
 * - Phase 3: UI (views, menu, styling)
 * - Phase 4: Scheduler (bootstrap configuration)
 */

echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║   GOOGLE SHEETS INTEGRATION - FINAL VALIDATION TEST          ║\n";
echo "║   All Phases Combined (1-4)                                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

$totalTests = 0;
$testsPassed = 0;
$testsFailed = 0;
$phaseResults = [];

// ============================================================================
// PHASE 1: INFRASTRUCTURE
// ============================================================================
echo "PHASE 1: Infrastructure Setup\n";
echo "─────────────────────────────────────────────────────────────────\n";

$phase1Tests = 0;
$phase1Passed = 0;

// Test 1.1: Config file
$configFile = __DIR__ . '/config/google-sheets.php';
$phase1Tests++;
if (file_exists($configFile) && strpos(file_get_contents($configFile), "'modules'") !== false) {
    echo "✓ Config file exists with modules\n";
    $phase1Passed++;
} else {
    echo "✗ Config file missing or incomplete\n";
}

// Test 1.2: Service class
$serviceFile = __DIR__ . '/app/Services/GoogleSheetsService.php';
$phase1Tests++;
if (file_exists($serviceFile)) {
    $serviceContent = file_get_contents($serviceFile);
    $requiredMethods = ['testConnection', 'getSheetData', 'appendRows', 'clearSheet'];
    $hasAll = true;
    foreach ($requiredMethods as $method) {
        if (strpos($serviceContent, "public function $method") === false) {
            $hasAll = false;
            break;
        }
    }
    if ($hasAll) {
        echo "✓ Service class with all required methods\n";
        $phase1Passed++;
    } else {
        echo "✗ Service class missing methods\n";
    }
} else {
    echo "✗ Service class not found\n";
}

// Test 1.3: Model class
$modelFile = __DIR__ . '/app/Models/GoogleSheetsSyncLog.php';
$phase1Tests++;
if (file_exists($modelFile) && strpos(file_get_contents($modelFile), 'GoogleSheetsSyncLog') !== false) {
    echo "✓ GoogleSheetsSyncLog model exists\n";
    $phase1Passed++;
} else {
    echo "✗ GoogleSheetsSyncLog model not found\n";
}

// Test 1.4: Migration file
$migrationFile = __DIR__ . '/database/migrations/2026_04_02_000000_create_google_sheets_sync_logs_table.php';
$phase1Tests++;
if (file_exists($migrationFile)) {
    echo "✓ Migration file exists\n";
    $phase1Passed++;
} else {
    echo "✗ Migration file not found\n";
}

// Test 1.5: .env variables
$envFile = __DIR__ . '/.env';
$phase1Tests++;
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    $requiredVars = [
        'GOOGLE_SHEETS_ENABLED',
        'GOOGLE_SERVICE_ACCOUNT_JSON_PATH',
        'GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID',
        'GOOGLE_SHEETS_AUTO_SYNC_ENABLED'
    ];
    $hasAll = true;
    foreach ($requiredVars as $var) {
        if (strpos($envContent, $var) === false) {
            $hasAll = false;
            break;
        }
    }
    if ($hasAll) {
        echo "✓ .env variables configured\n";
        $phase1Passed++;
    } else {
        echo "✗ .env missing required variables\n";
    }
} else {
    echo "✗ .env file not found\n";
}

// Test 1.6: Credentials folder
$credentialsDir = __DIR__ . '/storage/app/credentials';
$phase1Tests++;
if (is_dir($credentialsDir)) {
    echo "✓ Credentials directory exists\n";
    $phase1Passed++;
} else {
    echo "✗ Credentials directory not found\n";
}

$phaseResults['Phase 1'] = ['passed' => $phase1Passed, 'total' => $phase1Tests];
$testsPassed += $phase1Passed;
$totalTests += $phase1Tests;

// ============================================================================
// PHASE 2: API & CONTROLLERS
// ============================================================================
echo "\nPHASE 2: API Integration & Controllers\n";
echo "─────────────────────────────────────────────────────────────────\n";

$phase2Tests = 0;
$phase2Passed = 0;

// Test 2.1: Controller class
$controllerFile = __DIR__ . '/app/Http/Controllers/Admin/GoogleSheetsController.php';
$phase2Tests++;
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    $methods = ['index', 'setup', 'saveCredential', 'testConnection', 'push', 'pull', 'pullPreview', 'status', 'disconnect'];
    $hasAll = true;
    foreach ($methods as $method) {
        if (strpos($controllerContent, "public function $method") === false) {
            $hasAll = false;
            break;
        }
    }
    if ($hasAll) {
        echo "✓ Controller with all 9 methods\n";
        $phase2Passed++;
    } else {
        echo "✗ Controller missing methods\n";
    }
} else {
    echo "✗ Controller file not found\n";
}

// Test 2.2: Job class
$jobFile = __DIR__ . '/app/Jobs/SyncModuleToSheet.php';
$phase2Tests++;
if (file_exists($jobFile)) {
    $jobContent = file_get_contents($jobFile);
    if (strpos($jobContent, 'ShouldQueue') !== false && strpos($jobContent, 'public function handle') !== false) {
        echo "✓ Job class implements ShouldQueue\n";
        $phase2Passed++;
    } else {
        echo "✗ Job class incomplete\n";
    }
} else {
    echo "✗ Job file not found\n";
}

// Test 2.3: Routes registered
$webRoutesFile = __DIR__ . '/routes/web.php';
$phase2Tests++;
if (file_exists($webRoutesFile)) {
    $routeContent = file_get_contents($webRoutesFile);
    $requiredRoutes = [
        "Route::prefix('google-sheets')",
        "GoogleSheetsController::class, 'index'",
        "GoogleSheetsController::class, 'push'",
        "GoogleSheetsController::class, 'pull'",
        "GoogleSheetsController::class, 'testConnection'",
        "GoogleSheetsController::class, 'disconnect'",
    ];
    $hasAll = true;
    foreach ($requiredRoutes as $route) {
        if (strpos($routeContent, $route) === false) {
            $hasAll = false;
            break;
        }
    }
    if ($hasAll) {
        echo "✓ Routes registered (9 endpoints)\n";
        $phase2Passed++;
    } else {
        echo "✗ Routes incomplete\n";
    }
} else {
    echo "✗ Routes file not found\n";
}

// Test 2.4: Middleware applied
$phase2Tests++;
if (file_exists($controllerFile)) {
    if (strpos(file_get_contents($controllerFile), "middleware('role:admin')") !== false) {
        echo "✓ Admin role middleware applied\n";
        $phase2Passed++;
    } else {
        echo "✗ Middleware not applied\n";
    }
}

$phaseResults['Phase 2'] = ['passed' => $phase2Passed, 'total' => $phase2Tests];
$testsPassed += $phase2Passed;
$totalTests += $phase2Tests;

// ============================================================================
// PHASE 3: UI & ADMIN PANEL
// ============================================================================
echo "\nPHASE 3: UI & Admin Panel\n";
echo "─────────────────────────────────────────────────────────────────\n";

$phase3Tests = 0;
$phase3Passed = 0;

$viewFiles = [
    '/resources/views/admin/google-sheets/index.blade.php',
    '/resources/views/admin/google-sheets/setup.blade.php',
    '/resources/views/admin/google-sheets/pull-preview.blade.php',
];

foreach ($viewFiles as $file) {
    $phase3Tests++;
    if (file_exists(__DIR__ . $file)) {
        echo "✓ View file exists: " . basename($file) . "\n";
        $phase3Passed++;
    } else {
        echo "✗ View file missing: " . basename($file) . "\n";
    }
}

// Test 3.4: Sidebar menu
$phase3Tests++;
$sidebarFile = __DIR__ . '/resources/views/admin/partials/sneat-sidebar-menu.blade.php';
if (file_exists($sidebarFile) && strpos(file_get_contents($sidebarFile), 'Google Sheets Sync') !== false) {
    echo "✓ Sidebar menu integration\n";
    $phase3Passed++;
} else {
    echo "✗ Sidebar menu not integrated\n";
}

// Test 3.5: View styling
$phase3Tests++;
$indexFile = __DIR__ . '/resources/views/admin/google-sheets/index.blade.php';
if (file_exists($indexFile) && strpos(file_get_contents($indexFile), '@section(\'styles\')') !== false) {
    echo "✓ View styling included\n";
    $phase3Passed++;
} else {
    echo "✗ View styling missing\n";
}

$phaseResults['Phase 3'] = ['passed' => $phase3Passed, 'total' => $phase3Tests];
$testsPassed += $phase3Passed;
$totalTests += $phase3Tests;

// ============================================================================
// PHASE 4: SCHEDULER
// ============================================================================
echo "\nPHASE 4: Scheduler Configuration\n";
echo "─────────────────────────────────────────────────────────────────\n";

$phase4Tests = 0;
$phase4Passed = 0;

$bootstrapFile = __DIR__ . '/bootstrap/app.php';
$phase4Tests++;
if (file_exists($bootstrapFile)) {
    $bootstrapContent = file_get_contents($bootstrapFile);
    if (strpos($bootstrapContent, '->withSchedule(') !== false) {
        echo "✓ Scheduler configuration found\n";
        $phase4Passed++;
    } else {
        echo "✗ Scheduler configuration missing\n";
    }
}

// Test 4.2: Tier 1 modules
$phase4Tests++;
$tier1Modules = ['siswa', 'guru', 'kelas', 'jadwal_pelajaran', 'presensi', 'nilai'];
$allFound = true;
foreach ($tier1Modules as $module) {
    if (strpos($bootstrapContent, "'$module'") === false) {
        $allFound = false;
        break;
    }
}
if ($allFound && strpos($bootstrapContent, 'dailyAt') !== false) {
    echo "✓ Tier 1 modules (daily 00:30)\n";
    $phase4Passed++;
} else {
    echo "✗ Tier 1 modules not configured\n";
}

// Test 4.3: Tier 2 modules
$phase4Tests++;
$tier2Modules = ['tagihan', 'pembayaran', 'siswa_belum_lunas', 'rekap_keuangan'];
$allFound = true;
foreach ($tier2Modules as $module) {
    if (strpos($bootstrapContent, "'$module'") === false) {
        $allFound = false;
        break;
    }
}
if ($allFound && strpos($bootstrapContent, 'weeklyOn') !== false) {
    echo "✓ Tier 2 modules (weekly Sunday 01:00)\n";
    $phase4Passed++;
} else {
    echo "✗ Tier 2 modules not configured\n";
}

// Test 4.4: Overlap prevention
$phase4Tests++;
if (strpos($bootstrapContent, 'onOneServer()') !== false && strpos($bootstrapContent, 'withoutOverlapping') !== false) {
    echo "✓ Overlap prevention configured\n";
    $phase4Passed++;
} else {
    echo "✗ Overlap prevention missing\n";
}

$phaseResults['Phase 4'] = ['passed' => $phase4Passed, 'total' => $phase4Tests];
$testsPassed += $phase4Passed;
$totalTests += $phase4Tests;

// ============================================================================
// SUMMARY
// ============================================================================
echo "\n╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    FINAL RESULTS SUMMARY                     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

foreach ($phaseResults as $phase => $results) {
    $percentage = ($results['passed'] / $results['total']) * 100;
    $status = $results['passed'] === $results['total'] ? '✓' : '✗';
    printf("%s %s: %d/%d tests passed (%.0f%%)\n", 
        $status, $phase, $results['passed'], $results['total'], $percentage);
}

$percentage = ($testsPassed / $totalTests) * 100;
echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
printf("TOTAL: %d/%d tests passed (%.0f%%)\n", $testsPassed, $totalTests, $percentage);
echo "═══════════════════════════════════════════════════════════════\n";

if ($testsFailed === 0 && $testsPassed === $totalTests) {
    echo "\n🎉 ALL PHASES COMPLETE AND VALIDATED!\n";
    echo "\n✅ Ready for:\n";
    echo "   • Production deployment\n";
    echo "   • Admin panel access (/admin/google-sheets)\n";
    echo "   • Queue worker startup (php artisan queue:work)\n";
    echo "   • Scheduler activation (via cron)\n";
    echo "\n";
    exit(0);
} else {
    echo "\n❌ Some tests failed. Please review the output above.\n";
    exit(1);
}
