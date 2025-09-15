<?php

namespace App\Schedules;

use App\Constants;
use App\Facades\Sms;
use App\Models\Loan;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\DB;

class SmsSend {

    public function __invoke() {
        $defaultCompanyId = 1;
        $overdueTime = time() - 28 * 24 * 3600;

        $notificationRecipients = NotificationRecipient::where('processed_at', 0)
            ->where('cancelled_at', 0)
            ->limit(6)
            ->get();

        if(count($notificationRecipients) > 0) {
            $loans = Loan::withoutGlobalScope(new CompanyScope)
                ->where('company_id', $defaultCompanyId)
                ->whereIn('id', $notificationRecipients->map(function($item){
                    return $item->loan_id;
                }))
                ->get();

            foreach($notificationRecipients as $notificationRecipient) {
                foreach($loans as $loan) {
                    if($loan->id != $notificationRecipient->loan_id) {
                        continue;
                    }

                    $sendSms = false;

                    if(($loan->interest_accumulation_date < $overdueTime && $loan->last_interest_payment_date == 0) ||
                        ($loan->last_interest_payment_date > 0 && $loan->last_interest_payment_date < $overdueTime)) {

                            if(round($loan->unpaid_interest) > 0) {
                                $sendSms = true;
                            } else {
                                $sendSms = false;
                            }

                        /*$notificationRecipient->processed_at = time();
                        $notificationRecipient->save();

                        $message = 'Фоизи карз: ' . round($loan->unpaid_interest) . PHP_EOL;

                        if($loan->loaner->phone1 != '') {
                            Sms::send($loan->loaner->phone1, $message, ['company_id' => $defaultCompanyId]);
                        }*/
                    } else {
                        $sendSms = false;
                        /*$notificationRecipient->cancelled_at = time();
                        $notificationRecipient->save();*/
                    }

                    if($sendSms) {
                        $notificationRecipient->processed_at = time();
                        $notificationRecipient->save();

                        $message = 'Фоизи карз: ' . round($loan->unpaid_interest) . PHP_EOL;

                        if($loan->loaner->phone1 != '') {
                            Sms::send($loan->loaner->phone1, $message, ['company_id' => $defaultCompanyId]);
                        }
                    } else {
                        $notificationRecipient->cancelled_at = time();
                        $notificationRecipient->save();
                    }
                }
            }
        }
    }
}
