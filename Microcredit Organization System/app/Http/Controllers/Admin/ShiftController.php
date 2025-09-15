<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashierShift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $shifts = CashierShift::orderBy('id', 'desc')->paginate(50);
        $shiftIds = $shifts->pluck('id');
        $deltas = CashboxLedger::whereIn('shift_id', $shiftIds)
            ->selectRaw('shift_id, COALESCE(SUM(direction * amount),0) as delta')
            ->groupBy('shift_id')
            ->get()
            ->keyBy('shift_id');

        return view('admin.shift.index', [
            'shifts' => $shifts,
            'deltas' => $deltas,
        ]);
    }
}


