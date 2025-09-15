<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanSale;
use Illuminate\Support\Facades\Auth;

class SaleHistoryController extends Controller
{
    public function index()
    {
        $todayStart = strtotime(date('Y-m-d'));
        $todayEnd = $todayStart + 86399;

        $items = LoanSale::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
            ->whereBetween('sold_at', [$todayStart, $todayEnd])
            ->orderBy('id', 'desc')
            ->paginate(50);

        $loans = Loan::whereIn('id', $items->pluck('loan_id')->unique())->get()->keyBy('id');

        return view('cashbox.sale.index', [
            'items' => $items,
            'loans' => $loans,
        ]);
    }
}


