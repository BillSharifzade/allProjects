<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashboxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class TransferHistoryController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if(!$from && !$to) {
            $empty = new LengthAwarePaginator([], 0, 50);
            return view('admin.transfer.index', [
                'items' => $empty,
                'cashboxUsers' => collect(),
                'pairs' => collect(),
                'total' => null,
            ]);
        }

        // Normalize date range to inclusive days
        $fromTs = $from ? strtotime($from) : null;
        $toTs = $to ? (strtotime($to) + 86399) : null; // include entire day

        // If only one bound provided, clamp to that day
        if($fromTs && !$toTs) { $toTs = $fromTs + 86399; }
        if($toTs && !$fromTs) { $fromTs = $toTs - 86399; }

        $query = CashboxLedger::where('company_id', Auth::user()->company_id)
            ->whereIn('event_type', ['transfer_out','transfer_in','admin_fund']);

        if ($fromTs !== null) { $query->where('occurred_at', '>=', $fromTs); }
        if ($toTs !== null) { $query->where('occurred_at', '<=', $toTs); }

        $items = $query->orderBy('id', 'desc')->paginate(50);

        $cashboxUsers = CashboxUser::with('user')->with('cashbox')
            ->whereIn('user_id', $items->pluck('user_id')->unique())
            ->get()
            ->keyBy('user_id');

        $pairs = CashboxLedger::whereIn('event_id', $items->pluck('event_id')->unique())
            ->whereIn('event_type', ['transfer_out','transfer_in','admin_fund'])
            ->get()
            ->groupBy('event_id');

        // Per-cashier aggregated summary over full filtered range
        $summaryList = collect();
        $summaryUsers = collect();
        if ($request->get('calc')) {
            $agg = CashboxLedger::select('user_id', 'event_type', DB::raw('SUM(amount) as s'))
                ->where('company_id', Auth::user()->company_id)
                ->whereIn('event_type', ['transfer_out','transfer_in','admin_fund']);
            if ($fromTs !== null) { $agg->where('occurred_at', '>=', $fromTs); }
            if ($toTs !== null) { $agg->where('occurred_at', '<=', $toTs); }
            $rows = $agg->groupBy('user_id', 'event_type')->get();

            $byUser = [];
            foreach ($rows as $r) {
                $uid = $r->user_id;
                if (!isset($byUser[$uid])) {
                    $byUser[$uid] = [
                        'user_id' => $uid,
                        'admin_fund' => 0.0,
                        'transfer_out' => 0.0,
                        'transfer_in' => 0.0,
                        'grand_total' => 0.0,
                    ];
                }
                if ($r->event_type === 'admin_fund') { $byUser[$uid]['admin_fund'] += (float)$r->s; }
                if ($r->event_type === 'transfer_out') { $byUser[$uid]['transfer_out'] += (float)$r->s; }
                if ($r->event_type === 'transfer_in') { $byUser[$uid]['transfer_in'] += (float)$r->s; }
            }
            foreach ($byUser as &$entry) {
                $entry['grand_total'] = (float)$entry['admin_fund'] + (float)$entry['transfer_out'] + (float)$entry['transfer_in'];
            }
            unset($entry);
            $summaryList = collect(array_values($byUser))->sortByDesc('grand_total');

            $summaryUsers = CashboxUser::with('user')->with('cashbox')
                ->whereIn('user_id', $summaryList->pluck('user_id')->all())
                ->get()
                ->keyBy('user_id');
        }

        // Compute total amount over the entire filtered range (unique transfers, no double counting)
        $total = null;
        if ($request->get('calc')) {
            $sumQuery = CashboxLedger::where('company_id', Auth::user()->company_id)
                ->whereIn('event_type', ['transfer_out','admin_fund']);
            if ($fromTs !== null) { $sumQuery->where('occurred_at', '>=', $fromTs); }
            if ($toTs !== null) { $sumQuery->where('occurred_at', '<=', $toTs); }
            $total = (float)$sumQuery->sum('amount');
        }

        return view('admin.transfer.index', [
            'items' => $items,
            'cashboxUsers' => $cashboxUsers,
            'pairs' => $pairs,
            'summaryList' => $summaryList,
            'summaryUsers' => $summaryUsers,
            'total' => $total,
        ]);
    }
}


