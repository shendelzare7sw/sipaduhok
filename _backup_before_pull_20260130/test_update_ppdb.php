<?php

use App\Models\LandingPageSection;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$section = LandingPageSection::where('section_key', 'biaya_paud')->first();

if (!$section) {
    echo "Section not found\n";
    exit;
}

$content = $section->content;
$items = $content['items'] ?? [];

if (empty($items)) {
    echo "Items empty\n";
    exit;
}

// Change price of first item
echo "Old Price: " . $items[0]['price'] . "\n";
$items[0]['price'] = "Rp 999.999";
$content['items'] = $items;

$section->content = $content;
$section->save();

// Re-fetch to verify
$section = LandingPageSection::where('section_key', 'biaya_paud')->first();
$newPrice = $section->content['items'][0]['price'];

echo "New Price in DB: " . $newPrice . "\n";

if ($newPrice === "Rp 999.999") {
    echo "SUCCESS: DB Updated.\n";
} else {
    echo "FAIL: DB did not update.\n";
}
