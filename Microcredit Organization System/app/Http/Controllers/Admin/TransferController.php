<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashboxUser;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function create()
    {
        $targets = CashboxUser::with('user')
            ->with('cashbox')
            ->get();

        return view('admin.transfer.create', [
            'targets' => $targets,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cashbox_user_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ], [
            'cashbox_user_id.required' => 'Выберите получателя',
            'amount.required' => 'Укажите сумму',
            'amount.gt' => 'Сумма должна быть больше 0',
        ]);

        $target = CashboxUser::with('user')->with('cashbox')
            ->where('id', $validated['cashbox_user_id'])
            ->firstOrFail();

        // Admin injection has no balance restriction.
        $recipientShift = CashierShift::where('user_id', $target->user_id)
            ->where('cashbox_id', $target->cashbox_id)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();

        if(!$recipientShift) {
            return redirect()->back()->withErrors(['У получателя нет открытой смены'])->withInput();
        }

        $eventId = uniqid('adm_', true);

        // Admin funding (inflow to recipient) – must attach to open shift
        CashboxLedger::create([
            'company_id' => Auth::user()->company_id,
            'cashbox_id' => $target->cashbox_id,
            'user_id' => $target->user_id,
            'shift_id' => $recipientShift->id,
            'event_type' => 'admin_fund',
            'event_id' => $eventId,
            'direction' => +1,
            'amount' => (float)$validated['amount'],
            'occurred_at' => time(),
            'created_at' => time(),
        ]);

        return redirect()->route('cashbox-users')->with('message', 'Средства отправлены');
    }
}


