<?php

namespace App\Http\Controllers\Reporter;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request) {
        $loansTotalSum = 0;
        $loansLeftSum = 0;
        $loansInitialSum = 0;
        $principalPaymentsTotalSum = 0;
        $interestPaymentsTotalSum = 0;

        if ($request->get('cashbox') > 0) {
            $loansTotalSum = Loan::where('cashbox_id', $request->get('cashbox'))
                ->where('lend_date', '>=', strtotime($request->get('from')))
                ->where('lend_date', '<=', strtotime($request->get('to')));

            $payments = DB::table('payments')
                ->join('loans', 'payments.loan_id', '=', 'loans.id')
                ->whereRaw('loans.deleted_at IS NULL')
                ->whereRaw('payments.deleted_at IS NULL')
                ->where('payments.cashbox_id', $request->get('cashbox'))
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.paid_date', '>=', strtotime($request->get('from')))
                ->where('payments.paid_date', '<=', strtotime($request->get('to')));

            if($request->get('audit') == 'yes' || auth()->user()->isAudit()) {
                $loansTotalSum->where('in_audit', true);
                $payments->where('loans.in_audit', true);
            }

            $loansLeftSum = clone $loansTotalSum;
            $loansInitialSum = clone $loansTotalSum;

            unset($loansTotalSum);

            $loansLeftSum = $loansLeftSum
                ->sum('left_sum');

            $loansInitialSum = $loansInitialSum
                ->sum('initial_sum');

            $interestPaymentsTotalSum = clone $payments;
            $interestPaymentsTotalSum = $interestPaymentsTotalSum
                ->where('payments.type', Constants::PAYMENT_INTEREST)
                ->sum('sum');

            $principalPaymentsTotalSum = clone $payments;
            $principalPaymentsTotalSum = $principalPaymentsTotalSum
                ->where('payments.type', Constants::PAYMENT_PRINCIPAL)
                ->sum('sum');

            unset($payments);
        }

        return view('reporter.report.index', [
            'loansLeftSum' => $loansLeftSum,
            'loansInitialSum' => $loansInitialSum,
            'interestPaymentsTotalSum' => $interestPaymentsTotalSum,
            'principalPaymentsTotalSum' => $principalPaymentsTotalSum
        ]);
    }

    public function overdue65Form(Request $request)
    {
        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();
        return view('reporter.overdue65', [
            'cashboxes' => $cashboxes,
        ]);
    }

    public function overdue65(Request $request)
    {
        $cashboxId = (int)$request->get('cashbox');

        $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
        $expr = "GREATEST(0, FLOOR(({$today} - (CASE WHEN last_principal_payment_date > 0 THEN last_principal_payment_date ELSE interest_accumulation_date END))/86400) - (CASE WHEN interest_rate > 0 AND left_sum > 0 THEN FLOOR(latest_interest_payments_sum / ((interest_rate/30/100) * left_sum)) ELSE 0 END))";

        $loans = \App\Models\Loan::with('loaner')->with('jewelries')->with('auto')->with('phone')->with('user')->with('cashbox')
            ->where('closed_at', 0)
            ->where('company_id', Auth::user()->company_id)
            ->whereRaw($expr . ' >= 65');

        if ($cashboxId > 0) {
            $loans->where('cashbox_id', $cashboxId);
        }

        $loans = $loans->get();

        $rows = [];
        foreach ($loans as $loan) {
            $collateral = '';
            if ($loan->collateral_type == 1) {
                foreach ($loan->jewelries as $j) {
                    $collateral .= ($collateral ? '; ' : '') . $j->name . ', ' . $j->purity . ' пр., ' . $j->weight . ' гр.';
                }
            } elseif ($loan->collateral_type == 2 && $loan->auto) {
                $collateral = 'марка ' . $loan->auto->brand . ', ' . $loan->auto->year . ', ' . $loan->auto->plate_number;
            } elseif ($loan->collateral_type == 3 && $loan->phone) {
                $collateral = 'смартфон ' . $loan->phone->brand . ' ' . $loan->phone->model;
                if (!empty($loan->phone->storage_gb)) { $collateral .= ' ' . $loan->phone->storage_gb . 'GB'; }
                if (!empty($loan->phone->color)) { $collateral .= ', ' . $loan->phone->color; }
                if (!empty($loan->phone->imei)) { $collateral .= ', IMEI ' . $loan->phone->imei; }
            }

            $contractFull = '№' . $loan->document_no . ($loan->audit_document_no > 0 ? ('-' . $loan->audit_document_no) : '');
            $cashierName = $loan->user ? ($loan->user->last_name . ' ' . $loan->user->first_name) : '';

            $rows[] = [
                'cashbox' => optional($loan->cashbox)->name,
                'document_full' => $contractFull,
                'full_name' => optional($loan->loaner)->full_name,
                'phone' => optional($loan->loaner)->phone1,
                'cashier' => $cashierName,
                'collateral' => $collateral,
                'initial_sum' => (float)$loan->initial_sum,
                'unpaid_days' => (int)$loan->unpaid_days,
                'unpaid_interest' => (float)$loan->unpaid_interest,
                'lend_date' => date('Y-m-d', $loan->lend_date),
            ];
        }

        $collection = collect($rows);
        $groups = $collection
            ->groupBy('cashbox')
            ->sortKeys(SORT_NATURAL | SORT_FLAG_CASE)
            ->map(function($items){
                return $items->sortBy('cashier', SORT_NATURAL | SORT_FLAG_CASE)->values()->all();
            })
            ->all();

        $totalLoan = $collection->sum('initial_sum');
        $totalUnpaidInterest = $collection->sum('unpaid_interest');

        $p375 = (float)$request->get('p375', 0);
        $p585 = (float)$request->get('p585', 0);
        $p750 = (float)$request->get('p750', 0);
        $p875 = (float)$request->get('p875', 0);

        $goldWorth = 0.0;
        foreach ($collection as $r) {
            if (!empty($r['collateral']) && strpos($r['collateral'], 'пр.') !== false) {
                if (preg_match_all('/(\d{3})\s*пр\.,\s*([0-9]+(?:\.[0-9]+)?)\s*гр\./u', $r['collateral'], $m, PREG_SET_ORDER)) {
                    foreach ($m as $one) {
                        $purity = (int)$one[1];
                        $weight = (float)$one[2];
                        switch ($purity) {
                            case 375: $goldWorth += $weight * $p375; break;
                            case 585: $goldWorth += $weight * $p585; break;
                            case 750: $goldWorth += $weight * $p750; break;
                            case 875: $goldWorth += $weight * $p875; break;
                        }
                    }
                }
            }
        }

        $grandTotal = $totalLoan + $totalUnpaidInterest;
        $afterGoldOffset = $grandTotal - $goldWorth;

        $groupTotals = [];
        if ($cashboxId <= 0) {
            foreach ($groups as $cashboxName => $items) {
                $g = collect($items);
                $gWorth = 0.0;
                foreach ($items as $r) {
                    if (!empty($r['collateral']) && strpos($r['collateral'], 'пр.') !== false) {
                        if (preg_match_all('/(\d{3})\s*пр\.,\s*([0-9]+(?:\.[0-9]+)?)\s*гр\./u', $r['collateral'], $m, PREG_SET_ORDER)) {
                            foreach ($m as $one) {
                                $purity = (int)$one[1];
                                $weight = (float)$one[2];
                                switch ($purity) {
                                    case 375: $gWorth += $weight * $p375; break;
                                    case 585: $gWorth += $weight * $p585; break;
                                    case 750: $gWorth += $weight * $p750; break;
                                    case 875: $gWorth += $weight * $p875; break;
                                }
                            }
                        }
                    }
                }
                $loanSum = $g->sum('initial_sum');
                $interestSum = $g->sum('unpaid_interest');
                $grand = $loanSum + $interestSum;
                $after = $grand - $gWorth;
                $groupTotals[$cashboxName ?: 'Без кассы'] = [
                    'loan' => $loanSum,
                    'interest' => $interestSum,
                    'gold' => $gWorth,
                    'grand' => $grand,
                    'after' => $after,
                ];
            }
        }

        $totals = [
            'total_loan' => $totalLoan,
            'total_unpaid_interest' => $totalUnpaidInterest,
            'gold_worth' => $goldWorth,
            'grand_total' => $grandTotal,
            'after_gold_offset' => $afterGoldOffset,
            'p375' => $p375,
            'p585' => $p585,
            'p750' => $p750,
            'p875' => $p875,
        ];

        $export = new \App\Exports\Overdue65Export($groups, 'Overdue 65+', $totals, $groupTotals);
        return \Maatwebsite\Excel\Facades\Excel::download($export, 'overdue_65_plus.xlsx');
    }
}
