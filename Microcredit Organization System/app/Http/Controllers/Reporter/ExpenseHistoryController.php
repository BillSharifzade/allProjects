<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\CashboxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class ExpenseHistoryController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $items = new LengthAwarePaginator([], 0, 50);
        $byCategory = collect();

        if($from || $to) {
            if(!$from) { $from = $to; }
            if(!$to) { $to = $from; }
            $fromTs = strtotime($from . ' 00:00:00');
            $toTs = strtotime($to . ' 23:59:59');

            $query = Expense::where('company_id', Auth::user()->company_id)
                ->where('occurred_at', '>=', (int)$fromTs)
                ->where('occurred_at', '<=', (int)$toTs);

            $items = $query->orderBy('id', 'desc')->paginate(50);

            $byCategory = Expense::selectRaw('category, SUM(amount) as sum')
                ->where('company_id', Auth::user()->company_id)
                ->where('occurred_at', '>=', (int)$fromTs)
                ->where('occurred_at', '<=', (int)$toTs)
                ->groupBy('category')
                ->orderBy('category')
                ->get();
        }

        $cashboxUsers = CashboxUser::with('user')->with('cashbox')
            ->whereIn('user_id', $items->pluck('user_id')->unique())
            ->get()
            ->keyBy('user_id');

        return view('reporter.expense.index', [
            'items' => $items,
            'cashboxUsers' => $cashboxUsers,
            'byCategory' => $byCategory,
        ]);
    }
}


