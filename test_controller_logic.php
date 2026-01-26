<?php

use App\Models\LandingPageSection;
use Illuminate\Http\UploadedFile;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Load Section
$section = LandingPageSection::where('section_key', 'biaya_paud')->first();
echo "Original Price: " . $section->content['items'][0]['price'] . "\n";

// 2. Mock Input
// Simulate user changing price to 'Rp 888.888'
$mockInput = [
    'items' => $section->content['items'] // Copy existing items
];
$mockInput['items'][0]['price'] = 'Rp 888.888'; 

// 3. Run Controller Logic (Copy-Paste)
$content = $section->content;
$sectionInput = $mockInput;

// Determine Type logic
$isDirectArray = false;
if (is_array($content) && !empty($content) && isset($content[0])) {
     $isDirectArray = true;
} elseif (isset($content['items']) && is_array($content['items'])) {
     $isDirectArray = false;
} else {
    if ($section->section_key === 'stats') {
        $isDirectArray = true;
    }
}

if ($isDirectArray) {
    echo "Logic: Direct Array (Wrong for Biaya)\n";
} else {
    echo "Logic: Program Style (Correct for Biaya)\n";
    // PROGRAM Style: {items: [...], header: {...}}
    if (isset($sectionInput['items']) && is_array($sectionInput['items'])) {
        $newItems = [];
        foreach ($sectionInput['items'] as $index => $item) {
            $processedItem = $item;
            // (Image upload logic skipped for mock)
            $newItems[] = $processedItem;
        }

        $content['items'] = $newItems;

        // Handle Header fields if any
        if (isset($sectionInput['header'])) {
            $content['header'] = array_merge($content['header'] ?? [], $sectionInput['header']);
        }

        $section->content = $content;
    } else {
        echo "Logic Error: sectionInput['items'] not set?\n";
    }
}

// 4. Verify Result
echo "New Price in Object (Pre-Save): " . $section->content['items'][0]['price'] . "\n";

if ($section->content['items'][0]['price'] === 'Rp 888.888') {
    echo "SUCCESS: Logic works.\n";
} else {
    echo "FAIL: Logic failed.\n";
}
