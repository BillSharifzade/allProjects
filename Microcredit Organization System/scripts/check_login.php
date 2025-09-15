<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Models\User;

$login = $argv[1] ?? '';

echo "default=" . Config::get('database.default') . "\n";
echo "mysql.db=" . Config::get('database.connections.mysql.database') . "\n";

try {
    $count = User::count();
    echo "users_count=" . $count . "\n";
    if ($login !== '') {
        $exists = User::where('login', $login)->exists();
        echo "exists_login_{$login}=" . ($exists ? 'yes' : 'no') . "\n";
        if ($exists) {
            $u = User::where('login', $login)->first();
            $deleted = is_null($u->deleted_at) ? 'null' : (string)$u->deleted_at;
            echo "user_id={$u->id} role={$u->role} deleted_at={$deleted}\n";
        }
    }
} catch (Throwable $e) {
    echo "error=" . $e->getMessage() . "\n";
}