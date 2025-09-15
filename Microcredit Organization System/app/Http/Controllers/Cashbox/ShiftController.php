<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function open(Request $request)
    {
        $opening = (float)$request->input('opening_balance', 0);
        $cashboxId = Auth::user()->cashboxUser->cashbox_id;

        $exists = CashierShift::where('user_id', Auth::user()->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)->first();
        if ($exists) {
            return redirect()->back()->withErrors(['Смена уже открыта'])->withInput();
        }

        $shift = new CashierShift();
        $shift->company_id = Auth::user()->company_id;
        $shift->cashbox_id = $cashboxId;
        $shift->user_id = Auth::user()->id;
        $shift->opened_at = time();
        $shift->opening_balance = $opening;
        $shift->created_at = time();
        $shift->save();

        return redirect()->back();
    }

    public function close(Request $request)
    {
        $counted = (float)$request->input('counted_balance', 0);
        $cashboxId = Auth::user()->cashboxUser->cashbox_id;

        $shift = CashierShift::where('user_id', Auth::user()->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)->first();
        if (!$shift) {
            return redirect()->back()->withErrors(['Нет открытой смены'])->withInput();
        }

        // Compute expected balance = opening + sum(direction * amount) for this shift
        $delta = \App\Models\CashboxLedger::where('shift_id', $shift->id)
            ->select(\Illuminate\Support\Facades\DB::raw('COALESCE(SUM(direction * amount),0) as delta'))
            ->value('delta');
        $expected = (float)$shift->opening_balance + (float)$delta;
        $shift->closed_at = time();
        $shift->closing_balance = $counted;
        $shift->discrepancy = round($counted - $expected, 2);
        $shift->updated_at = time();
        $shift->save();

        // Redirect back and trigger report download asynchronously
        $reportUrl = url('/shift/report/' . $shift->id);
        return redirect()->back()->with('shift_report_url', $reportUrl);
    }

    public function report(\Illuminate\Http\Request $request, CashierShift $shift)
    {
        // Security: only the owner cashier of this shift can download
        if ($shift->user_id != Auth::id() || $shift->cashbox_id != Auth::user()->cashboxUser->cashbox_id) {
            abort(403);
        }

        $cashboxId = $shift->cashbox_id;

        // Build XLSX shift close report based on saved shift timestamps
        $todayStart = (int)$shift->opened_at;
        $todayEnd = (int)$shift->closed_at;

        $cashbox = \App\Models\Cashbox::where('id', $cashboxId)->first();

        // Portfolio snapshot
        $portfolioInitial = \App\Models\Loan::where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)->sum('initial_sum');
        $portfolioLeft = \App\Models\Loan::where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)->sum('left_sum');

        // Payments during shift window (ledger-based to match actual shift activity)
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
        // Expense reversals (ledger inflow)
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

        // Expected balance by ledger (opening + all ledger deltas for this shift)
        $expected = (float)$shift->opening_balance + (float)\App\Models\CashboxLedger::where('shift_id', $shift->id)
            ->select(\Illuminate\Support\Facades\DB::raw('COALESCE(SUM(direction * amount),0) as delta'))
            ->value('delta');

        // Balance computed by explicit formula requested
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

        // Portfolio reconciliation (approx): reconstruct start left sum
        // StartLeft = EndLeft + principalPayments + soldPrincipal - disbursements
        $portfolioStartLeft = (float)$portfolioLeft
            + (float)$paymentsPrincipal
            + (float)$salesPrincipalCleared
            - (float)$disbursements;

        $data = [
            'cashbox' => $cashbox,
            'cashierName' => Auth::user()->last_name . ' ' . Auth::user()->first_name,
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
            'portfolioStartLeft' => $portfolioStartLeft,
            'salesPrincipalCleared' => $salesPrincipalCleared,
        ];

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ShiftCloseReport($data), 'shift_close_'.date('Ymd_His', $shift->closed_at).'.xlsx');
    }
}


