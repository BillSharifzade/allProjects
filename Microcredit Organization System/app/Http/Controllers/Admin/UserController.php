<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\User;
use App\Models\UserCashbox;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        Cashbox::where('id', $request->get('cashboxId'))->firstOrFail();

        return view('admin.user.index', [

        ]);
    }
}
