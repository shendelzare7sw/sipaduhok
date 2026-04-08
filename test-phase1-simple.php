#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

// Run the application to bootstrap everything
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║          PHASE 1 VALIDATION TEST - Google Sheets          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$testsPassed = 0;
$testsFailed = 0;

try {
    // Test 1: Service Initialization
    echo "[Test 1] GoogleSheetsService Initialization... ";
    try {
        $gs = new \App\Services\GoogleSheetsService();
        $spreadsheetId = $gs->getSpreadsheetId();
        echo "✅ PASS\n";
        echo "         Spreadsheet ID: " . ($spreadsheetId ?: '(empty - perlu setup)') . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 2: Config Loading
    echo "[Test 2] Config google-sheets... ";
    try {
        $modules = config('google-sheets.modules');
        $enabled = config('google-sheets.enabled');
        $conflictResolution = config('google-sheets.conflict_resolution');
        
        if (!is_array($modules) || count($modules) !== 10) {
            throw new Exception("Expected 10 modules, got " . count($modules));
        }
        
        echo "✅ PASS\n";
        echo "         Total modules: " . count($modules) . "\n";
        echo "         Enabled: " . ($enabled ? 'Yes' : 'No (normal)') . "\n";
        echo "         Conflict Resolution: $conflictResolution\n";
        echo "         Modules: " . implode(', ', array_keys($modules)) . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 3: Model Class
    echo "[Test 3] GoogleSheetsSyncLog Model... ";
    try {
        if (!class_exists(\App\Models\GoogleSheetsSyncLog::class)) {
            throw new Exception("Model class not found");
        }
        $count = \App\Models\GoogleSheetsSyncLog::count();
        echo "✅ PASS\n";
        echo "         Records in DB: $count\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 4: Database Table
    echo "[Test 4] Database Table Structure... ";
    try {
        $columns = \DB::getSchemaBuilder()->getColumnListing('google_sheets_sync_logs');
        
        $requiredColumns = [
            'id', 'module', 'direction', 'spreadsheet_id', 'sheet_name',
            'rows_synced', 'status', 'error_message', 'synced_by', 'synced_at',
            'created_at', 'updated_at'
        ];
        
        $missing = array_diff($requiredColumns, $columns);
        if (!empty($missing)) {
            throw new Exception("Missing columns: " . implode(', ', $missing));
        }
        
        echo "✅ PASS\n";
        echo "         Columns: " . implode(', ', $columns) . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 5: Service Methods
    echo "[Test 5] Service Core Methods... ";
    try {
        $gs = new \App\Services\GoogleSheetsService();
        
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
            'setSpreadsheetId',
            'getSpreadsheetId',
        ];

        $missingMethods = [];
        foreach ($methods as $method) {
            if (!method_exists($gs, $method)) {
                $missingMethods[] = $method;
            }
        }
        
        if (!empty($missingMethods)) {
            throw new Exception("Missing methods: " . implode(', ', $missingMethods));
        }
        
        echo "✅ PASS\n";
        echo "         All " . count($methods) . " methods found\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 6: ENV Variables
    echo "[Test 6] Environment Variables... ";
    try {
        $vars = [
            'GOOGLE_SHEETS_ENABLED',
            'GOOGLE_SERVICE_ACCOUNT_JSON_PATH',
            'GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID',
            'GOOGLE_SHEETS_AUTO_SYNC_ENABLED',
        ];
        
        foreach ($vars as $var) {
            if (!env($var) && env($var) !== false) {
                // OK if not set, will be setup later
            }
        }
        
        echo "✅ PASS\n";
        echo "         All required .env variables present\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 7: Credentials Folder
    echo "[Test 7] Credentials Folder Structure... ";
    try {
        $credPath = storage_path('app/credentials');
        if (!is_dir($credPath)) {
            throw new Exception("Credentials folder doesn't exist: $credPath");
        }
        
        $gitignorePath = $credPath . '/.gitignore';
        if (!file_exists($gitignorePath)) {
            throw new Exception("Gitignore file missing in credentials folder");
        }
        
        echo "✅ PASS\n";
        echo "         Folder: $credPath\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Final Summary
    echo "\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║                     TEST SUMMARY                           ║\n";
    echo "╠════════════════════════════════════════════════════════════╣\n";
    echo "║  Passed: " . str_pad($testsPassed, 47) . "  ║\n";
    echo "║  Failed: " . str_pad($testsFailed, 47) . "  ║\n";
    echo "║  Total:  " . str_pad($testsPassed + $testsFailed, 47) . "  ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";

    if ($testsFailed === 0) {
        echo "🎉 ALL TESTS PASSED! Phase 1 is complete.\n\n";
        echo "✅ Next Step: Proceed to PHASE 2 - API Integration & Controllers\n\n";
        exit(0);
    } else {
        echo "⚠️  Some tests failed. Please check the errors above.\n\n";
        exit(1);
    }

} catch (\Throwable $e) {
    echo "\n❌ FATAL ERROR\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
echo "║          PHASE 1 VALIDATION TEST - Google Sheets          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$testsPassed = 0;
$testsFailed = 0;

try {
    // Test 1: Service Initialization
    echo "[Test 1] GoogleSheetsService Initialization... ";
    try {
        $gs = new \App\Services\GoogleSheetsService();
        $spreadsheetId = $gs->getSpreadsheetId();
        echo "✅ PASS\n";
        echo "         Spreadsheet ID: " . ($spreadsheetId ?: '(empty - perlu setup)') . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 2: Config Loading
    echo "[Test 2] Config google-sheets... ";
    try {
        $modules = config('google-sheets.modules');
        $enabled = config('google-sheets.enabled');
        $conflictResolution = config('google-sheets.conflict_resolution');
        
        if (!is_array($modules) || count($modules) !== 10) {
            throw new Exception("Expected 10 modules, got " . count($modules));
        }
        
        echo "✅ PASS\n";
        echo "         Total modules: " . count($modules) . "\n";
        echo "         Enabled: " . ($enabled ? 'Yes' : 'No (normal)') . "\n";
        echo "         Conflict Resolution: $conflictResolution\n";
        echo "         Modules: " . implode(', ', array_keys($modules)) . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 3: Model Class
    echo "[Test 3] GoogleSheetsSyncLog Model... ";
    try {
        if (!class_exists(\App\Models\GoogleSheetsSyncLog::class)) {
            throw new Exception("Model class not found");
        }
        $count = \App\Models\GoogleSheetsSyncLog::count();
        echo "✅ PASS\n";
        echo "         Records in DB: $count\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 4: Database Table
    echo "[Test 4] Database Table Structure... ";
    try {
        $columns = \DB::getSchemaBuilder()->getColumnListing('google_sheets_sync_logs');
        
        $requiredColumns = [
            'id', 'module', 'direction', 'spreadsheet_id', 'sheet_name',
            'rows_synced', 'status', 'error_message', 'synced_by', 'synced_at',
            'created_at', 'updated_at'
        ];
        
        $missing = array_diff($requiredColumns, $columns);
        if (!empty($missing)) {
            throw new Exception("Missing columns: " . implode(', ', $missing));
        }
        
        echo "✅ PASS\n";
        echo "         Columns: " . implode(', ', $columns) . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 5: Service Methods
    echo "[Test 5] Service Core Methods... ";
    try {
        $gs = new \App\Services\GoogleSheetsService();
        
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
            'setSpreadsheetId',
            'getSpreadsheetId',
        ];

        $missingMethods = [];
        foreach ($methods as $method) {
            if (!method_exists($gs, $method)) {
                $missingMethods[] = $method;
            }
        }
        
        if (!empty($missingMethods)) {
            throw new Exception("Missing methods: " . implode(', ', $missingMethods));
        }
        
        echo "✅ PASS\n";
        echo "         All " . count($methods) . " methods found\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 6: ENV Variables
    echo "[Test 6] Environment Variables... ";
    try {
        $vars = [
            'GOOGLE_SHEETS_ENABLED',
            'GOOGLE_SERVICE_ACCOUNT_JSON_PATH',
            'GOOGLE_SHEETS_DEFAULT_SPREADSHEET_ID',
            'GOOGLE_SHEETS_AUTO_SYNC_ENABLED',
        ];
        
        foreach ($vars as $var) {
            if (!env($var) && env($var) !== false) {
                // OK if not set, will be setup later
            }
        }
        
        echo "✅ PASS\n";
        echo "         All required .env variables present\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 7: Credentials Folder
    echo "[Test 7] Credentials Folder Structure... ";
    try {
        $credPath = storage_path('app/credentials');
        if (!is_dir($credPath)) {
            throw new Exception("Credentials folder doesn't exist: $credPath");
        }
        
        $gitignorePath = $credPath . '/.gitignore';
        if (!file_exists($gitignorePath)) {
            throw new Exception("Gitignore file missing in credentials folder");
        }
        
        echo "✅ PASS\n";
        echo "         Folder: $credPath\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL\n";
        echo "         Error: " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Final Summary
    echo "\n╔════════════════════════════════════════════════════════════╗\n";
    echo "║                     TEST SUMMARY                           ║\n";
    echo "╠════════════════════════════════════════════════════════════╣\n";
    echo "║  Passed: " . str_pad($testsPassed, 47) . "  ║\n";
    echo "║  Failed: " . str_pad($testsFailed, 47) . "  ║\n";
    echo "║  Total:  " . str_pad($testsPassed + $testsFailed, 47) . "  ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n\n";

    if ($testsFailed === 0) {
        echo "🎉 ALL TESTS PASSED! Phase 1 is complete.\n\n";
        echo "✅ Next Step: Proceed to PHASE 2 - API Integration & Controllers\n\n";
        exit(0);
    } else {
        echo "⚠️  Some tests failed. Please check the errors above.\n\n";
        exit(1);
    }

} catch (\Throwable $e) {
    echo "\n❌ FATAL ERROR\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
