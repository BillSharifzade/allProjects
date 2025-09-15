<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserStoreRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class IncassatorController extends Controller
{
    public function index()
    {
        $items = User::where('company_id', Auth::user()->company_id)
            ->where('role', 'incassator')
            ->orderBy('id','desc')->paginate(50);
        return view('admin.incassator.index', [ 'items' => $items ]);
    }

    public function create()
    {
        return view('admin.incassator.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required','string','min:2'],
            'last_name' => ['required','string','min:2'],
            'phone' => ['nullable','string'],
            'login' => ['required','string','min:3','unique:users,login'],
            'password' => ['required','string','min:5'],
        ]);

        $u = new User();
        $u->company_id = Auth::user()->company_id;
        $u->first_name = $validated['first_name'];
        $u->last_name = $validated['last_name'];
        $u->phone = $validated['phone'] ?? '';
        $u->login = $validated['login'];
        $u->password = Hash::make($validated['password']);
        $u->role = 'incassator';
        $u->save();

        return redirect()->route('admin-incassators');
    }

    public function delete(User $user)
    {
        if($user->role !== 'incassator' || $user->company_id != Auth::user()->company_id) {
            abort(403);
        }
        $user->delete();
        return redirect()->route('admin-incassators');
    }

    public function safe(User $user)
    {
        if($user->role !== 'incassator' || $user->company_id != Auth::user()->company_id) {
            abort(403);
        }
        $items = \App\Models\IncassatorSafeLoan::where('incassator_id', $user->id)
            ->orderBy('id','desc')->paginate(50);
        return view('admin.incassator.safe', [ 'user' => $user, 'items' => $items ]);
    }
}


