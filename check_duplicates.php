<?php

use App\Models\LandingPage;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pages = LandingPage::where('slug', 'ppdb')->get();

echo "Found " . $pages->count() . " pages with slug 'ppdb'.\n";
foreach ($pages as $page) {
    echo "ID: " . $page->id . " - Title: " . $page->title . "\n";
}
