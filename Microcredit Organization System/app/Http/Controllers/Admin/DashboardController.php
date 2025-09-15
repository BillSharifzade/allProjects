<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\CashboxLedger;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\LoanSale;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = (string)$request->get('range', '30d');
        $cashboxId = (int)$request->get('cashbox', 0); // 0 => all cashboxes
        $now = time();
        $map = [
            '1d' => 1, '7d' => 7, '30d' => 30, '90d' => 90, '180d' => 180, '365d' => 365, '730d' => 730, 'all' => null,
        ];
        $days = $map[$range] ?? 30;
        $fromTs = is_null($days) ? 0 : ($now - $days * 86400);
        // When "all" selected, compute exact earliest timestamp for the chosen cashbox scope
        if (is_null($days)) {
            $cid = $companyId = Auth::user()->company_id;
            $mins = [];
            $mins[] = (int)DB::table('payments')->where('company_id', $cid)
                ->when($cashboxId>0,function($q) use ($cashboxId){ $q->where('cashbox_id',$cashboxId); })
                ->where('paid_date', '>', 0)->min('paid_date');
            $mins[] = (int)CashboxLedger::where('company_id', $cid)
                ->when($cashboxId>0,function($q) use ($cashboxId){ $q->where('cashbox_id',$cashboxId); })
                ->where('occurred_at', '>', 0)->min('occurred_at');
            $mins[] = (int)Expense::where('company_id', $cid)
                ->when($cashboxId>0,function($q) use ($cashboxId){ $q->where('cashbox_id',$cashboxId); })
                ->where('occurred_at', '>', 0)->min('occurred_at');
            $mins[] = (int)LoanSale::where('company_id', $cid)
                ->when($cashboxId>0,function($q) use ($cashboxId){ $q->where('cashbox_id',$cashboxId); })
                ->where('sold_at', '>', 0)->min('sold_at');
            $mins[] = (int)DB::table('loans')->where('company_id', $cid)
                ->when($cashboxId>0,function($q) use ($cashboxId){ $q->where('cashbox_id',$cashboxId); })
                ->where('lend_date', '>', 0)->min('lend_date');
            $mins = array_filter($mins, function($v){ return $v > 0; });
            $fromTs = !empty($mins) ? min($mins) : $now;
        }
        // Granularity selection
        // For "all" always show month-by-month over the entire history
        if ($range === 'all') {
            $granularity = 'month';
        } else {
            $granularity = ($days >= 365) ? 'month' : 'day';
        }

        $companyId = Auth::user()->company_id;

        // Build labels based on granularity
        $labels = [];
        if ($granularity === 'day') {
            $points = (int)ceil(($now - $fromTs) / 86400);
            // For "all" do not cap points; for other ranges cap to 730 to avoid heavy charts
            if ($range !== 'all') { $points = max(1, min($points, 730)); } else { $points = max(1, $points); }
            for ($i = $points - 1; $i >= 0; $i--) {
                $ts = strtotime(date('Y-m-d', $now - $i * 86400));
                $labels[] = date('Y-m-d', $ts);
            }
        } else {
            $start = new \DateTime(date('Y-m-01', $fromTs));
            $end = new \DateTime(date('Y-m-01', $now));
            while ($start <= $end) {
                $labels[] = $start->format('Y-m');
                $start->modify('+1 month');
            }
        }

        $rangeStart = $fromTs; $rangeEnd = $now;
        $fmt = ($granularity === 'month') ? '%Y-%m' : '%Y-%m-%d';

        // For "all" + monthly we will derive labels from actual data months (union across datasets)
        if ($range === 'all' && $granularity === 'month') {
            $labelsSet = [];

            $payRows = DB::table('payments')
                ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(paid_date), '$fmt') as d, type, SUM(sum) as s")
                ->where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d','type')->get();
            foreach ($payRows as $r) { $labelsSet[$r->d] = true; }

            $disbRows = DB::table('loans')
                ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(lend_date), '$fmt') as d, SUM(initial_sum) as s")
                ->where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d')->get();
            foreach ($disbRows as $r) { $labelsSet[$r->d] = true; }

            $expRows = DB::table('expenses')
                ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(occurred_at), '$fmt') as d, SUM(amount) as s")
                ->where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d')->get();
            foreach ($expRows as $r) { $labelsSet[$r->d] = true; }

            $admRows = CashboxLedger::selectRaw("DATE_FORMAT(FROM_UNIXTIME(occurred_at), '$fmt') as d, SUM(amount) as s")
                ->where('company_id', $companyId)
                ->where('event_type','admin_fund')
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d')->get();
            foreach ($admRows as $r) { $labelsSet[$r->d] = true; }

            $saleRows = LoanSale::selectRaw("DATE_FORMAT(FROM_UNIXTIME(sold_at), '$fmt') as d, SUM(total_amount) as total, SUM(GREATEST(profit_amount,0)) as profit, SUM(GREATEST(-profit_amount,0)) as loss")
                ->where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d')->get();
            foreach ($saleRows as $r) { $labelsSet[$r->d] = true; }

            $balRows = CashboxLedger::selectRaw("DATE_FORMAT(FROM_UNIXTIME(occurred_at), '$fmt') as d, SUM(direction * amount) as s")
                ->where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d')->get();
            foreach ($balRows as $r) { $labelsSet[$r->d] = true; }

            $labels = array_keys($labelsSet);
            sort($labels, SORT_NATURAL);

            $rangeStart = 0; $rangeEnd = $now; // not used to filter in this branch

            $payInterest = array_fill_keys($labels, 0.0);
            $payPrincipal = array_fill_keys($labels, 0.0);
            foreach ($payRows as $r) {
                if (!isset($payInterest[$r->d])) { continue; }
                if ((int)$r->type === \App\Constants::PAYMENT_INTEREST) { $payInterest[$r->d] += (float)$r->s; }
                if ((int)$r->type === \App\Constants::PAYMENT_PRINCIPAL) { $payPrincipal[$r->d] += (float)$r->s; }
            }

            $disbursements = array_fill_keys($labels, 0.0);
            foreach ($disbRows as $r) { if (isset($disbursements[$r->d])) { $disbursements[$r->d] += (float)$r->s; } }

            $expenses = array_fill_keys($labels, 0.0);
            foreach ($expRows as $r) { if (isset($expenses[$r->d])) { $expenses[$r->d] += (float)$r->s; } }

            $adminFundArr = array_fill_keys($labels, 0.0);
            foreach ($admRows as $r) { if (isset($adminFundArr[$r->d])) { $adminFundArr[$r->d] += (float)$r->s; } }

            $salesCash = array_fill_keys($labels, 0.0);
            $salesTotal = array_fill_keys($labels, 0.0);
            $salesProfit = array_fill_keys($labels, 0.0);
            $salesLoss = array_fill_keys($labels, 0.0);
            foreach ($saleRows as $r) {
                if (!isset($salesCash[$r->d])) { continue; }
                $salesTotal[$r->d] += (float)$r->total;
                $salesCash[$r->d] += (float)$r->total + (float)$r->profit - (float)$r->loss;
                $salesProfit[$r->d] += (float)$r->profit;
                $salesLoss[$r->d] += (float)$r->loss;
            }

            $balanceDelta = array_fill_keys($labels, 0.0);
            foreach ($balRows as $r) { if (isset($balanceDelta[$r->d])) { $balanceDelta[$r->d] += (float)$r->s; } }

            // Loans created count
            $loanCreateRows = DB::table('loans')
                ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(lend_date), '$fmt') as d, COUNT(*) as c")
                ->where('company_id', $companyId)
                ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
                ->groupBy('d')->get();
            foreach ($loanCreateRows as $r) { $labelsSet[$r->d] = true; }
            $loansCreated = array_fill_keys($labels, 0);
            foreach ($loanCreateRows as $r) { if (isset($loansCreated[$r->d])) { $loansCreated[$r->d] += (int)$r->c; } }
        } else {
            // Payments
        $payRows = DB::table('payments')
            ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(paid_date), '$fmt') as d, type, SUM(sum) as s")
            ->where('company_id', $companyId)
            ->whereBetween('paid_date', [$rangeStart, $rangeEnd])
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->groupBy('d','type')->get();
        $payInterest = array_fill_keys($labels, 0.0);
        $payPrincipal = array_fill_keys($labels, 0.0);
        foreach ($payRows as $r) {
            if (!isset($payInterest[$r->d])) { continue; }
            if ((int)$r->type === \App\Constants::PAYMENT_INTEREST) { $payInterest[$r->d] += (float)$r->s; }
            if ((int)$r->type === \App\Constants::PAYMENT_PRINCIPAL) { $payPrincipal[$r->d] += (float)$r->s; }
        }

        // Disbursements (issuances) from loans to ensure full historical coverage
        // Use table query to avoid Loan global scopes (must include closed loans)
        $disbRows = DB::table('loans')
            ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(lend_date), '$fmt') as d, SUM(initial_sum) as s")
            ->where('company_id', $companyId)
            ->whereBetween('lend_date', [$rangeStart, $rangeEnd])
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->groupBy('d')->get();
        $disbursements = array_fill_keys($labels, 0.0);
        foreach ($disbRows as $r) { if (isset($disbursements[$r->d])) { $disbursements[$r->d] += (float)$r->s; } }

        // Expenses
        $expRows = Expense::selectRaw("DATE_FORMAT(FROM_UNIXTIME(occurred_at), '$fmt') as d, SUM(amount) as s")
            ->where('company_id', $companyId)
            ->whereBetween('occurred_at', [$rangeStart, $rangeEnd])
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->groupBy('d')->get();
        $expenses = array_fill_keys($labels, 0.0);
        foreach ($expRows as $r) { if (isset($expenses[$r->d])) { $expenses[$r->d] += (float)$r->s; } }

        // Transfers net (in minus out; admin_fund counts as in)
        $admRows = CashboxLedger::selectRaw("DATE_FORMAT(FROM_UNIXTIME(occurred_at), '$fmt') as d, SUM(amount) as s")
            ->where('company_id', $companyId)
            ->whereBetween('occurred_at', [$rangeStart, $rangeEnd])
            ->where('event_type','admin_fund')
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->groupBy('d')->get();
        $adminFundArr = array_fill_keys($labels, 0.0);
        foreach ($admRows as $r) { if (isset($adminFundArr[$r->d])) { $adminFundArr[$r->d] += (float)$r->s; } }

        // Sales cash effect and profit/loss
        $saleRows = LoanSale::selectRaw("DATE_FORMAT(FROM_UNIXTIME(sold_at), '$fmt') as d, SUM(total_amount) as total, SUM(GREATEST(profit_amount,0)) as profit, SUM(GREATEST(-profit_amount,0)) as loss")
            ->where('company_id', $companyId)
            ->whereBetween('sold_at', [$rangeStart, $rangeEnd])
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->groupBy('d')->get();
        $salesCash = array_fill_keys($labels, 0.0);
        $salesTotal = array_fill_keys($labels, 0.0);
        $salesProfit = array_fill_keys($labels, 0.0);
        $salesLoss = array_fill_keys($labels, 0.0);
        foreach ($saleRows as $r) {
            if (!isset($salesCash[$r->d])) { continue; }
            $salesTotal[$r->d] += (float)$r->total;
            $salesCash[$r->d] += (float)$r->total + (float)$r->profit - (float)$r->loss;
            $salesProfit[$r->d] += (float)$r->profit;
            $salesLoss[$r->d] += (float)$r->loss;
        }

        // Balance delta over time from ledger (net of all events). On all-cashboxes scope this naturally cancels internal transfers except admin_fund.
        $balRows = CashboxLedger::selectRaw("DATE_FORMAT(FROM_UNIXTIME(occurred_at), '$fmt') as d, SUM(direction * amount) as s")
            ->where('company_id', $companyId)
            ->whereBetween('occurred_at', [$rangeStart, $rangeEnd])
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->groupBy('d')->get();
        $balanceDelta = array_fill_keys($labels, 0.0);
        foreach ($balRows as $r) { if (isset($balanceDelta[$r->d])) { $balanceDelta[$r->d] += (float)$r->s; } }

        }

        // Totals for composition charts
        $sum = function(array $a){ return array_sum(array_values($a)); };
        $summary = [
            'interest' => $sum($payInterest),
            'principal' => $sum($payPrincipal),
            'disbursements' => $sum($disbursements),
            'expenses' => $sum($expenses),
            'adminFund' => $sum($adminFundArr),
            'salesTotal' => $sum($salesTotal),
            'salesProfit' => $sum($salesProfit),
            'salesLoss' => $sum($salesLoss),
            'balanceNet' => $sum($balanceDelta),
        ];

        // Per-cashbox current balances (opening + ledger delta of last open shift if any)
        $cashboxes = Cashbox::orderBy('name')->get();
        $balances = [];
        foreach ($cashboxes as $cb) {
            $delta = (float)CashboxLedger::where('cashbox_id', $cb->id)
                ->where('company_id', $companyId)
                ->select(DB::raw('COALESCE(SUM(direction * amount),0) as d'))
                ->value('d');
            $balances[] = [ 'id' => $cb->id, 'name' => $cb->name, 'amount' => $delta ];
        }

        // Portfolio snapshot (current) — align with active credits list logic (ignore global scopes)
        $portfolioActive = (float)DB::table('loans')
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('closed_at', 0)
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->sum('left_sum');
        $portfolioInitial = (float)DB::table('loans')
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->where('closed_at', 0)
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->sum('initial_sum');

        // Workforce stats
        $workers = [
            'cashiers' => (int)\App\Models\User::whereIn('role', ['cashier','cashier-audit'])->count(),
            'incassators' => (int)\App\Models\User::where('role','incassator')->count(),
            'reporters' => (int)\App\Models\User::where('role','reporter')->count(), 
            'audit' => (int)\App\Models\User::where('role','audit')->count(),
        ];
        // HR positions breakdown
        $hrRows = \App\Models\HrEmployee::select('position', DB::raw('COUNT(*) as c'))
            ->groupBy('position')->get();
        $hrPositions = [ 'labels' => [], 'values' => [] ];
        foreach ($hrRows as $r) { $hrPositions['labels'][] = (string)$r->position ?: '—'; $hrPositions['values'][] = (int)$r->c; }
        $cashboxCount = (int)\App\Models\Cashbox::count();

        // Top cashboxes within selected range
        $activeByCb = Loan::select('cashbox_id', DB::raw('SUM(left_sum) as s'))
            ->where('company_id', $companyId)->where('closed_at', 0)
            ->groupBy('cashbox_id')->pluck('s','cashbox_id');
        $wealthiestId = $activeByCb->sortDesc()->keys()->first();

        $interestByCb = CashboxLedger::select('cashbox_id', DB::raw('SUM(amount) as s'))
            ->where('company_id', $companyId)
            ->where('event_type','interest_payment')
            ->whereBetween('occurred_at', [$rangeStart, $rangeEnd])
            ->groupBy('cashbox_id')->pluck('s','cashbox_id');
        $profitableId = $interestByCb->sortDesc()->keys()->first();

        $growthByCb = CashboxLedger::select('cashbox_id', DB::raw("SUM(CASE WHEN event_type='loan_disbursement' THEN amount ELSE 0 END) - SUM(CASE WHEN event_type='principal_payment' THEN amount ELSE 0 END) as g"))
            ->where('company_id', $companyId)
            ->whereBetween('occurred_at', [$rangeStart, $rangeEnd])
            ->groupBy('cashbox_id')->pluck('g','cashbox_id');
        $growingId = $growthByCb->sortDesc()->keys()->first();

        $createdByCb = Loan::select('cashbox_id', DB::raw('SUM(initial_sum) as c'))
            ->where('company_id', $companyId)
            ->whereBetween('lend_date', [$rangeStart, $rangeEnd])
            ->groupBy('cashbox_id')->pluck('c','cashbox_id');
        $potentialId = $createdByCb->sortDesc()->keys()->first();

        $idToName = \App\Models\Cashbox::pluck('name','id');
        $tops = [
            'wealthiest' => ['id' => $wealthiestId, 'name' => $idToName[$wealthiestId] ?? null, 'value' => (float)($activeByCb[$wealthiestId] ?? 0)],
            'profitable' => ['id' => $profitableId, 'name' => $idToName[$profitableId] ?? null, 'value' => (float)($interestByCb[$profitableId] ?? 0)],
            'growing' => ['id' => $growingId, 'name' => $idToName[$growingId] ?? null, 'value' => (float)($growthByCb[$growingId] ?? 0)],
            'potential' => ['id' => $potentialId, 'name' => $idToName[$potentialId] ?? null, 'value' => (int)($createdByCb[$potentialId] ?? 0)],
        ];
        // Full ranked datasets for tops chart
        $topsData = [
            'wealthiest' => [ 'labels' => [], 'values' => [] ],
            'profitable' => [ 'labels' => [], 'values' => [] ],
            'growing' => [ 'labels' => [], 'values' => [] ],
            'potential' => [ 'labels' => [], 'values' => [] ],
        ];
        foreach ($activeByCb->sortDesc() as $id=>$val) { $topsData['wealthiest']['labels'][] = $idToName[$id] ?? ('#'.$id); $topsData['wealthiest']['values'][] = (float)$val; }
        foreach ($interestByCb->sortDesc() as $id=>$val) { $topsData['profitable']['labels'][] = $idToName[$id] ?? ('#'.$id); $topsData['profitable']['values'][] = (float)$val; }
        foreach ($growthByCb->sortDesc() as $id=>$val) { $topsData['growing']['labels'][] = $idToName[$id] ?? ('#'.$id); $topsData['growing']['values'][] = (float)$val; }
        foreach ($createdByCb->sortDesc() as $id=>$val) { $topsData['potential']['labels'][] = $idToName[$id] ?? ('#'.$id); $topsData['potential']['values'][] = (int)$val; }

        // Loans created count time series for completeness
        $loanCreateRows = DB::table('loans')
            ->selectRaw("DATE_FORMAT(FROM_UNIXTIME(lend_date), '$fmt') as d, COUNT(*) as c")
            ->where('company_id', $companyId)
            ->when($cashboxId > 0, function($q) use ($cashboxId){ $q->where('cashbox_id', $cashboxId); })
            ->whereBetween('lend_date', [$rangeStart, $rangeEnd])
            ->groupBy('d')->get();
        $loansCreated = array_fill_keys($labels, 0);
        foreach ($loanCreateRows as $r) { if (isset($loansCreated[$r->d])) { $loansCreated[$r->d] += (int)$r->c; } }

        return view('admin.dashboard.index', [
            'labels' => $labels,
            'data' => [
                'interest' => array_values($payInterest),
                'principal' => array_values($payPrincipal),
                'disbursements' => array_values($disbursements),
                'expenses' => array_values($expenses),
                'adminFund' => array_values($adminFundArr),
                'balance' => array_values($balanceDelta),
                'loansCreated' => array_values($loansCreated),
                'salesCash' => array_values($salesCash),
                'salesTotal' => array_values($salesTotal),
                'salesProfit' => array_values($salesProfit),
                'salesLoss' => array_values($salesLoss),
            ],
            'balances' => $balances,
            'portfolio' => [ 'active' => $portfolioActive, 'initial' => $portfolioInitial ],
            'range' => $range,
            'granularity' => $granularity,
            'summary' => $summary,
            'cashboxes' => $cashboxes,
            'selectedCashbox' => $cashboxId,
            'workers' => $workers,
            'cashboxCount' => $cashboxCount,
            'tops' => $tops,
            'topsData' => $topsData,
            'hrPositions' => $hrPositions,
        ]);
    }
}
