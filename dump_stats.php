<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$page = App\Models\LandingPage::where('slug', 'fasilitas')->first();
if (!$page) {
    echo "Fasilitas page not found\n";
    exit;
}

$section = $page->sections->where('section_key', 'stats')->first();
if (!$section) {
    echo "Stats section not found\n";
    echo "Available sections: " . implode(', ', $page->sections->pluck('section_key')->toArray()) . "\n";
    exit;
}

echo "Stats Content:\n";
print_r($section->content);
