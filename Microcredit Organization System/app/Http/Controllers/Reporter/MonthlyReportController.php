<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonthlyReportController extends Controller
{
    public function index(Request $request)
    {
        $cashboxes = \App\Models\Cashbox::orderBy('name')->get();
        return view('reporter.monthly.index', [ 'cashboxes' => $cashboxes ]);
    }

    public function export(Request $request)
    {
        $cashboxId = (int)$request->get('cashbox', 0);
        $allTime = (bool)$request->get('all_time');
        $selected = [];
        if ($allTime) {
            $min1 = (int)\App\Models\CashboxLedger::where('company_id', Auth::user()->company_id)->min('occurred_at');
            $min2 = (int)\App\Models\Expense::where('company_id', Auth::user()->company_id)->min('occurred_at');
            $min3 = (int)\App\Models\LoanSale::where('company_id', Auth::user()->company_id)->min('sold_at');
            $minTs = max(0, min(array_filter([$min1, $min2, $min3], function($v){ return $v > 0; })));
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

        $rows = [];
        $totals = [
            'interest' => 0.0,'principal' => 0.0,'disbursements' => 0.0,'expenses' => 0.0,'expense_reversal' => 0.0,
            'transfer_in' => 0.0,'transfer_out' => 0.0,'sales_total' => 0.0,'sales_profit' => 0.0,'sales_loss' => 0.0,
        ];
        foreach ($selected as $ym) {
            $from = strtotime($ym . '-01 00:00:00');
            $toTs = strtotime($ym . '-' . date('t', $from) . ' 23:59:59');
            $interestQ = \App\Models\CashboxLedger::where('company_id', $companyId)->where('event_type', 'interest_payment')->whereBetween('occurred_at', [$from, $toTs]); if ($cashboxId > 0) { $interestQ->where('cashbox_id', $cashboxId); } $interest = (float)$interestQ->sum('amount');
            $principalQ = \App\Models\CashboxLedger::where('company_id', $companyId)->where('event_type', 'principal_payment')->whereBetween('occurred_at', [$from, $toTs]); if ($cashboxId > 0) { $principalQ->where('cashbox_id', $cashboxId); } $principal = (float)$principalQ->sum('amount');
            $disbQ = \App\Models\CashboxLedger::where('company_id', $companyId)->where('event_type', 'loan_disbursement')->whereBetween('occurred_at', [$from, $toTs]); if ($cashboxId > 0) { $disbQ->where('cashbox_id', $cashboxId); } $disb = (float)$disbQ->sum('amount');
            $expQ = \App\Models\Expense::where('company_id', $companyId)->whereBetween('occurred_at', [$from, $toTs]); if ($cashboxId > 0) { $expQ->where('cashbox_id', $cashboxId); } $expenses = (float)$expQ->sum('amount');
            $expRevQ = \App\Models\CashboxLedger::where('company_id', $companyId)->where('event_type', 'expense_reversal')->whereBetween('occurred_at', [$from, $toTs]); if ($cashboxId > 0) { $expRevQ->where('cashbox_id', $cashboxId); } $expenseReversal = (float)$expRevQ->sum('amount');
            $trInQ = \App\Models\CashboxLedger::where('company_id', $companyId)->whereBetween('occurred_at', [$from, $toTs])->whereIn('event_type', ['transfer_in','admin_fund']); if ($cashboxId > 0) { $trInQ->where('cashbox_id', $cashboxId); } $trIn = (float)$trInQ->sum('amount');
            $trOutQ = \App\Models\CashboxLedger::where('company_id', $companyId)->whereBetween('occurred_at', [$from, $toTs])->where('event_type', 'transfer_out'); if ($cashboxId > 0) { $trOutQ->where('cashbox_id', $cashboxId); } $trOut = (float)$trOutQ->sum('amount');
            $salesQ = \App\Models\LoanSale::where('company_id', $companyId)->whereBetween('sold_at', [$from, $toTs]); if ($cashboxId > 0) { $salesQ->where('cashbox_id', $cashboxId); }
            $salesTotal = (float)(clone $salesQ)->sum('total_amount');
            $salesProfit = (float)(clone $salesQ)->where('profit_amount', '>', 0)->sum('profit_amount');
            $salesLoss = (float)(clone $salesQ)->where('profit_amount', '<', 0)->sum(\DB::raw('ABS(profit_amount)'));
            $rows[] = compact('ym','interest','principal','disbursements','expenses','expenseReversal','transfer_in','transfer_out','sales_total','sales_profit','sales_loss');
            $totals['interest'] += $interest; $totals['principal'] += $principal; $totals['disbursements'] += $disb; $totals['expenses'] += $expenses; $totals['expense_reversal'] += $expenseReversal; $totals['transfer_in'] += $trIn; $totals['transfer_out'] += $trOut; $totals['sales_total'] += $salesTotal; $totals['sales_profit'] += $salesProfit; $totals['sales_loss'] += $salesLoss;
        }
        $titleCashbox = 'Все кассы'; if ($cashboxId > 0) { $cb = \App\Models\Cashbox::find($cashboxId); if ($cb) { $titleCashbox = $cb->name; } }
        $data = [ 'rows' => $rows, 'totals' => $totals, 'selected' => $selected, 'titleCashbox' => $titleCashbox, 'allTime' => $allTime ];
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonthlyReport($data), 'monthly_'.date('Ymd_His').'.xlsx');
    }
}


