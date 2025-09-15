<?php

namespace App\Http\Controllers\Admin;

use App\Constants;
use App\Exports\CashboxReport;
use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\Loan;
use App\Models\User;
use App\Models\UserCashbox;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExcelController extends Controller
{
    public function cashbox(Request $request) {
        if ($request->get('cashbox') > 0) {
            $cashbox = Cashbox::where('id', $request->get('cashbox'))->firstOrFail();
            $loansQuery = Loan::where('cashbox_id', $request->get('cashbox'))
                ->where('lend_date', '>=', strtotime($request->get('from')))
                ->where('lend_date', '<=', strtotime($request->get('to')));
            $paymentsQuery = DB::table('payments')
                ->join('loans', 'payments.loan_id', '=', 'loans.id')
                ->join('loaners', 'loans.loaner_id', '=', 'loaners.id')
                ->whereRaw('loans.deleted_at IS NULL')
                ->whereRaw('payments.deleted_at IS NULL')
                ->where('payments.cashbox_id', $request->get('cashbox'))
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.paid_date', '>=', strtotime($request->get('from')))
                ->where('payments.paid_date', '<=', strtotime($request->get('to')));

            if($request->get('audit') == 'yes' || auth()->user()->isAudit()) {
                $loansQuery->where('in_audit', true);
                $paymentsQuery->where('loans.in_audit', true);
            }

            $payments = $paymentsQuery->get();
            $loans = $loansQuery->get();

            $transactions = [];

            foreach($loans as $loan) {
                $date = date("Y-m-d", $loan->created_at->timestamp);
                $transactions[$date][] = [
                    'document_no' => $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
                    'loaner' => $loan->loaner->full_name,
                    'sum' => $loan->initial_sum,
                    'interest_sum' => 0,
                    'principal_sum' => 0,
                    'close_sum' => 0
                ];
            }

            foreach($payments as $payment) {
                $date = date("Y-m-d", $payment->paid_date);
                if(isset($transactions[$date][$payment->uuid])) {
                    if($payment->type == Constants::PAYMENT_INTEREST ) {
                        $transactions[$date][$payment->uuid]['interest_sum'] = $payment->sum;
                    } else {
                        $transactions[$date][$payment->uuid]['principal_sum'] = $payment->sum;
                    }
                } else {
                    $interestSum = 0;
                    $closeSum = 0;
                    $principalSum = 0;

                    if($payment->type == Constants::PAYMENT_PRINCIPAL ) {
                        if($payment->left_sum == 0) {
                            if($payment->paid_date == $payment->last_principal_payment_date) {
                                $closeSum = $payment->sum;
                            } else {
                                $principalSum = $payment->sum;
                            }
                        } else {
                            $principalSum = $payment->sum;
                        }
                    } else {
                        $interestSum = $payment->sum;
                    }

                    $transactions[$date][$payment->uuid] = [
                        'document_no' => $payment->in_audit ? $payment->audit_document_no : $payment->document_no,
                        'loaner' => $payment->full_name,
                        'interest_sum' => $interestSum,
                        'principal_sum' => $principalSum,
                        'close_sum' => $closeSum,
                        'sum' => 0,
                    ];
                }
            }

            asort($transactions);

            return Excel::download(new CashboxReport([
                'transactions' => $transactions,
                'cashbox' => $cashbox->address,
            ]), 'invoices.xlsx');
        }

        die('error');
    }
}
