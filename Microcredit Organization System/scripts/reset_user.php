<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\CashboxUser;
use App\Models\Cashbox;

$login = $argv[1] ?? '';
$newPass = $argv[2] ?? '123456';

if ($login === '') { echo "usage: php scripts/reset_user.php <login> [newPassword]\n"; exit(1); }

$user = User::withTrashed()->where('login', $login)->first();
if (!$user) { echo "user_not_found\n"; exit(1); }

// unblock and undelete
if (Schema::hasColumn('users', 'is_blocked')) {
    $user->is_blocked = 0;
}
$user->deleted_at = null;
$user->password = Hash::make($newPass);
$user->save();

// ensure cashier link
if ($user->isCashier() || $user->isCashierAudit()) {
    $link = CashboxUser::withTrashed()->where('user_id', $user->id)->first();
    if ($link && !is_null($link->deleted_at)) {
        $link->deleted_at = null;
        $link->save();
    }
    if (!$link) {
        $cashbox = Cashbox::first();
        if ($cashbox) {
            CashboxUser::create([
                'company_id' => $user->company_id,
                'cashbox_id' => $cashbox->id,
                'user_id' => $user->id,
                'user_license' => ''
            ]);
        }
    }
}

echo "reset_ok user_id={$user->id} role={$user->role}\n";


