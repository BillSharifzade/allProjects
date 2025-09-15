<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminCashboxStoreRequest;
use App\Http\Requests\AdminCashboxUpdateRequest;
use App\Models\Cashbox;
use App\Models\CashboxUser;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use const http\Client\Curl\AUTH_ANY;

class CashboxController extends Controller
{
    public function index() {
        return view('admin.cashbox.index', [
            'cashboxes' => Cashbox::get(),
        ]);
    }

    public function delete(Request $request, Cashbox $cashbox) {
        if(Loan::where('cashbox_id', $cashbox->id)->first()) {
            return redirect()->back()->withErrors([
                'На кассе имеются неудаленные кредиты'
            ])->withInput();
        }

        if(CashboxUser::where('cashbox_id', $cashbox->id)->first()) {
            return redirect()->back()->withErrors([
                'На кассе имеются неудаленные кассиры'
            ])->withInput();
        }

        $cashbox->delete();
        return redirect()->route('cashboxes');
    }

    public function create() {
        return view('admin.cashbox.create');
    }

    public function store(AdminCashboxStoreRequest $request) {
        $validated = $request->validated();

        if(count(Cashbox::get()) >= Auth::user()->company->cashbox_quota) {
            return redirect()->back()->withErrors([
                'Достигнуто максимальное количество доступных касс'
            ])->withInput();
        }

        $cashbox = new Cashbox();
        $cashbox->company_id = Auth::user()->company_id;
        $cashbox->name = $validated['name'];
        $cashbox->nickname = $validated['nickname'];
        $cashbox->address = $validated['address'];
        $cashbox->phone = $validated['phone'];
        $cashbox->license = $validated['license'];

        if($cashbox->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось создать кассу'
            ])->withInput();
        }

        return redirect()->route('cashboxes');
    }

    public function edit(Request $request, Cashbox $cashbox) {
        return view('admin.cashbox.edit', [
            'cashbox' => $cashbox
        ]);
    }

    public function update(AdminCashboxUpdateRequest $request, Cashbox $cashbox) {
        $validated = $request->validated();

        $cashbox->company_id = Auth::user()->company_id;
        $cashbox->name = $validated['name'];
        $cashbox->nickname = $validated['nickname'];
        $cashbox->address = $validated['address'];
        $cashbox->phone = $validated['phone'];
        $cashbox->license = $validated['license'];

        if($cashbox->save() === false) {
            return redirect()->back()->withErrors([
                'Не удалось создать кассу'
            ])->withInput();
        }

        return redirect()->route('cashboxes');
    }
}
