<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Ensure migrations table exists
if (!DB::getSchemaBuilder()->hasTable('migrations')) {
    // create via Artisan
    $app->make(Illuminate\Contracts\Console\Kernel::class)->call('migrate:install');
}

$dir = __DIR__ . '/../database/migrations';
$files = glob($dir . '/*.php');
$added = 0; $skipped = 0;

foreach ($files as $file) {
    $name = basename($file, '.php');
    $exists = DB::table('migrations')->where('migration', $name)->exists();
    if ($exists) { $skipped++; continue; }
    DB::table('migrations')->insert(['migration' => $name, 'batch' => 1]);
    $added++;
}

echo "added={$added} skipped={$skipped}\n";


