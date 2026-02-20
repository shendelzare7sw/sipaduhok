<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$page = \App\Models\LandingPage::with('sections')->where('slug', 'fasilitas')->first();
if (!$page) {
    echo "Page not found\n";
    exit;
}

foreach ($page->sections as $section) {
    echo "Section: " . $section->section_key . "\n";
    $content = $section->content;
    if (isset($content['items']) && count($content['items']) > 0) {
        print_r(array_keys($content['items'][0]));
    } elseif (is_array($content) && count($content) > 0 && isset($content[0])) {
         print_r(array_keys($content[0]));
    }
    echo "-------------------\n";
}
