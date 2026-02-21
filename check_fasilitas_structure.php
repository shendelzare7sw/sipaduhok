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
    echo "Section: " . $section->section_key . " (" . $section->type . ")\n";
    
    $content = $section->content;
    if (is_array($content)) {
        if (isset($content['items'])) {
             echo "Has 'items' key. Count: " . count($content['items']) . "\n";
             if (!empty($content['items'])) {
                 echo "First item keys: " . implode(', ', array_keys($content['items'][0])) . "\n";
             }
        } elseif (!empty($content) && array_key_exists(0, $content)) {
             echo "Is direct array. Count: " . count($content) . "\n";
             echo "First item: \n";
             print_r($content[0]);
        } else {
            echo "Is associative array (fields).\n";
        }
    }
    
    echo "\n-------------------\n";
}
