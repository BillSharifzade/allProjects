<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    public function index(Request $request)
    {
        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();
        return view('admin.monthly.index', [ 'cashboxes' => $cashboxes ]);
    }

    public function export(Request $request)
    {
        $cashboxId = (int)$request->get('cashbox', 0);
        $allTime = (bool)$request->get('all_time');
        $selected = [];
        if ($allTime) {
            $cid = Auth::user()->company_id;
            $minLedger = (int)\App\Models\CashboxLedger::where('company_id', $cid)
                ->where('occurred_at', '>', 0)
                ->min('occurred_at');
            $minExpense = (int)\App\Models\Expense::where('company_id', $cid)
                ->where('occurred_at', '>', 0)
                ->min('occurred_at');
            $minSale = (int)\App\Models\LoanSale::where('company_id', $cid)
                ->where('sold_at', '>', 0)
                ->min('sold_at');
            $minPayment = (int)\DB::table('payments')->where('company_id', $cid)
                ->where('paid_date', '>', 0)
                ->min('paid_date');
            $minLoan = (int)\DB::table('loans')->where('company_id', $cid)
                ->whereNull('deleted_at')
                ->where('lend_date', '>', 0)
                ->min('lend_date');
            $cand = array_filter([$minLedger, $minExpense, $minSale, $minPayment, $minLoan], function($v){ return (int)$v > 0; });
            $minTs = !empty($cand) ? (int)min($cand) : 0;
            if ($minTs <= 0) { $minTs = strtotime(date('Y-m-01')); }
            $start = new \DateTime(date('Y-m-01', $minTs));
            $end = new \DateTime(date('Y-m-01'));
            while ($start <= $end) { $selected[] = $start->format('Y-m'); $start->modify('+1 month'); }
        } else {
            $from = (string)$request->get('from');
            $to = (string)$request->get('to');
            if (!preg_match('/^\d{4}-\d{2}$/', $from)) { return redirect()->back()->withErrors(['Укажите месяц С']); }
            if ($to !== '' && !preg_match('/^\d{4}-\d{2}$/', $to)) { return redirect()->back()->withErrors(['Укажите месяц ПО']); }
            if ($to === '') { $to = $from; }
            $start = \DateTime::createFromFormat('Y-m', $from);
            $end = \DateTime::createFromFormat('Y-m', $to);
            if ($start > $end) { [$start, $end] = [$end, $start]; }
            $cur = new \DateTime($start->format('Y-m-01'));
            $count = 0;
            while ($cur <= $end && $count < 60) { $selected[] = $cur->format('Y-m'); $cur->modify('+1 month'); $count++; }
        }

        $companyId = Auth::user()->company_id;

        // Build rows per month using domain timestamps; also compute portfolio dynamics and expense notes
        $rows = [];
        $totals = [
            'interest' => 0.0,
            'principal' => 0.0,
            'disbursements' => 0.0,
            'expenses' => 0.0,
            'transfer_in' => 0.0,
            'transfer_out' => 0.0,
            'admin_fund' => 0.0,
            'sales_total' => 0.0,
            'sales_profit' => 0.0,
            'sales_loss' => 0.0,
            'sales_principal' => 0.0,
            'portfolio_start' => 0.0,
            'portfolio_growth' => 0.0,
            'portfolio_end' => 0.0,
        ];
        $expenseNotesByRow = [];

        // Baseline portfolio before the first selected month
        $firstYm = $selected[0] ?? date('Y-m');
        $rangeStart = strtotime($firstYm . '-01 00:00:00');
        $baselineDisb = (float)\DB::table('loans')
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->where('lend_date', '<', $rangeStart)
            ->sum('initial_sum');
        $baselinePrincipal = (float)\DB::table('payments')
            ->where('company_id', $companyId)
            ->whereRaw('deleted_at IS NULL')
            ->where('type', \App\Constants::PAYMENT_PRINCIPAL)
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->where('paid_date', '<', $rangeStart)
            ->sum('sum');
        $baselineSalesPrincipal = (float)\DB::table('loan_sales')
            ->where('company_id', $companyId)
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->where('sold_at', '<', $rangeStart)
            ->sum('amount_principal');
        $runningPortfolio = max(0.0, $baselineDisb - $baselinePrincipal - $baselineSalesPrincipal);
        $totals['portfolio_start'] = $runningPortfolio; // baseline before first month

        foreach ($selected as $ym) {
            $from = strtotime($ym . '-01 00:00:00');
            $to = (int)date('t', $from);
            $toTs = strtotime($ym . '-' . $to . ' 23:59:59');

            // Payments (use domain paid_date, not ledger occurred_at)
            $interest = (float)\DB::table('payments')
                ->where('company_id', $companyId)
                ->whereRaw('deleted_at IS NULL')
                ->where('type', \App\Constants::PAYMENT_INTEREST)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->whereBetween('paid_date', [$from, $toTs])
                ->sum('sum');
            $principal = (float)\DB::table('payments')
                ->where('company_id', $companyId)
                ->whereRaw('deleted_at IS NULL')
                ->where('type', \App\Constants::PAYMENT_PRINCIPAL)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->whereBetween('paid_date', [$from, $toTs])
                ->sum('sum');

            // Disbursements (use loans.lend_date)
            $disb = (float)\DB::table('loans')
                ->where('company_id', $companyId)
                ->whereNull('deleted_at')
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->whereBetween('lend_date', [$from, $toTs])
                ->sum('initial_sum');

            // Expenses (sum and details)
            $expQ = \App\Models\Expense::where('company_id', $companyId)
                ->whereBetween('occurred_at', [$from, $toTs]);
            if ($cashboxId > 0) { $expQ->where('cashbox_id', $cashboxId); }
            $expenses = (float)$expQ->sum('amount');
            // Build note text with details (first 100 items to keep size manageable)
            $expItems = \App\Models\Expense::where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->whereBetween('occurred_at', [$from, $toTs])
                ->orderBy('occurred_at','asc')->limit(100)->get();
            $userNames = \App\Models\User::whereIn('id', $expItems->pluck('user_id')->filter()->unique())->get()->keyBy('id');
            $noteLines = [];
            foreach ($expItems as $e) {
                $who = $userNames[$e->user_id]->last_name.' '.$userNames[$e->user_id]->first_name ?? ('#'.$e->user_id);
                $noteLines[] = date('Y-m-d H:i', (int)$e->occurred_at) . ' — ' . ($e->category ?: '—') . ': ' . (string)$e->description . ' (' . number_format((float)$e->amount, 2, '.', ' ') . ') — ' . $who;
            }

            // Transfers
            $trIn = (float)\App\Models\CashboxLedger::where('company_id', $companyId)
                ->where('event_type', 'transfer_in')
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->whereBetween('occurred_at', [$from, $toTs])
                ->sum('amount');
            $adminFund = (float)\App\Models\CashboxLedger::where('company_id', $companyId)
                ->where('event_type', 'admin_fund')
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->whereBetween('occurred_at', [$from, $toTs])
                ->sum('amount');
            $trOutQ = \App\Models\CashboxLedger::where('company_id', $companyId)
                ->whereBetween('occurred_at', [$from, $toTs])
                ->where('event_type', 'transfer_out');
            if ($cashboxId > 0) { $trOutQ->where('cashbox_id', $cashboxId); }
            $trOut = (float)$trOutQ->sum('amount');

            // Sales (LoanSale model stores sums at transaction time)
            $salesQ = \App\Models\LoanSale::where('company_id', $companyId)
                ->whereBetween('sold_at', [$from, $toTs]);
            if ($cashboxId > 0) { $salesQ->where('cashbox_id', $cashboxId); }
            $salesTotal = (float)(clone $salesQ)->sum('total_amount');
            $salesProfit = (float)(clone $salesQ)->where('profit_amount', '>', 0)->sum('profit_amount');
            $salesLoss = (float)(clone $salesQ)->where('profit_amount', '<', 0)->sum(\DB::raw('ABS(profit_amount)'));
            $salesPrincipal = (float)(clone $salesQ)->sum('amount_principal');

            // Portfolio dynamics (start, growth, end of month)
            $portfolioStart = $runningPortfolio;
            $portfolioGrowth = (float)$disb - (float)$principal - (float)$salesPrincipal;
            $runningPortfolio += $portfolioGrowth;

            $rows[] = [
                'month' => $ym,
                'interest' => $interest,
                'principal' => $principal,
                'disbursements' => $disb,
                'expenses' => $expenses,
                'transfer_in' => $trIn,
                'transfer_out' => $trOut,
                'admin_fund' => $adminFund,
                'sales_total' => $salesTotal,
                'sales_profit' => $salesProfit,
                'sales_loss' => $salesLoss,
                'sales_principal' => $salesPrincipal,
                'portfolio_start' => $portfolioStart,
                'portfolio_growth' => $portfolioGrowth,
                'portfolio_end' => $runningPortfolio,
            ];

            $totals['interest'] += $interest;
            $totals['principal'] += $principal;
            $totals['disbursements'] += $disb;
            $totals['expenses'] += $expenses;
            $totals['transfer_in'] += $trIn;
            $totals['transfer_out'] += $trOut;
            $totals['admin_fund'] += $adminFund;
            $totals['sales_total'] += $salesTotal;
            $totals['sales_profit'] += $salesProfit;
            $totals['sales_loss'] += $salesLoss;
            $totals['sales_principal'] += $salesPrincipal;
            $totals['portfolio_growth'] += $portfolioGrowth;
            $totals['portfolio_end'] = $runningPortfolio;

            // Row number for this ym in sheet (title row + header row + index)
            $rowNumber = 3 + count($rows) - 1;
            $expenseNotesByRow[$rowNumber] = implode("\n", $noteLines);
        }

        $titleCashbox = 'Все кассы';
        if ($cashboxId > 0) { $cb = \App\Models\Cashbox::find($cashboxId); if ($cb) { $titleCashbox = $cb->name; } }
        $data = [ 'rows' => $rows, 'totals' => $totals, 'selected' => $selected, 'titleCashbox' => $titleCashbox, 'allTime' => $allTime, 'expenseNotesByRow' => $expenseNotesByRow ];
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlyReport($data), 'monthly_'.date('Ymd_His').'.xlsx');
    }
}


