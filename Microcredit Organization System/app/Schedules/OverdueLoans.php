<?php

namespace App\Schedules;

use App\Models\Loan;
use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Support\Facades\DB;

class OverdueLoans {

    public function __invoke() {
        $defaultCompanyId = 1;
        $overdueTime = time() - 28 * 24 * 3600;
        $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));

        $todayNotification = Notification::where('date', $today)
            ->where('company_id', $defaultCompanyId)
            ->first();

        if(!$todayNotification) {
            $loans = Loan::where('closed_at', 0)
                ->whereRaw('((interest_accumulation_date < ' . $overdueTime . ' AND last_interest_payment_date = 0) OR (last_interest_payment_date > 0 AND last_interest_payment_date < ' . $overdueTime . '))')
                ->where('company_id', $defaultCompanyId)
                ->get();

            if(count($loans) == 0) {
                return;
            }

            DB::beginTransaction();

            $todayNotification = new Notification();
            $todayNotification->company_id = $defaultCompanyId;
            $todayNotification->date = $today;

            if($todayNotification->save() === false) {
                DB::rollBack();
                return;
            }

            $insertData = [];

            foreach($loans as $loan) {
                $insertData[] = [
                    'notification_id' => $todayNotification->id,
                    'company_id' => $defaultCompanyId,
                    'loan_id' => $loan->id,
                    'created_at' => time(),
                ];
            }

            if(NotificationRecipient::insert($insertData) === false) {
                DB::rollBack();
            }

            DB::commit();
        }
    }
}
