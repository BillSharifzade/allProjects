<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashboxUser;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $items = CashboxUser::with('user')->with('cashbox')->orderBy('id','desc')->paginate(50);

        // attach latest shift (open or last closed)
        $latestShifts = CashierShift::whereIn('user_id', $items->pluck('user_id')->unique())
            ->whereIn('cashbox_id', $items->pluck('cashbox_id')->unique())
            ->orderBy('id','desc')->get()->groupBy(function($s){ return $s->user_id.':'.$s->cashbox_id; });

        return view('admin.daily_report.index', [
            'items' => $items,
            'latestShifts' => $latestShifts,
        ]);
    }

    public function download(Request $request, CashierShift $shift)
    {
        // Security: same company only
        if ((int)$shift->company_id !== (int)Auth::user()->company_id) { abort(403); }
        if ((int)$shift->closed_at <= 0) { abort(400, 'Смена не закрыта'); }

        // Reuse cashier variant logic with viewer-agnostic params
        $cashboxId = $shift->cashbox_id;

        $todayStart = (int)$shift->opened_at;
        $todayEnd = (int)$shift->closed_at;

        $cashbox = \App\Models\Cashbox::where('id', $cashboxId)->first();

        // Portfolio snapshot
        $portfolioInitial = \App\Models\Loan::where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)->sum('initial_sum');
        $portfolioLeft = \App\Models\Loan::where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)->sum('left_sum');

        // Payments during shift window (ledger-based)
        $paymentsInterest = (float)\App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->where('event_type', 'interest_payment')
            ->sum('amount');
        $paymentsPrincipal = (float)\App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->where('event_type', 'principal_payment')
            ->sum('amount');

        // Expenses
        $expenses = \App\Models\Expense::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->orderBy('id','asc')->get();
        $expensesTotal = (float)$expenses->sum('amount');
        $expenseReversal = (float)\App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->where('event_type', 'expense_reversal')
            ->sum('amount');

        // Transfers (in/out + admin_fund) in shift
        $transfers = \App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->whereIn('event_type', ['transfer_out','transfer_in','admin_fund'])
            ->orderBy('id','asc')->get();
        $transferIn = (float)$transfers->where('event_type', '!=', 'transfer_out')->sum('amount');
        $transferOut = (float)$transfers->where('event_type', 'transfer_out')->sum('amount');

        // Sold loans during shift
        $salesQ = \App\Models\LoanSale::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('sold_at', [$todayStart, $todayEnd]);
        $salesTotal = (float)(clone $salesQ)->sum('total_amount');
        $salesProfit = (float)(clone $salesQ)->where('profit_amount', '>', 0)->sum('profit_amount');
        $salesLoss = (float)(clone $salesQ)->where('profit_amount', '<', 0)->sum(\DB::raw('ABS(profit_amount)'));
        $salesPrincipalCleared = (float)(clone $salesQ)->sum('amount_principal');
        $netSalesCash = (float)$salesTotal + (float)$salesProfit - (float)$salesLoss;

        // Disbursements during this shift (cash out for issued loans)
        $disbursements = (float)\App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)
            ->where('shift_id', $shift->id)
            ->where('event_type', 'loan_disbursement')
            ->sum('amount');

        // Expected balance by ledger
        $expected = (float)$shift->opening_balance + (float)\App\Models\CashboxLedger::where('shift_id', $shift->id)
            ->select(\Illuminate\Support\Facades\DB::raw('COALESCE(SUM(direction * amount),0) as delta'))->value('delta');

        // Balance computed by formula (include reversal inflows and payment reversals)
        $reversals = (float)\App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', $cashboxId)
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->where('event_type', 'reversal')
            ->sum('amount');
        $balanceComputed = (float)$shift->opening_balance
            + (float)$transferIn
            + (float)$paymentsPrincipal
            + (float)$paymentsInterest
            + (float)$salesProfit
            - (float)$disbursements
            - (float)$expensesTotal
            + (float)$expenseReversal
            - (float)$transferOut
            - (float)$salesLoss
            - (float)$reversals;
        $balanceDelta = (float)$expected - (float)$balanceComputed;

        $cashier = \App\Models\User::where('id', $shift->user_id)->first();
        $cashierName = $cashier ? ($cashier->last_name . ' ' . $cashier->first_name) : '';

        $data = [
            'cashbox' => $cashbox,
            'cashierName' => $cashierName,
            'shift' => $shift,
            'expectedBalance' => $expected,
            'balanceComputed' => $balanceComputed,
            'balanceDelta' => $balanceDelta,
            'portfolio' => (object)['initial_sum' => $portfolioInitial, 'left_sum' => $portfolioLeft],
            'payments' => (object)['interest' => $paymentsInterest, 'principal' => $paymentsPrincipal],
            'expenses' => $expenses,
            'expensesTotal' => $expensesTotal,
            'transfers' => $transfers,
            'transferIn' => $transferIn,
            'transferOut' => $transferOut,
            'transferNet' => (float)$transferIn - (float)$transferOut,
            'salesTotal' => $salesTotal,
            'salesProfit' => $salesProfit,
            'salesLoss' => $salesLoss,
            'netSalesCash' => $netSalesCash,
            'disbursements' => $disbursements,
            'portfolioStartLeft' => (float)$portfolioLeft + (float)$paymentsPrincipal + (float)$salesPrincipalCleared - (float)$disbursements,
            'salesPrincipalCleared' => $salesPrincipalCleared,
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShiftCloseReport($data), 'shift_close_'.date('Ymd_His', $shift->closed_at).'.xlsx');
    }
}


