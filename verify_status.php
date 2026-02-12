<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking for inconsistencies...\n";

// Check 1: Student Not Active, but User Active
$mismatches = \App\Models\Siswa::with('user')
    ->where('status', '!=', 'aktif')
    ->whereHas('user', function($q) {
        $q->where('is_active', true);
    })
    ->get();

if ($mismatches->count() > 0) {
    echo "FOUND MISMATCHES (Student Non-Aktif, User Active):\n";
    foreach ($mismatches as $s) {
        echo "- {$s->nama_lengkap} ({$s->status}) - User ID: {$s->user_id} - Active: {$s->user->is_active}\n";
    }
} else {
    echo "No mismatches found (Student Non-Aktif -> User Inactive is consistent).\n";
}

// Check 2: Latest 5 updated students
echo "\nLast 5 updated students:\n";
$latest = \App\Models\Siswa::with('user')->orderBy('updated_at', 'desc')->take(5)->get();
foreach ($latest as $s) {
    echo "- {$s->nama_lengkap} ({$s->status}) - User Active: " . ($s->user->is_active ? 'YES' : 'NO') . " - Updated: {$s->updated_at}\n";
}
