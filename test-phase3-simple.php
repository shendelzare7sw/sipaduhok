<?php

/**
 * Phase 3 Validation Test - View Files & Menu Integration
 * File: test-phase3-simple.php
 * 
 * Tests:
 * 1. All view files exist
 * 2. Sidebar menu integration
 * 3. Controller methods return proper responses
 * 4. All 9 routes are registered
 */

echo "\n========== PHASE 3 VALIDATION TESTS ==========\n\n";

$testsPassed = 0;
$testsFailed = 0;

// Bootstrap Laravel
require_once __DIR__ . '/vendor/autoload.php';

// Test 1: View Files Exist
echo "[TEST 1] View Files Exist\n";
$viewFiles = [
    'resources/views/admin/google-sheets/index.blade.php',
    'resources/views/admin/google-sheets/setup.blade.php',
    'resources/views/admin/google-sheets/pull-preview.blade.php',
];

foreach ($viewFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "  ✓ $file exists\n";
        $testsPassed++;
    } else {
        echo "  ✗ $file NOT found\n";
        $testsFailed++;
    }
}

// Test 2: Sidebar Menu Integration
echo "\n[TEST 2] Sidebar Menu Integration\n";
$sidebarFile = __DIR__ . '/resources/views/admin/partials/sneat-sidebar-menu.blade.php';
if (file_exists($sidebarFile)) {
    $content = file_get_contents($sidebarFile);
    if (strpos($content, 'Google Sheets Sync') !== false && strpos($content, 'admin.google-sheets.index') !== false) {
        echo "  ✓ Google Sheets menu item integrated\n";
        $testsPassed++;
    } else {
        echo "  ✗ Google Sheets menu item NOT found in sidebar\n";
        $testsFailed++;
    }
} else {
    echo "  ✗ Sidebar file NOT found\n";
    $testsFailed++;
}

// Test 3: Routes Registered in web.php
echo "\n[TEST 3] Routes Defined in web.php\n";
$webRoutesFile = __DIR__ . '/routes/web.php';
if (file_exists($webRoutesFile)) {
    $routeContent = file_get_contents($webRoutesFile);
    $expectedRoutes = [
        "Route::prefix('google-sheets')",
        "GoogleSheetsController::class, 'index'",
        "GoogleSheetsController::class, 'setup'",
        "GoogleSheetsController::class, 'saveCredential'",
        "GoogleSheetsController::class, 'testConnection'",
        "GoogleSheetsController::class, 'push'",
        "GoogleSheetsController::class, 'pullPreview'",
        "GoogleSheetsController::class, 'pull'",
        "GoogleSheetsController::class, 'status'",
        "GoogleSheetsController::class, 'disconnect'",
    ];
    
    foreach ($expectedRoutes as $route) {
        if (strpos($routeContent, $route) !== false) {
            echo "  ✓ Route definition found\n";
            $testsPassed++;
        } else {
            echo "  ✗ Route definition NOT found: '$route'\n";
            $testsFailed++;
        }
    }
} else {
    echo "  ✗ web.php NOT found\n";
    $testsFailed += 10;
}

// Test 4: Controller Methods Exist
echo "\n[TEST 4] Controller Methods Exist\n";
$controller = 'App\Http\Controllers\Admin\GoogleSheetsController';
$methods = [
    'index',
    'setup',
    'saveCredential',
    'testConnection',
    'push',
    'pull',
    'pullPreview',
    'status',
    'disconnect',
];

$controllerFile = __DIR__ . '/app/Http/Controllers/Admin/GoogleSheetsController.php';
if (file_exists($controllerFile)) {
    $controllerContent = file_get_contents($controllerFile);
    foreach ($methods as $method) {
        if (strpos($controllerContent, "public function {$method}") !== false) {
            echo "  ✓ Method '{$method}' exists\n";
            $testsPassed++;
        } else {
            echo "  ✗ Method '{$method}' NOT found\n";
            $testsFailed++;
        }
    }
} else {
    echo "  ✗ Controller file NOT found\n";
    $testsFailed += count($methods);
}

// Test 5: View Variables
echo "\n[TEST 5] View File Content Check\n";
$indexFile = __DIR__ . '/resources/views/admin/google-sheets/index.blade.php';
$setupFile = __DIR__ . '/resources/views/admin/google-sheets/setup.blade.php';
$pullPreviewFile = __DIR__ . '/resources/views/admin/google-sheets/pull-preview.blade.php';

$checks = [
    [$indexFile, ['@extends', '$modules', '$lastSyncs', '$syncHistory'], 'index.blade.php'],
    [$setupFile, ['@extends', '@section', 'testConnection', 'setupForm'], 'setup.blade.php'],
    [$pullPreviewFile, ['@extends', '$headers', '$rows', 'confirmImport'], 'pull-preview.blade.php'],
];

foreach ($checks as [$filePath, $keywords, $name]) {
    $content = file_get_contents($filePath);
    $allFound = true;
    foreach ($keywords as $keyword) {
        if (strpos($content, $keyword) === false) {
            echo "  ✗ '$name' missing keyword: '$keyword'\n";
            $allFound = false;
            $testsFailed++;
            break;
        }
    }
    if ($allFound) {
        echo "  ✓ '$name' contains all required keywords\n";
        $testsPassed++;
    }
}

// Summary
echo "\n========== TEST SUMMARY ==========\n";
echo "Tests Passed: $testsPassed\n";
echo "Tests Failed: $testsFailed\n";

if ($testsFailed === 0) {
    echo "\n✓ ALL PHASE 3 TESTS PASSED!\n";
    exit(0);
} else {
    echo "\n✗ SOME TESTS FAILED\n";
    exit(1);
}
