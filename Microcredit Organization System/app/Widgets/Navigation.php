<?php

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CashierShift;
use App\Models\CashboxLedger;

class Navigation extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        if(Auth::user()->isIncassator()) {
            // Incassator has its own mobile layout and navbar; no global nav
            return '';
        } else if(Auth::user()->isCashier() || Auth::user()->isCashierAudit()) {
            $balance = null;
            try {
                $cashboxId = Auth::user()->cashboxUser->cashbox_id;
                $shift = CashierShift::where('user_id', Auth::user()->id)
                    ->where('cashbox_id', $cashboxId)
                    ->where('closed_at', 0)
                    ->orderBy('id', 'desc')
                    ->first();
                $nextOpening = null;
                if($shift) {
                    $delta = CashboxLedger::where('shift_id', $shift->id)
                        ->select(DB::raw('COALESCE(SUM(direction * amount),0) as delta'))
                        ->value('delta');
                    $balance = (float)$shift->opening_balance + (float)$delta;
                } else {
                    $prev = CashierShift::where('user_id', Auth::user()->id)
                        ->where('cashbox_id', $cashboxId)
                        ->where('closed_at', '>', 0)
                        ->orderBy('id', 'desc')
                        ->first();
                    $nextOpening = $prev ? (float)$prev->closing_balance : 0.0;
                }
            } catch (\Throwable $e) {
                $balance = null;
                $nextOpening = null;
            }

            $hasDelivered = false;
            try {
                $hasDelivered = \App\Models\IncassationTransfer::where('company_id', Auth::user()->company_id)
                    ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
                    ->where('delivered_by_incassator', true)
                    ->where('accepted_by_cashier', false)
                    ->exists();
            } catch (\Throwable $e) { $hasDelivered = false; }

            return view('widgets.cashier_navigation', [
                'config' => $this->config,
                'balance' => $balance,
                'shift' => $shift ?? null,
                'nextOpening' => $nextOpening,
                'hasDelivered' => $hasDelivered,
            ]);
        } else if(Auth::user()->isAdmin()) {
            return view('widgets.admin_navigation', [
                'config' => $this->config,
            ]);
        } else {
            return view('widgets.reporter_navigation', [
                'config' => $this->config,
            ]);
        }
    }
}
