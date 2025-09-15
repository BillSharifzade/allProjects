<?php

namespace App\Http\Controllers\Reporter;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request) {
        if($request->get('loanId') > 0) {
            return view('reporter.payment.loan', [
                'loan' =>  Loan::with(['payments' => function($query){
                    $query->orderBy('id', 'desc');
                }])->where('id', $request->get('loanId'))->firstOrFail()
            ]);
        }

        $payments = [];
        $loans = [];
        $principalPaymentsTotalSum = 0;
        $interestPaymentsTotalSum = 0;

        if($request->get('cashbox') > 0) {
            Cashbox::where('id', $request->get('cashbox'))->firstOrFail();

            $payments = DB::table('payments')
                ->join('loans', 'payments.loan_id', '=', 'loans.id')
                ->whereRaw('loans.deleted_at IS NULL')
                ->whereRaw('payments.deleted_at IS NULL')
                ->where('payments.cashbox_id', $request->get('cashbox'))
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.paid_date', '>=', strtotime($request->get('from')))
                ->where('payments.paid_date', '<=', strtotime($request->get('to')));

            if($request->get('audit') == 'yes' || auth()->user()->isAudit()) {
                $payments->where('loans.in_audit', true);
            }

            $interestPaymentsTotalSum = clone $payments;
            $interestPaymentsTotalSum = $interestPaymentsTotalSum
                ->where('payments.type', Constants::PAYMENT_INTEREST)
                ->sum('sum');

            $principalPaymentsTotalSum = clone $payments;
            $principalPaymentsTotalSum = $principalPaymentsTotalSum
                ->where('payments.type', Constants::PAYMENT_PRINCIPAL)
                ->sum('sum');

            $payments = $payments
                ->orderBy('payments.id', 'desc')
                ->paginate(50);

            $loans = Loan::with('loaner')
                ->with('cashbox')
                ->whereIn('id', $payments->map(function($payment){
                    return $payment->loan_id;
                }))
                ->get();
        }

        return view('reporter.payment.index', [
            'payments' => $payments,
            'loans' => $loans,
            'principalPaymentsTotalSum' => $principalPaymentsTotalSum,
            'interestPaymentsTotalSum' => $interestPaymentsTotalSum,
        ]);
    }
}
