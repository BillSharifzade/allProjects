<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

$login = $argv[1] ?? 'admin_test';
$pass = $argv[2] ?? 'Admin123!';

$user = User::where('login', $login)->first();
if (!$user) {
    $user = new User();
    $user->company_id = 1;
    $user->login = $login;
}

$user->first_name = 'Admin';
$user->last_name = 'Admin';
$user->phone = '000';
$user->role = 'admin';
$user->password = Hash::make($pass);

if (!$user->save()) {
    echo "error\n"; exit(1);
}

echo "created user_id={$user->id} login={$user->login} role={$user->role}\n";


