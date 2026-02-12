<?php

use App\Models\LandingPage;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$page = LandingPage::where('slug', 'kontak')->first();

if (!$page) {
    echo "Page 'kontak' not found.\n";
    exit;
}

$section = $page->getSection('contact_info');

if (!$section) {
    echo "Section 'contact_info' not found.\n";
    exit;
}

file_put_contents('contact_dump.json', json_encode($section->content, JSON_PRETTY_PRINT));
echo "Dumped to contact_dump.json\n";
