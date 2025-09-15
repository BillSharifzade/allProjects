<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashboxUser;
use Illuminate\Support\Facades\Auth;

class TransferHistoryController extends Controller
{
    public function index()
    {
        // Clamp today to full day range for correct inclusive filtering
        $todayStart = strtotime(date('Y-m-d'));
        $todayEnd = $todayStart + 86399;

        // Current cashier can only see today's transfers
        $items = CashboxLedger::where('company_id', Auth::user()->company_id)
            ->whereIn('event_type', ['transfer_out','transfer_in','admin_fund'])
            ->whereBetween('occurred_at', [$todayStart, $todayEnd])
            ->orderBy('id', 'desc')
            ->paginate(50);

        $cashboxUsers = CashboxUser::with('user')->with('cashbox')
            ->whereIn('user_id', $items->pluck('user_id')->unique())
            ->get()
            ->keyBy('user_id');

        return view('cashbox.transfer.index', [
            'items' => $items,
            'cashboxUsers' => $cashboxUsers,
        ]);
    }
}


