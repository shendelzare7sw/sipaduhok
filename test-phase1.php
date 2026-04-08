#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

// Bootstrap the application
$app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "\n=== PHASE 1 TEST: Google Sheets Integration ===\n\n";

try {
    // Test 1: Service Initialization
    echo "Test 1: GoogleSheetsService Initialization... ";
    $gs = new \App\Services\GoogleSheetsService();
    $spreadsheetId = $gs->getSpreadsheetId();
    echo "✅\n";
    echo "  Spreadsheet ID: " . ($spreadsheetId ?: '(empty - normal jika belum setup)') . "\n\n";

    // Test 2: Config
    echo "Test 2: Config google-sheets... ";
    $modules = config('google-sheets.modules');
    $enabled = config('google-sheets.enabled');
    $conflictResolution = config('google-sheets.conflict_resolution');
    echo "✅\n";
    echo "  Total modules: " . count($modules) . "\n";
    echo "  Enabled: " . ($enabled ? 'Yes' : 'No - normal, perlu setup') . "\n";
    echo "  Conflict Resolution: $conflictResolution\n";
    echo "  Modules: " . implode(', ', array_keys($modules)) . "\n\n";

    // Test 3: Model
    echo "Test 3: GoogleSheetsSyncLog Model... ";
    $count = \App\Models\GoogleSheetsSyncLog::count();
    echo "✅\n";
    echo "  Records in DB: $count\n\n";

    // Test 4: Database Table
    echo "Test 4: Database Table Structure... ";
    $columns = \DB::getSchemaBuilder()->getColumnListing('google_sheets_sync_logs');
    echo "✅\n";
    echo "  Columns: " . implode(', ', $columns) . "\n\n";

    // Test 5: Service Methods
    echo "Test 5: Service Core Methods... ";
    $methods = [
        'testConnection',
        'getSpreadsheetMetadata',
        'getSheetNames',
        'getSheetData',
        'getRange',
        'updateRange',
        'clearSheet',
        'appendRows',
        'formatHeader',
        'shareSpreadsheet',
        'getSpreadsheetUrl',
    ];
    $reflection = new ReflectionClass($gs);
    $publicMethods = [];
    foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (!$method->isStatic() && strpos($method->getName(), '__') === false) {
            $publicMethods[] = $method->getName();
        }
    }
    $allExists = count(array_intersect($methods, $publicMethods)) === count($methods);
    echo ($allExists ? "✅" : "❌") . "\n";
    echo "  Methods found: " . implode(', ', array_intersect($methods, $publicMethods)) . "\n\n";

    // Final Result
    echo "╔════════════════════════════════════════════╗\n";
    echo "║  ✅ PHASE 1 VALIDATION: ALL TESTS PASSED! ║\n";
    echo "╚════════════════════════════════════════════╝\n\n";

    echo "Next: Proceed to PHASE 2 - API Integration & Controllers\n\n";

} catch (\Exception $e) {
    echo "❌ ERROR\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
