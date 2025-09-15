<?php

namespace App\Http\Controllers\Incassator;

use App\Http\Controllers\Controller;
use App\Models\IncassationTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IncassationTransferLog;

class ToDeliverController extends Controller
{
    public function index()
    {
        $items = IncassationTransfer::where('company_id', Auth::user()->company_id)
            ->where('delivered_by_incassator', false)
            ->orderBy('id', 'desc')
            ->paginate(100);

        return view('incassator.todeliver.index', [ 'items' => $items ]);
    }

    public function pick(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) { $ids = []; }

        foreach ($ids as $id) {
            DB::transaction(function() use ($id) {
                $affected = DB::table('incassation_transfers')
                    ->where('id', (int)$id)
                    ->where('company_id', Auth::user()->company_id)
                    ->where('picked_by_incassator', false)
                    ->where('delivered_by_incassator', false)
                    ->where('accepted_by_cashier', false)
                    ->update([
                        'picked_by_incassator' => true,
                        'picked_at' => time(),
                        'incassator_id' => Auth::id(),
                    ]);
                if ($affected) {
                    IncassationTransferLog::create([
                        'company_id' => Auth::user()->company_id,
                        'incassation_transfer_id' => (int)$id,
                        'actor_user_id' => Auth::id(),
                        'action' => 'pick',
                        'picked_by_incassator' => true,
                        'delivered_by_incassator' => false,
                        'accepted_by_cashier' => false,
                        'created_at' => time(),
                    ]);
                }
            });
        }

        return redirect()->back();
    }

    public function deliver(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) { $ids = []; }
        foreach ($ids as $id) {
            DB::transaction(function() use ($id) {
                $affected = DB::table('incassation_transfers')
                    ->where('id', (int)$id)
                    ->where('company_id', Auth::user()->company_id)
                    // must be picked first by this incassator
                    ->where('picked_by_incassator', true)
                    ->where('incassator_id', Auth::id())
                    ->where('delivered_by_incassator', false)
                    ->update([
                        'delivered_by_incassator' => true,
                        'delivered_at' => time(),
                    ]);
                if ($affected) {
                    IncassationTransferLog::create([
                        'company_id' => Auth::user()->company_id,
                        'incassation_transfer_id' => (int)$id,
                        'actor_user_id' => Auth::id(),
                        'action' => 'deliver',
                        'picked_by_incassator' => true,
                        'delivered_by_incassator' => true,
                        'accepted_by_cashier' => false,
                        'created_at' => time(),
                    ]);
                }
            });
        }
        return redirect()->route('inc-history');
    }
}


