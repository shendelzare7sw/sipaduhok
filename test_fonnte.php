<?php

/**
 * Fonnte API Debug Script
 *
 * Copy file ini ke: c:\laragon\www\sipaduhok\test_fonnte.php
 *
 * Jalankan: php test_fonnte.php
 * atau: http://localhost/sipaduhok/test_fonnte.php (jika akses via browser)
 */

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

echo "=== FONNTE API DEBUG TEST ===\n\n";

// 1. Test nomor format
echo "1. Testing Nomor Format Conversion\n";
echo "   ============================\n";

$testNumbers = [
    '082113100791',
    '08-2113-100791',
    '+6282113100791',
    '6282113100791',
    '0 821 1310 0791',
];

foreach ($testNumbers as $num) {
    $clean = preg_replace('/[^0-9]/', '', $num);
    if (str_starts_with($clean, '0')) {
        $formatted = '62' . substr($clean, 1);
    } else {
        $formatted = $clean;
    }
    echo "   Input: '{$num}' → Clean: '{$clean}' → Formatted: '{$formatted}'\n";
}

echo "\n";

// 2. Check Environment Settings
echo "2. Checking Environment Settings\n";
echo "   ==============================\n";

$appEnv = env('APP_ENV', 'NOT SET');
$disableSSL = env('DISABLE_SSL_VERIFY', 'NOT SET');

echo "   APP_ENV: {$appEnv}\n";
echo "   DISABLE_SSL_VERIFY: {$disableSSL}\n\n";

// 3. Test API Connection
echo "3. Testing Fonnte API Connection\n";
echo "   ==============================\n";

$token = env('FONNTE_TOKEN', '');
if (empty($token)) {
    echo "   ❌ FONNTE_TOKEN is empty! Cannot test real API.\n";
    echo "   Please set FONNTE_TOKEN in .env file to test.\n";
} else {
    echo "   ✅ Token found: " . substr($token, 0, 10) . "...(hidden)\n\n";

    // Test with dummy data
    $testPhone = '6282113100791';
    $testMessage = 'Test message - ' . date('Y-m-d H:i:s');

    echo "   Sending test message to: {$testPhone}\n";
    echo "   Message: {$testMessage}\n";
    echo "   SSL Verification: DISABLED (verify: false)\n\n";

    try {
        // Build request WITH SSL disabled (this is the important part!)
        $http = Http::withHeaders([
            'Authorization' => $token
        ]);

        // IMPORTANT: Disable SSL verification for development
        $http = $http->withOptions(['verify' => false]);

        echo "   Attempting POST to https://api.fonnte.com/send...\n";

        $response = $http->post('https://api.fonnte.com/send', [
            'target' => $testPhone,
            'message' => $testMessage,
            'countryCode' => '62',
        ]);

        echo "   ✅ Request successful (no exception)\n\n";
        echo "   HTTP Status: " . $response->status() . "\n";
        echo "   Is Successful (2xx): " . ($response->successful() ? 'YES' : 'NO') . "\n";
        echo "   Response Body:\n";
        echo "   " . $response->body() . "\n\n";

        if ($response->successful()) {
            $data = $response->json();
            echo "   Response JSON:\n";
            foreach ($data as $key => $value) {
                echo "      {$key}: " . json_encode($value) . "\n";
            }

            echo "\n   Status Value: " . json_encode($data['status'] ?? null) . "\n";
            if (isset($data['reason'])) {
                echo "   Reason: " . $data['reason'] . "\n";
            }

            // Important: Check if message actually sent
            if ($data['status'] === true) {
                echo "\n   ✅ MESSAGE SENT SUCCESSFULLY!\n";
            } else {
                echo "\n   ⚠️ API returned status=false (Check reason above)\n";
                echo "   This usually means:\n";
                echo "      - Fonnte device is disconnected\n";
                echo "      - Phone number format issue\n";
                echo "      - Rate limit reached\n";
                echo "      - Invalid message format\n";
            }
        } else {
            echo "   ❌ Response not successful (non-2xx status)\n";
        }
    } catch (\Exception $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";
        echo "\n   This usually means:\n";
        echo "      - Network issue\n";
        echo "      - SSL certificate problem\n";
        echo "      - Invalid endpoint\n";
    }
}

echo "\n";
echo "=== END DEBUG TEST ===\n";
