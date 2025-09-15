<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashboxUser;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function create()
    {
        $currentUserId = Auth::user()->id;
        $targets = CashboxUser::with('user')
            ->with('cashbox')
            ->where('user_id', '<>', $currentUserId)
            ->get();

        return view('cashbox.transfer.create', [
            'targets' => $targets,
        ]);
    }

    private function currentShiftAndBalance(): array
    {
        $user = Auth::user();
        $cashboxId = $user->cashboxUser->cashbox_id;
        $shift = CashierShift::where('user_id', $user->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();

        if(!$shift) {
            return [null, 0.0];
        }

        $delta = CashboxLedger::where('shift_id', $shift->id)
            ->select(DB::raw('COALESCE(SUM(direction * amount),0) as delta'))
            ->value('delta');
        $balance = (float)$shift->opening_balance + (float)$delta;
        return [$shift, $balance];
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

        if ($target->user_id == Auth::id()) {
            return redirect()->back()->withErrors(['Нельзя перевести самому себе'])->withInput();
        }

        // Sender shift and balance
        [$senderShift, $senderBalance] = $this->currentShiftAndBalance();
        if(!$senderShift) {
            return redirect()->back()->withErrors(['Нет открытой смены'])->withInput();
        }

        $amount = (float)$validated['amount'];
        if($amount > $senderBalance) {
            return redirect()->back()->withErrors([
                'Недостаточно средств. Доступно: ' . number_format($senderBalance, 2, '.', ' ')
            ])->withInput();
        }

        // Recipient must have open shift
        $recipientShift = CashierShift::where('user_id', $target->user_id)
            ->where('cashbox_id', $target->cashbox_id)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();

        if(!$recipientShift) {
            return redirect()->back()->withErrors(['У получателя нет открытой смены'])->withInput();
        }

        $eventId = uniqid('tf_', true);

        // Write sender outflow (idempotent by unique event key)
        CashboxLedger::create([
            'company_id' => Auth::user()->company_id,
            'cashbox_id' => Auth::user()->cashboxUser->cashbox_id,
            'user_id' => Auth::id(),
            'shift_id' => $senderShift->id,
            'event_type' => 'transfer_out',
            'event_id' => $eventId,
            'direction' => -1,
            'amount' => $amount,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);

        // Write recipient inflow (idempotent by same event key)
        CashboxLedger::create([
            'company_id' => Auth::user()->company_id,
            'cashbox_id' => $target->cashbox_id,
            'user_id' => $target->user_id,
            'shift_id' => $recipientShift->id,
            'event_type' => 'transfer_in',
            'event_id' => $eventId,
            'direction' => +1,
            'amount' => $amount,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);

        return redirect()->route('loans')->with('message', 'Перевод выполнен');
    }
}


