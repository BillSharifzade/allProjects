<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function index() {
        return view('admin.password.index');
    }

    public function store(Request $request) {

        $login = $request->input('login');
        $oldPassword = $request->input('old_password');
        $newPassword = $request->input('new_password');
        $passwordConfirm = $request->input('password_confirm');

        if($newPassword != $passwordConfirm || $newPassword == '') {
            return redirect()->back()->withErrors([
                'Новый пароль не совпадает с подтверждением пароля'
            ])->withInput();
        }

        $user = User::where('login', $login)->firstOrFail();

        if(!Hash::check($oldPassword, $user->password)) {
            return redirect()->back()->withErrors([
                'Неверный текущий пароль'
            ])->withInput();
        }

        $user->password = Hash::make($newPassword);
        if($user->save() == false) {
            return redirect()->back()->withErrors([
                'Не удалось сохранить новый пароль'
            ])->withInput();
        }

        return redirect()->back()->with('message', 'Пароль ' . $user->login . ' изменен');
    }
}
