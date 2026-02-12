<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$p = App\Models\LandingPage::where('slug', 'ppdb')->first();
foreach ($p->sections as $s) {
    echo "=== " . $s->section_key . " ===\n";
    echo json_encode($s->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}
