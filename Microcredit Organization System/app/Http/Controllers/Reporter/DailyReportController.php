<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\CashboxUser;
use App\Models\CashierShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $items = CashboxUser::with('user')->with('cashbox')->orderBy('id','desc')->paginate(50);
        $latestShifts = CashierShift::whereIn('user_id', $items->pluck('user_id')->unique())
            ->whereIn('cashbox_id', $items->pluck('cashbox_id')->unique())
            ->orderBy('id','desc')->get()->groupBy(function($s){ return $s->user_id.':'.$s->cashbox_id; });
        return view('reporter.daily_report.index', [ 'items' => $items, 'latestShifts' => $latestShifts ]);
    }

    public function download(Request $request, CashierShift $shift)
    {
        // Same company hard guard
        if ((int)$shift->company_id !== (int)Auth::user()->company_id) { abort(403); }
        if ((int)$shift->closed_at <= 0) { abort(400, 'Смена не закрыта'); }
        // Delegate to Admin controller to avoid duplication
        return app(\App\Http\Controllers\Admin\DailyReportController::class)->download($request, $shift);
    }
}


