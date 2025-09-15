<?php

namespace App\Http\Controllers\Admin;

 
use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\IncassationTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
 

class IncassationHistoryController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $query = IncassationTransfer::where('company_id', Auth::user()->company_id);

        if($from || $to) {
            if(!$from) { $from = $to; }
            if(!$to) { $to = $from; }
            $fromTs = strtotime($from . ' 00:00:00');
            $toTs = strtotime($to . ' 23:59:59');
            $query->whereBetween('created_at', [$fromTs, $toTs]);
        }

        $items = $query->orderBy('cashbox_id')->orderBy('id','desc')->paginate(100);
        $cashboxes = Cashbox::whereIn('id', $items->pluck('cashbox_id')->unique())->get()->keyBy('id');
        $userIds = $items->pluck('incassator_id')->merge($items->pluck('cashier_id'))->filter()->unique();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

        // Latest log per transfer (to detect explicit rejections via 'reset')
        $latestLogs = collect();
        try {
            $logs = DB::table('incassation_transfer_logs')
                ->whereIn('incassation_transfer_id', $items->pluck('id'))
                ->orderBy('created_at','desc')
                ->get();
            foreach ($logs as $log) {
                if (!$latestLogs->has($log->incassation_transfer_id)) {
                    $latestLogs->put($log->incassation_transfer_id, $log);
                }
            }
        } catch (\Throwable $e) { $latestLogs = collect(); }

        // XLSX export removed per request

        return view('admin.incassation.index', [
            'items' => $items,
            'cashboxes' => $cashboxes,
            'users' => $users,
            'latestLogs' => $latestLogs,
        ]);
    }
}


