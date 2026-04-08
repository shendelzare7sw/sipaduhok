#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

// Bootstrap the application
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║        PHASE 2 VALIDATION TEST - API Integration           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$testsPassed = 0;
$testsFailed = 0;

try {
    // Test 1: Controller exists
    echo "[Test 1] GoogleSheetsController exists... ";
    try {
        if (!class_exists(\App\Http\Controllers\Admin\GoogleSheetsController::class)) {
            throw new Exception("Controller class not found");
        }
        echo "✅ PASS\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL - " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 2: Job classes exist
    echo "[Test 2] SyncModuleToSheet Job exists... ";
    try {
        if (!class_exists(\App\Jobs\SyncModuleToSheet::class)) {
            throw new Exception("Job class not found");
        }
        echo "✅ PASS\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL - " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 3: Routes registered
    echo "[Test 3] Google Sheets routes registered... ";
    try {
        $routes = [
            'admin.google-sheets.index',
            'admin.google-sheets.setup',
            'admin.google-sheets.save-credential',
            'admin.google-sheets.test-connection',
            'admin.google-sheets.push',
            'admin.google-sheets.pull-preview',
            'admin.google-sheets.pull',
            'admin.google-sheets.status',
            'admin.google-sheets.disconnect',
        ];

        $routeList = \Route::getRoutes();
        $registeredNames = array_map(function ($route) {
            return $route->getName();
        }, iterator_to_array($routeList));

        $missing = array_diff($routes, $registeredNames);
        if (!empty($missing)) {
            throw new Exception("Missing routes: " . implode(', ', $missing));
        }

        echo "✅ PASS\n";
        echo "         All " . count($routes) . " routes found\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL - " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 4: Controller methods exist
    echo "[Test 4] Controller methods... ";
    try {
        $reflection = new ReflectionClass(\App\Http\Controllers\Admin\GoogleSheetsController::class);
        $methods = [
            'index',
            'setup',
            'saveCredential',
            'testConnection',
            'push',
            'pullPreview',
            'pull',
            'status',
            'disconnect',
        ];

        $missingMethods = [];
        foreach ($methods as $method) {
            if (!$reflection->hasMethod($method)) {
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
        echo "❌ FAIL - " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 5: Job handle method
    echo "[Test 5] Job handle method... ";
    try {
        $reflection = new ReflectionClass(\App\Jobs\SyncModuleToSheet::class);
        if (!$reflection->hasMethod('handle')) {
            throw new Exception("handle() method not found");
        }
        echo "✅ PASS\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL - " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 6: Job implements ShouldQueue
    echo "[Test 6] Job implements ShouldQueue... ";
    try {
        $job = new \App\Jobs\SyncModuleToSheet('siswa', 'push');
        if (!($job instanceof \Illuminate\Contracts\Queue\ShouldQueue)) {
            throw new Exception("Job does not implement ShouldQueue");
        }
        echo "✅ PASS\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL - " . $e->getMessage() . "\n";
        $testsFailed++;
    }

    // Test 7: Config modules in sync job
    echo "[Test 7] Job can access modules config... ";
    try {
        $modules = config('google-sheets.modules');
        if (!is_array($modules) || count($modules) === 0) {
            throw new Exception("Modules config not available");
        }
        echo "✅ PASS\n";
        echo "         Modules available: " . count($modules) . "\n";
        $testsPassed++;
    } catch (\Exception $e) {
        echo "❌ FAIL - " . $e->getMessage() . "\n";
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
        echo "🎉 ALL TESTS PASSED! Phase 2 is complete.\n\n";
        echo "✅ Next Step: Proceed to PHASE 3 - UI & Admin Panel\n\n";
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
