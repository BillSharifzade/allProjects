<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(): \Illuminate\Http\RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('signin');
        }

        if(Auth::user()->isCashier() || Auth::user()->isCashierAudit()) {
            return redirect()->route('loans');
        } else if(Auth::user()->isAdmin()) {
            return redirect()->route('admin-loans');
        } else if(method_exists(Auth::user(), 'isIncassator') && Auth::user()->isIncassator()) {
            return redirect()->route('inc-todeliver');
        } else {
            return redirect()->route('reporter-loans');
        }
    }
}
