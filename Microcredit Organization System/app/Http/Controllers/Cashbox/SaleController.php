<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\CashboxLedger;
use App\Models\CashierShift;
use App\Models\Loan;
use App\Models\LoanSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    private function currentShift(): ?CashierShift
    {
        $user = Auth::user();
        $cashboxId = $user->cashboxUser->cashbox_id;
        return CashierShift::where('user_id', $user->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function store(Request $request, Loan $loan)
    {
        if ($loan->closed_at != 0) {
            abort(404, 'LOAN_CLOSED');
        }

        // Enforce 70+ unpaid days on server side as well
        $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
        $expr = "GREATEST(0, FLOOR(({$today} - (CASE WHEN last_principal_payment_date > 0 THEN last_principal_payment_date ELSE interest_accumulation_date END))/86400) - (CASE WHEN interest_rate > 0 AND left_sum > 0 THEN FLOOR(latest_interest_payments_sum / ((interest_rate/30/100) * left_sum)) ELSE 0 END))";
        $canSell = Loan::where('id', $loan->id)->whereRaw($expr . ' >= 70')->exists();
        if (!$canSell) {
            return redirect()->back()->withErrors(['Продажа доступна только при 70+ неоплаченных дней'])->withInput();
        }

        $isGold = ((int)$loan->collateral_type === 1);
        if ($isGold) {
            // Validate modal inputs for gold proceeds (prices by purity)
            $validated = $request->validate([
                'price_375' => ['required','numeric','gte:0'],
                'price_585' => ['required','numeric','gte:0'],
                'price_750' => ['required','numeric','gte:0'],
                'price_875' => ['required','numeric','gte:0'],
            ]);
        } else {
            // Auto / Phone: one explicit proceeds input
            $validated = $request->validate([
                'proceeds' => ['required','numeric','gte:0'],
            ]);
        }

        $shift = $this->currentShift();
        if (!$shift) {
            return redirect()->back()->withErrors(['Нет открытой смены'])->withInput();
        }

        $eventId = uniqid('sale_', true);

        DB::beginTransaction();

        // Snapshot
        $snapshotLeft = (float)$loan->left_sum;
        $snapshotLPPD = (int)$loan->last_principal_payment_date;
        $snapshotLIPD = (int)$loan->last_interest_payment_date;
        $snapshotLatestIP = (float)$loan->latest_interest_payments_sum;

        // Compute proceeds
        $p375 = 0.0; $p585 = 0.0; $p750 = 0.0; $p875 = 0.0; $proceeds = 0.0;
        if ($isGold) {
            $p375 = (float)$validated['price_375'];
            $p585 = (float)$validated['price_585'];
            $p750 = (float)$validated['price_750'];
            $p875 = (float)$validated['price_875'];

            $weights = [375 => 0.0, 585 => 0.0, 750 => 0.0, 875 => 0.0];
            foreach ($loan->jewelries as $j) {
                $purity = (int)$j->purity;
                if (isset($weights[$purity])) {
                    $weights[$purity] += (float)$j->weight;
                }
            }
            $proceeds = round($weights[375]*$p375 + $weights[585]*$p585 + $weights[750]*$p750 + $weights[875]*$p875, 2);
        } else {
            $proceeds = round((float)$validated['proceeds'], 2);
        }

        // Amount required to fully close = principal left + unpaid interest now
        $unpaidInterest = (float)$loan->unpaid_interest;
        $principalLeft = (float)$loan->left_sum;
        $requiredToClose = round($principalLeft + $unpaidInterest, 2);
        $profit = round($proceeds - $requiredToClose, 2);

        // Create sale record
        $sale = new LoanSale();
        $sale->company_id = Auth::user()->company_id;
        $sale->cashbox_id = $shift->cashbox_id;
        $sale->user_id = Auth::id();
        $sale->shift_id = $shift->id;
        $sale->loan_id = $loan->id;
        $sale->sold_at = time();
        $sale->amount_principal = $principalLeft;
        $sale->amount_interest = $unpaidInterest;
        $sale->total_amount = $requiredToClose;
        $sale->event_id = $eventId;
        $sale->prev_left_sum = $snapshotLeft;
        $sale->prev_last_principal_payment_date = $snapshotLPPD;
        $sale->prev_last_interest_payment_date = $snapshotLIPD;
        $sale->prev_latest_interest_payments_sum = $snapshotLatestIP;
        $sale->price_375 = $p375;
        $sale->price_585 = $p585;
        $sale->price_750 = $p750;
        $sale->price_875 = $p875;
        $sale->proceeds_amount = $proceeds;
        $sale->profit_amount = $profit;
        $sale->created_at = time();
        $sale->save();

        // Close out loan financials: set left_sum to 0, mark last payment dates to today
        $loan->left_sum = 0;
        $loan->last_principal_payment_date = $today;
        $loan->last_interest_payment_date = $today;
        $loan->latest_interest_payments_sum = $snapshotLatestIP + $unpaidInterest;
        $loan->save();

        // Ledger entries:
        // 1) Inflow equal to loan close requirement
        CashboxLedger::create([
            'company_id' => Auth::user()->company_id,
            'cashbox_id' => $shift->cashbox_id,
            'user_id' => Auth::id(),
            'shift_id' => $shift->id,
            'event_type' => 'loan_sale',
            'event_id' => $eventId,
            'direction' => +1,
            'amount' => $requiredToClose,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);

        // 2) Profit/loss adjustment to balance
        if ($profit != 0.0) {
            CashboxLedger::create([
                'company_id' => Auth::user()->company_id,
                'cashbox_id' => $shift->cashbox_id,
                'user_id' => Auth::id(),
                'shift_id' => $shift->id,
                'event_type' => 'loan_sale_profit',
                'event_id' => $eventId,
                'direction' => $profit >= 0 ? +1 : -1,
                'amount' => abs($profit),
                'occurred_at' => time(),
                'created_at' => time(),
            ]);
        }

        DB::commit();

        return redirect('/print/sale?event_id=' . urlencode($eventId));
    }
}


