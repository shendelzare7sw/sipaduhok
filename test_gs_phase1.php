<?php

// Test GoogleSheetsService
$gs = new \App\Services\GoogleSheetsService();
echo "Spreadsheet ID: " . $gs->getSpreadsheetId() . "\n";
echo "Service initialized successfully!\n";

// Test Config
$modules = config('google-sheets.modules');
echo "Total modules configured: " . count($modules) . "\n";
echo "Modules: " . implode(', ', array_keys($modules)) . "\n";

// Test Model
$count = \App\Models\GoogleSheetsSyncLog::count();
echo "GoogleSheetsSyncLog records: " . $count . "\n";

echo "\n✅ ALL TESTS PASSED!\n";
