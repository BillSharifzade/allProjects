<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\IncassationTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IncassationTransferLog;

class IncassationAcceptController extends Controller
{
    public function index()
    {
        // Delivered and awaiting acceptance
        $items = IncassationTransfer::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
            ->where('delivered_by_incassator', true)
            ->where('accepted_by_cashier', false)
            ->orderBy('id','desc')->paginate(100);

        // Not yet delivered (to be delivered by incassator)
        $toDeliver = IncassationTransfer::where('company_id', Auth::user()->company_id)
            ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
            ->where('accepted_by_cashier', false)
            ->where('delivered_by_incassator', false)
            ->orderBy('id','desc')->get();

        return view('cashbox.incassation.accept', [ 'items' => $items, 'toDeliver' => $toDeliver ]);
    }

    public function accept(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) { $ids = []; }
        foreach ($ids as $id) {
            DB::transaction(function() use ($id) {
                $affected = DB::table('incassation_transfers')
                    ->where('id', (int)$id)
                    ->where('company_id', Auth::user()->company_id)
                    ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
                    // must be delivered by incassator and not accepted yet
                    ->where('delivered_by_incassator', true)
                    ->where('accepted_by_cashier', false)
                    ->update([
                        'accepted_by_cashier' => true,
                        'accepted_at' => time(),
                        'cashier_id' => Auth::id(),
                    ]);
                if ($affected) {
                    IncassationTransferLog::create([
                        'company_id' => Auth::user()->company_id,
                        'incassation_transfer_id' => (int)$id,
                        'actor_user_id' => Auth::id(),
                        'action' => 'accept',
                        'picked_by_incassator' => true,
                        'delivered_by_incassator' => true,
                        'accepted_by_cashier' => true,
                        'created_at' => time(),
                    ]);
                }
            });
        }
        return redirect()->back();
    }

    public function notDelivered(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) { $ids = []; }
        foreach ($ids as $id) {
            DB::transaction(function() use ($id) {
                DB::table('incassation_transfers')
                    ->where('id', (int)$id)
                    ->where('company_id', Auth::user()->company_id)
                    ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
                    ->update([
                        'accepted_by_cashier' => false,
                        'accepted_at' => 0,
                        'cashier_id' => null,
                        'delivered_by_incassator' => false,
                        'delivered_at' => 0,
                        'picked_by_incassator' => false,
                        'picked_at' => 0,
                        'incassator_id' => null,
                    ]);
                IncassationTransferLog::create([
                    'company_id' => Auth::user()->company_id,
                    'incassation_transfer_id' => (int)$id,
                    'actor_user_id' => Auth::id(),
                    'action' => 'reset',
                    'picked_by_incassator' => false,
                    'delivered_by_incassator' => false,
                    'accepted_by_cashier' => false,
                    'created_at' => time(),
                ]);
            });
        }
        return redirect()->back();
    }
}


