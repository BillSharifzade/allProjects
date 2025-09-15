<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashierShift;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
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

    public function create()
    {
        $categories = config('expenses.categories');
        [$shift, $balance] = $this->currentShiftAndBalance();
        return view('cashbox.expense.create', [
            'categories' => $categories,
            'balance' => $shift ? $balance : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ], [
            'category.required' => 'Выберите категорию',
            'amount.required' => 'Укажите сумму',
            'amount.gt' => 'Сумма должна быть больше 0',
        ]);

        [$shift, $balance] = $this->currentShiftAndBalance();
        if(!$shift) {
            return redirect()->back()->withErrors(['Нет открытой смены'])->withInput();
        }
        $amount = (float)$validated['amount'];
        if($amount > $balance) {
            return redirect()->back()->withErrors(['Недостаточно средств. Доступно: ' . number_format($balance, 2, '.', ' ')])->withInput();
        }

        $expense = new Expense();
        $expense->company_id = Auth::user()->company_id;
        $expense->cashbox_id = Auth::user()->cashboxUser->cashbox_id;
        $expense->user_id = Auth::id();
        $expense->shift_id = $shift->id;
        $expense->category = $validated['category'];
        $expense->description = $validated['description'] ?? '';
        $expense->amount = $amount;
        $expense->occurred_at = time();
        $expense->created_at = time();
        $expense->save();

        CashboxLedger::create([
            'company_id' => Auth::user()->company_id,
            'cashbox_id' => Auth::user()->cashboxUser->cashbox_id,
            'user_id' => Auth::id(),
            'shift_id' => $shift->id,
            'event_type' => 'expense',
            'event_id' => (string)$expense->id,
            'direction' => -1,
            'amount' => $amount,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);

        return redirect()->route('cashier-expenses');
    }

    public function index(Request $request)
    {
        // Today only for cashier
        $todayStart = strtotime(date('Y-m-d'));
        $todayEnd = $todayStart + 86399;
        $items = Expense::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->orderBy('id', 'desc')
            ->paginate(50);

        return view('cashbox.expense.index', [
            'items' => $items,
        ]);
    }
}


