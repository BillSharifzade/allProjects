<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserStoreRequest;
use App\Http\Requests\AdminUserUpdateRequest;
use App\Models\Cashbox;
use App\Models\CashboxUser;
use App\Models\CashierShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CashboxUserController extends Controller
{
    public function index(Request $request) {
        $cashboxUsers =  CashboxUser::with('user')
            ->with('cashbox');

        if($request->get('cashboxId') > 0) {
            Cashbox::where('id', $request->get('cashboxId'))->firstOrFail();
            $cashboxUsers->where('cashbox_id', $request->get('cashboxId'));
        }

        $list = $cashboxUsers->get();

        // Build open shift map for displayed cashier-cashbox pairs
        $userIds = $list->pluck('user_id')->unique();
        $cashboxIds = $list->pluck('cashbox_id')->unique();

        $openShifts = CashierShift::where('company_id', Auth::user()->company_id)
            ->where('closed_at', 0)
            ->whereIn('user_id', $userIds)
            ->whereIn('cashbox_id', $cashboxIds)
            ->get();

        $shiftOpenMap = [];
        foreach ($openShifts as $shift) {
            $shiftOpenMap[$shift->user_id . ':' . $shift->cashbox_id] = true;
        }

        return view('admin.cashboxUser.index', [
            'cashboxUsers' => $list,
            'shiftOpenMap' => $shiftOpenMap,
        ]);
    }

    public function delete(Request $request, CashboxUser $cashboxUser) {
        $cashboxUser->delete();
        return redirect()->route('cashbox-users');
    }

    public function create() {
        $cashboxes = [];

        foreach(Cashbox::get() as $cashbox) {
            $cashboxes[$cashbox->id] = $cashbox->name;
        }

        return view('admin.cashboxUser.create', [
            'cashboxes' => $cashboxes,
        ]);
    }

    public function store(AdminUserStoreRequest $request) {
        $validated = $request->validated();

        DB::beginTransaction();

        $user = new User();
        $user->company_id = Auth::user()->company_id;
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone = $validated['phone'];
        $user->login = $validated['login'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];

        if($user->save() === false){
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось создать кассира'
            ])->withInput();
        }

        $cashboxUser = new CashboxUser();

        $cashboxUser->company_id = Auth::user()->company_id;
        $cashboxUser->cashbox_id = $validated['cashbox_id'];
        $cashboxUser->user_id = $user->id;
        $cashboxUser->user_license = $validated['user_license'];

        if($cashboxUser->save() === false){
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось создать кассира'
            ])->withInput();
        }

        DB::commit();

        return redirect()->route('cashbox-users');
    }

    public function edit(Request $request, CashboxUser $cashboxUser) {
        $cashboxes = [];

        foreach(Cashbox::get() as $cashbox) {
            $cashboxes[$cashbox->id] = $cashbox->name;
        }

        return view('admin.cashboxUser.edit', [
            'user' => User::where('id', $cashboxUser->user_id)->firstOrFail(),
            'cashboxes' => $cashboxes,
            'cashboxUser' => $cashboxUser,
        ]);
    }

    public function update(AdminUserUpdateRequest $request, CashboxUser $cashboxUser) {
        $validated = $request->validated();

        DB::beginTransaction();

        $user = User::where('id', $cashboxUser->user_id)->firstOrFail();

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone = $validated['phone'];
        $user->password = $validated['password'] != '' ? Hash::make($validated['password']) : $user->password;
        $user->role = $validated['role'];

        if($user->save() === false){
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось создать кассира'
            ])->withInput();
        }

        $cashboxUser->cashbox_id = $validated['cashbox_id'];
        $cashboxUser->user_license = $validated['user_license'];
        if($cashboxUser->save() === false){
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось создать кассира'
            ])->withInput();
        }

        DB::commit();

        return redirect()->route('cashbox-users');
    }
}
