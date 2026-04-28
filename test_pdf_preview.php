<?php
/**
 * Test PDF Preview - Debug file paths and storage
 */

require 'vendor/autoload.php';
require 'bootstrap/app.php';

use Illuminate\Support\Facades\Storage;

// Get sample materi with PDF
$materi = \App\Models\Materi::where('tipe_file', 'pdf')
    ->whereNotNull('file_materi')
    ->first();

if (!$materi) {
    echo "❌ No PDF materi found in database\n";
    exit;
}

echo "=== PDF Preview Debug ===\n\n";
echo "📄 Materi: {$materi->judul_materi}\n";
echo "📁 File Path: {$materi->file_materi}\n\n";

// Test 1: Check if file exists in public disk
$disk = Storage::disk('public');
$exists = $disk->exists($materi->file_materi);
echo "📍 Storage::disk('public')->exists(): " . ($exists ? "✅ YES" : "❌ NO") . "\n";

// Test 2: Check full path
$fullPath = storage_path('app/public/' . $materi->file_materi);
echo "📍 Full path: $fullPath\n";
echo "📍 file_exists(): " . (file_exists($fullPath) ? "✅ YES" : "❌ NO") . "\n";
echo "📍 is_readable(): " . (is_readable($fullPath) ? "✅ YES" : "❌ NO") . "\n";

// Test 3: Check file size and type
if (file_exists($fullPath)) {
    $size = filesize($fullPath);
    $type = mime_content_type($fullPath);
    echo "📍 File size: " . ($size / 1024 / 1024) . " MB\n";
    echo "📍 MIME type: $type\n";
}

// Test 4: Generate preview URL
$previewUrl = route('storage.preview', ['path' => $materi->file_materi]);
echo "\n📍 Preview URL: $previewUrl\n";

// Test 5: Try to access the file
echo "\n=== Testing File Access ===\n";
try {
    $response = \Illuminate\Support\Facades\Http::get($previewUrl);
    if ($response->successful()) {
        echo "✅ Preview URL accessible\n";
        echo "📍 Status: {$response->status()}\n";
        echo "📍 Content-Type: {$response->header('Content-Type')}\n";
        echo "📍 Content-Length: {$response->header('Content-Length')}\n";
        echo "📍 Content-Disposition: {$response->header('Content-Disposition')}\n";
    } else {
        echo "❌ Preview URL error: {$response->status()}\n";
        echo "📍 Body: {$response->body()}\n";
    }
} catch (Exception $e) {
    echo "❌ Error accessing preview URL: {$e->getMessage()}\n";
}

echo "\n";
