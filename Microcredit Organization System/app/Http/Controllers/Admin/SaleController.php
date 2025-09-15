<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\Loan;
use App\Models\LoanSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $items = LoanSale::with('cashbox')->orderBy('id', 'desc')->paginate(50);

        if($from || $to) {
            if(!$from) { $from = $to; }
            if(!$to) { $to = $from; }
            $fromTs = strtotime($from . ' 00:00:00');
            $toTs = strtotime($to . ' 23:59:59');
            $items = LoanSale::with('cashbox')->whereBetween('sold_at', [$fromTs, $toTs])->orderBy('id', 'desc')->paginate(50);
        }

        $loans = Loan::whereIn('id', $items->pluck('loan_id')->unique())->get()->keyBy('id');

        return view('admin.sale.index', [
            'items' => $items,
            'loans' => $loans,
        ]);
    }

    public function cancel(Request $request, LoanSale $sale)
    {
        if ($sale->canceled_at > 0) {
            return redirect()->back();
        }

        DB::beginTransaction();

        // Revert loan snapshot
        $loan = Loan::where('id', $sale->loan_id)->firstOrFail();
        $loan->left_sum = $sale->prev_left_sum;
        $loan->last_principal_payment_date = $sale->prev_last_principal_payment_date;
        $loan->last_interest_payment_date = $sale->prev_last_interest_payment_date;
        $loan->latest_interest_payments_sum = $sale->prev_latest_interest_payments_sum;
        $loan->save();

        // Ledger reversal (outflow equal to sale total)
        CashboxLedger::create([
            'company_id' => Auth::user()->company_id,
            'cashbox_id' => $sale->cashbox_id,
            'user_id' => $sale->user_id,
            'shift_id' => $sale->shift_id,
            'event_type' => 'loan_sale_reversal',
            'event_id' => $sale->event_id,
            'direction' => -1,
            'amount' => (float)$sale->total_amount,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);

        // Reverse previously posted profit/loss to neutralize balance
        $profit = (float)$sale->profit_amount;
        if ($profit != 0.0) {
            CashboxLedger::create([
                'company_id' => Auth::user()->company_id,
                'cashbox_id' => $sale->cashbox_id,
                'user_id' => $sale->user_id,
                'shift_id' => $sale->shift_id,
                'event_type' => 'loan_sale_profit_reversal',
                'event_id' => $sale->event_id,
                'direction' => $profit > 0 ? -1 : +1,
                'amount' => abs($profit),
                'occurred_at' => time(),
                'created_at' => time(),
            ]);
        }

        $sale->canceled_at = time();
        $sale->canceled_by = Auth::id();
        $sale->save();

        DB::commit();

        return redirect()->back();
    }
}


