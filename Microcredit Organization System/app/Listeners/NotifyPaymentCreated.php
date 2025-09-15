<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use App\Facades\Sms;
use App\Models\Loan;

class NotifyPaymentCreated
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PaymentCreated $event)
    {
        $message = '';

        $loan = Loan::where('id', !is_null($event->interestPayment) ? $event->interestPayment->loan_id : $event->principalPayment->loan_id)->firstOrFail();

        if(!is_null($event->interestPayment)) {
            $message .= 'Проценты: ' . $event->interestPayment->sum . ' c.' . PHP_EOL;
        }

        if(!is_null($event->principalPayment)) {
            $message .= 'Основной кредит: ' . $event->principalPayment->sum . ' c.' . PHP_EOL;
        }

        $message .= 'Остаток основного кредита: ' . (int)$loan->left_sum . ' c.';

        try {
            if($loan->loaner->phone1 != '') { @Sms::send($loan->loaner->phone1, $message); }
            if($loan->loaner->phone2 != '') { @Sms::send($loan->loaner->phone2, $message); }
            if($loan->loaner->phone3 != '') { @Sms::send($loan->loaner->phone3, $message); }
            if($loan->loaner->phone4 != '') { @Sms::send($loan->loaner->phone4, $message); }
        } catch (\Throwable $e) {
            // swallow to avoid any impact on payment UX
        }
    }
}
