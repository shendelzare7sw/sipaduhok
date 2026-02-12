<?php

use App\Models\LandingPage;
use App\Models\LandingPageSection;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$section = LandingPageSection::where('section_key', 'biaya_paud')->first();

if (!$section) {
    echo "Section biaya_paud NOT FOUND\n";
    exit;
}

echo "Section ID: " . $section->id . "\n";
echo "Type: " . $section->type . "\n";
echo "Content:\n";
print_r($section->content);
