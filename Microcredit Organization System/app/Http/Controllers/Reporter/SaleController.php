<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanSale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $items = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50);

        if($from || $to) {
            if(!$from) { $from = $to; }
            if(!$to) { $to = $from; }
            $fromTs = strtotime($from . ' 00:00:00');
            $toTs = strtotime($to . ' 23:59:59');
            $items = LoanSale::with('cashbox')->whereBetween('sold_at', [$fromTs, $toTs])
                ->orderBy('id', 'desc')
                ->paginate(50);
        }

        $loans = Loan::whereIn('id', $items->pluck('loan_id')->unique())->get()->keyBy('id');

        return view('reporter.sale.index', [
            'items' => $items,
            'loans' => $loans,
        ]);
    }
}


