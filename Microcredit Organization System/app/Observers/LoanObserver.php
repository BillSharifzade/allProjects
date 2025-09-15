<?php

namespace App\Observers;

use App\Models\InterestRate;
use App\Models\Loan;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function created(Loan $loan)
    {
        //
    }

    /**
     * Handle the Loan "creating" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function creating(Loan $loan) {
        if($loan->left_sum > 0) {
            $interestRate = InterestRate::where('sum_from', '<=',  $loan->left_sum)
                ->where('sum_to', '>=',  $loan->left_sum)
                ->first();
            if(!$interestRate) {
                abort(404, 'Ошибка процентовки');
            }

            $loan->interest_rate = $interestRate->rate;
        }
    }

    /**
     * Handle the Loan "updating" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function updating(Loan $loan) {
        if($loan->left_sum > 0) {
            $interestRate = InterestRate::where('sum_from', '<=',  $loan->left_sum)
                ->where('sum_to', '>=',  $loan->left_sum)
                ->first();
            if(!$interestRate) {
                abort(404, 'Ошибка процентовки');
            }

            $loan->interest_rate = $interestRate->rate;
        }
    }

    /**
     * Handle the Loan "updated" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function updated(Loan $loan)
    {
        //
    }

    /**
     * Handle the Loan "deleted" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function deleted(Loan $loan)
    {
        try {
            $snapshot = self::buildSnapshot($loan);
            \App\Models\Archive::create([
                'company_id' => (int)$loan->company_id,
                'loan_id' => (int)$loan->id,
                'type' => 'deleted',
                'snapshot' => json_encode($snapshot, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'archived_at' => time(),
                'created_at' => time(),
            ]);
        } catch (\Throwable $e) {}
    }

    /**
     * Handle the Loan "restored" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function restored(Loan $loan)
    {
        //
    }

    /**
     * Handle the Loan "force deleted" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function forceDeleted(Loan $loan)
    {
        try {
            $snapshot = self::buildSnapshot($loan);
            \App\Models\Archive::create([
                'company_id' => (int)$loan->company_id,
                'loan_id' => (int)$loan->id,
                'type' => 'deleted',
                'snapshot' => json_encode($snapshot, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                'archived_at' => time(),
                'created_at' => time(),
            ]);
        } catch (\Throwable $e) {}
    }

    /**
     * Handle the Loan "retrieved" event.
     *
     * @param  \App\Models\Loan  $loan
     * @return void
     */
    public function retrieved(Loan $loan) {

        if($loan->left_sum > 0) {
            $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
            $interestAccumulationDate = $loan->last_principal_payment_date > 0 ? $loan->last_principal_payment_date : $loan->interest_accumulation_date;
            $rate = isset($loan->interest_rate) ? $loan->interest_rate : (isset($loan->interestRate) ? $loan->interestRate : 0);
            $dailyInterstRate = round($rate / 30, 2);
            $dailyInterest = round(($rate / 30 / 100) * $loan->left_sum, 5);

            $paidDays = $dailyInterest > 0 ? round($loan->latest_interest_payments_sum / $dailyInterest, 0) : 0;
            $unpaidDays = round(($today-$interestAccumulationDate) / (24 * 3600) - $paidDays, 0);
            $unpaidInterest = round($unpaidDays * $dailyInterest, 2);

            $loan->grace_period = ($loan->interest_accumulation_date - $loan->lend_date) / (24 * 3600);
            $loan->unpaid_interest = $unpaidInterest > 0 ? $unpaidInterest : 0;
            $loan->unpaid_days = $unpaidDays > 0 ? $unpaidDays : 0;
            $loan->daily_interest = $dailyInterest;
            $loan->paid_days = $paidDays;
            $loan->monthly_interest = round($dailyInterest * 30 );
            $loan->daily_interest_rate = $dailyInterstRate;
        }
    }

    private static function buildSnapshot(Loan $loan): array
    {
        $loan->loadMissing('loaner','jewelries','auto','phone','payments','cashbox','user');
        return [
            'loan' => $loan->toArray(),
            'loaner' => optional($loan->loaner)->toArray(),
            'jewelries' => $loan->jewelries ? $loan->jewelries->toArray() : [],
            'auto' => optional($loan->auto)->toArray(),
            'phone' => optional($loan->phone)->toArray(),
            'payments' => $loan->payments ? $loan->payments->toArray() : [],
            'cashbox' => optional($loan->cashbox)->toArray(),
            'user' => optional($loan->user)->toArray(),
        ];
    }
}
