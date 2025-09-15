<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request) {
        $loans = [];
        $loansTotalSum = 0;
        $loansLeftSum = 0;
        $loansInitialSum = 0;
        $loanJewelries = [];
        $collateral_type = (int)$request->get('collateral_type');

        if ($request->get('cashbox') > 0) {
            $loans = Loan::with('jewelries')
                ->with('loaner')
                ->with('auto')
                ->with('phone')
                ->where('closed_at', 0)
                ->where('cashbox_id', $request->get('cashbox'));

            $loansTotalSum = DB::table('loans')
                ->whereRaw('deleted_at is NULL')
                ->where('closed_at', 0)
                ->where('cashbox_id', $request->get('cashbox'));


            $loanJewelries = DB::table('loan_jewelries')
                ->selectRaw('SUM(weight) as weight, purity')
                ->join('loans', 'loan_jewelries.loan_id', '=', 'loans.id')
                ->whereRaw('loans.deleted_at IS NULL')
                ->whereRaw('loan_jewelries.deleted_at IS NULL')
                ->where('loans.closed_at', 0)
                ->where('loans.cashbox_id', $request->get('cashbox'));

            if($collateral_type > 0) {
                $loans->where('collateral_type', $collateral_type);
                $loansTotalSum->where('collateral_type', $collateral_type);
                $loanJewelries->where('loans.collateral_type', $collateral_type);
            }

            if($request->get('audit') == 'yes' || auth()->user()->isAudit()) {
                $loans->where('in_audit', true);
                $loansTotalSum->where('in_audit', true);
                $loanJewelries->where('loans.in_audit', true);
            }

            $isOverdue = false;
            if ($request->get('filter') == 'overdue') {
                $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
                $expr = "GREATEST(0, FLOOR(({$today} - (CASE WHEN last_principal_payment_date > 0 THEN last_principal_payment_date ELSE interest_accumulation_date END))/86400) - (CASE WHEN interest_rate > 0 AND left_sum > 0 THEN FLOOR(latest_interest_payments_sum / ((interest_rate/30/100) * left_sum)) ELSE 0 END))";
                $loans->whereRaw($expr . ' >= 35');
                $loans->whereHas('loaner');
                $loans->orderByRaw($expr . ' DESC');
                $isOverdue = true;
            }

            if (!$isOverdue) {
                $loans->orderBy('id', 'desc');
            }

            $loans = $loans->paginate(50);

            // Incassation transfer statuses for badges
            $incTransfers = collect();
            try {
                if ($loans->count() > 0) {
                    $incTransfers = \App\Models\IncassationTransfer::whereIn('loan_id', $loans->pluck('id')->all())
                        ->orderBy('id','desc')->get()->keyBy('loan_id');
                }
            } catch (\Throwable $e) { $incTransfers = collect(); }

            $loansInitialSum = clone $loansTotalSum;
            $loansLeftSum = clone $loansTotalSum;

            unset($loansTotalSum);

            $loansInitialSum = $loansInitialSum
                ->sum('initial_sum');

            $loansLeftSum = $loansLeftSum
                ->sum('left_sum');

            $loanJewelries = $loanJewelries
                ->groupBy('loan_jewelries.purity')
                ->get();
        }

        return view('reporter.loan.index', [
            'loans' => $loans,
            'loansInitialSum' => $loansInitialSum,
            'loansLeftSum' => $loansLeftSum,
            'loanJewelries' => $loanJewelries,
            'incTransfers' => $incTransfers,
        ]);
    }

    public function closed(Request $request) {
        $loans = [];

        if ($request->get('cashbox') > 0) {
            $loans = Loan::with('jewelries')
                ->with('loaner')
                ->with('auto')
                ->with('phone')
                ->where('closed_at', '>',0)
                ->where('cashbox_id', $request->get('cashbox'));

            if($request->get('audit') == 'yes') {
                $loans->where('in_audit', true);
            }

            if($request->get('from') != '') {
                $loans->where('closed_at', '>=', strtotime($request->get('from')));
            }

            if($request->get('to') != '') {
                $loans->where('closed_at', '<=', strtotime($request->get('to')));
            }

            $loans = $loans
                ->orderBy('id', 'desc')
                ->paginate(50);
        }

        $incTransfers = collect();
        try {
            if ($loans && $loans->count() > 0) {
                $incTransfers = \App\Models\IncassationTransfer::whereIn('loan_id', $loans->pluck('id')->all())
                    ->orderBy('id','desc')->get()->keyBy('loan_id');
            }
        } catch (\Throwable $e) { $incTransfers = collect(); }

        return view('reporter.loan.index', [
            'loans' => $loans,
            'incTransfers' => $incTransfers,
        ]);
    }
}
