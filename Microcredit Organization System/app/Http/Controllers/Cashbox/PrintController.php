<?php

namespace App\Http\Controllers\Cashbox;

use App\Constants;
use App\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\Loan;
use App\Models\LoanSale;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    public function sale(Request $request)
    {
        $eventId = $request->get('event_id');
        $sale = LoanSale::where('event_id', $eventId)->firstOrFail();
        $loan = Loan::where('id', $sale->loan_id)->firstOrFail();
        $cashbox = \App\Models\Cashbox::where('id', Auth::user()->cashboxUser->cashbox_id)->firstOrFail();

        $printText = str_replace([
            '[[company_name]]',
            '[[cashbox_name]]',
            '[[loaner_name]]',
            '[[loan_document_no]]',
            '[[sale_sum]]',
            '[[sale_date]]'
        ], [
            Auth::user()->company->name,
            $cashbox->name,
            $loan->loaner->full_name,
            $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            (int)$sale->total_amount,
            date('«d» m Y', $sale->sold_at)
        ], Auth::user()->company->payment_receipt_text);

        return view('cashbox.print.print', [
            'printText' => $printText . $printText,
        ]);
    }
    public function payment(Request $request) {
        $principalSum = 0;
        $interestSum = 0;
        $loan = null;
        $paidDate = 0;
        $cashbox = Cashbox::where('id', Auth::user()->cashboxUser->cashbox_id)->firstOrFail();

        $payments = Payment::where('uuid', $request->get('uuid'))->get();
        if(count($payments) == 0) {
            abort(404);
        }

        foreach($payments as $payment) {
            if(is_null($loan)) {
                $loan = Loan::where('id', $payment->loan_id)->firstOrFail();
            }

            $paidDate = $payment->paid_date;

            switch ($payment->type) {
                case Constants::PAYMENT_INTEREST:
                    $interestSum = $payment->sum;
                    break;
                case Constants::PAYMENT_PRINCIPAL:
                    $principalSum = $payment->sum;
                    break;
            }
        }

        $printText = str_replace([
            '[[company_name]]',
            '[[cashbox_name]]',
            '[[loaner_name]]',
            '[[loan_document_no]]',
            '[[principal_sum]]',
            '[[interest_sum]]',
            '[[loan_left_sum]]',
            '[[payment_date]]'
        ], [
            Auth::user()->company->name,
            $cashbox->name,
            $loan->loaner->full_name,
            $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            (int)$principalSum,
            (int)$interestSum,
            (int)$loan->left_sum,
            date('«d» m Y', $paidDate)
        ], Auth::user()->company->payment_receipt_text);

        return view('cashbox.print.print', [
            'printText' => $printText.$printText,
        ]);
    }

    public function receipt(Request $request) {
        $principalSum = 0;
        $interestSum = 0;
        $loan = null;
        $paidDate = 0;
        $documentNo = 0;
        $cashbox = Cashbox::where('id', Auth::user()->cashboxUser->cashbox_id)->firstOrFail();

        $payments = Payment::where('uuid', $request->get('uuid'))->get();
        if(count($payments) == 0) {
            abort(404);
        }

        foreach($payments as $payment) {
            if(is_null($loan)) {
                $loan = Loan::where('id', $payment->loan_id)->firstOrFail();
            }

            $paidDate = $payment->paid_date;
            $documentNo = $payment->document_no;
            switch ($payment->type) {
                case Constants::PAYMENT_INTEREST:
                    $interestSum = $payment->sum;
                    break;
                case Constants::PAYMENT_PRINCIPAL:
                    $principalSum = $payment->sum;
                    break;
            }
        }

        $printText = str_replace([
            '[[company_name]]',
            '[[cashbox_name]]',
            '[[loaner_name]]',
            '[[loan_document_no]]',
            '[[principal_sum]]',
            '[[interest_sum]]',
            '[[loan_left_sum]]',
            '[[payment_date]]'
        ], [
            Auth::user()->company->name,
            $cashbox->name,
            $loan->loaner->full_name,
            $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            (int)$principalSum,
            (int)$interestSum,
            (int)$loan->left_sum,
            date('«d» m Y', $paidDate)
        ], Auth::user()->company->payment_receipt_text);

        return view('cashbox.print.receipt_order', [
            'loaner_name' => $loan->loaner->full_name,
            'loan_document_no' => $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            'passport_number' => $loan->loaner->passport_number,
            'passport_issuer' => $loan->loaner->passport_issuer,
            'passport_issued_day' => $loan->loaner->passport_issued_day,
            'passport_issued_month' => $loan->loaner->passport_issued_month,
            'passport_issued_year' => $loan->loaner->passport_issued_year,
            'residence_address' => $loan->loaner->residence_address,
            'tin' => $loan->loaner->tin,
            'total_sum' => $principalSum+$interestSum,
            'total_sum_text' => Helpers::num2Text($principalSum+$interestSum),
            'loan_date' =>  date('«d» m Y', $loan->lend_date),
            'cashier_name' => Auth::user()->last_name . ' ' . Auth::user()->first_name,
            'cashbox_name' => $cashbox->name,
            'paid_date' => date('«d» m Y', $paidDate),
            'document_no' => $documentNo,
        ]);
    }

    public function withdrawal(Request $request, Loan $loan) {
        $cashbox = Cashbox::where('id', Auth::user()->cashboxUser->cashbox_id)->firstOrFail();

        return view('cashbox.print.withdrawal_slip', [
            'loaner_name' => $loan->loaner->full_name,
            'loan_document_no' => $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            'loaner_passport_number' => $loan->loaner->passport_number,
            'loaner_passport_issuer' => $loan->loaner->passport_issuer,
            'loaner_passport_issued_day' => $loan->loaner->passport_issued_day,
            'loaner_passport_issued_month' => $loan->loaner->passport_issued_month,
            'loaner_passport_issued_year' => $loan->loaner->passport_issued_year,
            'residence_address' => $loan->loaner->residence_address,
            'tin' => $loan->loaner->tin,
            'loan_date' =>  date('«d» m Y', $loan->lend_date),
            'cashbox_license' => $cashbox->license,
            'cashier_name' => Auth::user()->last_name . ' ' . Auth::user()->first_name,
            'cashier_license' => Auth::user()->cashboxUser->user_license,
            'loan_initial_sum' => $loan->initial_sum,
            'loan_daily_interest_rate' => $loan->daily_interest_rate,
            'loan_monthly_interest' => $loan->monthly_interest,
            'loan_initial_sum_text' => Helpers::num2Text($loan->initial_sum),
        ]);
    }

    public function loan(Request $request, Loan $loan) {
        $cashbox = Cashbox::where('id', Auth::user()->cashboxUser->cashbox_id)->firstOrFail();

        $searches = [
            '[[company_name]]',
            '[[loan_document_no]]',
            '[[cashbox_license]]',
            '[[cashier_name]]',
            '[[cashier_license]]',
            '[[loaner_name]]',
            '[[loaner_passport_number]]',
            '[[loaner_passport_issuer]]',
            '[[loaner_passport_issued_day]]',
            '[[loaner_passport_issued_month]]',
            '[[loaner_passport_issued_year]]',
            '[[loaner_residence_address]]',
            '[[loaner_tin]]',
            '[[loan_initial_sum]]',
            '[[loan_daily_interest_rate]]',
            '[[loan_monthly_interest]]',
            '[[loan_lend_date]]',
            '[[image]]',
            '[[vehicle_brand]]',
            '[[vehicle_year]]',
            '[[vehicle_color]]',
            '[[vehicle_mileage]]',
            '[[vehicle_transmission]]',
            '[[vehicle_plate_number]]',
            '[[vehicle_engine]]',
            '[[vehicle_description]]',
            '[[vehicle_gas]]',
            '[[loan_daily_interest]]',
            '[[loaner_phone1]]',
        ];

        $replaces = [
            Auth::user()->company->name,
            $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            $cashbox->license,
            Auth::user()->last_name . ' ' . Auth::user()->first_name,
            Auth::user()->cashboxUser->user_license,
            $loan->loaner->full_name,
            $loan->loaner->passport_number,
            $loan->loaner->passport_issuer,
            $loan->loaner->passport_issued_day,
            $loan->loaner->passport_issued_month,
            $loan->loaner->passport_issued_year,
            $loan->loaner->residence_address,
            $loan->loaner->tin,
            $loan->initial_sum,
            $loan->daily_interest_rate,
            $loan->monthly_interest,
            date('«d» m Y', $loan->lend_date),
            '/' . $loan->image,
            $loan->collateral_type == 2 ? $loan->auto->brand : '',
            $loan->collateral_type == 2 ? $loan->auto->year : '',
            $loan->collateral_type == 2 ? Constants::COLORS[$loan->auto->color] : '',
            $loan->collateral_type == 2 ? $loan->auto->mileage : '',
            $loan->collateral_type == 2 ? Constants::TRANSMISSION[$loan->auto->transmission] : '',
            $loan->collateral_type == 2 ? $loan->auto->plate_number : '',
            $loan->collateral_type == 2 ? $loan->auto->engine : '',
            $loan->collateral_type == 2 ? $loan->auto->description : '',
            $loan->collateral_type == 2 ? Constants::GAS[$loan->auto->gas] : '',
            $loan->daily_interest,
            $loan->loaner->phone1,
        ];

        for($i = 0; $i < 10; $i++) {
            $searches[] = '[[jewelry_' . ($i+1) . '_name]]';
            $replaces[] = isset($loan->jewelries[$i]) && $loan->jewelries[$i]->name != '' ? $loan->jewelries[$i]->name : '';

            $searches[] = '[[jewelry_' . ($i+1) . '_purity]]';
            $replaces[] = isset($loan->jewelries[$i]) && $loan->jewelries[$i]->purity != '' ? $loan->jewelries[$i]->purity : '';

            $searches[] = '[[jewelry_' . ($i+1) . '_weight]]';
            $replaces[] = isset($loan->jewelries[$i]) && $loan->jewelries[$i]->weight != '' ? $loan->jewelries[$i]->weight : '';

            $searches[] = '[[jewelry_' . ($i+1) . '_count]]';
            $replaces[] = isset($loan->jewelries[$i]) && $loan->jewelries[$i]->count != '' ? $loan->jewelries[$i]->count : '';

            $searches[] = '[[jewelry_' . ($i+1) . '_pure_weight]]';
            $replaces[] = isset($loan->jewelries[$i]) && $loan->jewelries[$i]->pure_weight != '' ? $loan->jewelries[$i]->pure_weight : '';

            $searches[] = '[[jewelry_' . ($i+1) . '_price]]';
            $replaces[] = isset($loan->jewelries[$i]) && $loan->jewelries[$i]->price != '' ? $loan->jewelries[$i]->price : '';
        }

        switch ($loan->collateral_type) {
            case 2:
                $contractText = Auth::user()->company->contract_auto_text;
                break;
            case 3:
                $contractText = Auth::user()->company->contract_phone_text;
                break;
            default:
                $contractText = Auth::user()->company->contract_text;
                break;
        }

        $printText = str_replace($searches, $replaces, $contractText);

        return view('cashbox.print.print', [
            'printText' => $printText . $printText
        ]);
    }

    public function loanslip(Request $request, Loan $loan) {
        $cashbox = Cashbox::where('id', Auth::user()->cashboxUser->cashbox_id)->firstOrFail();
        $interests = Payment::where('loan_id', $loan->id)->where('type', Constants::PAYMENT_INTEREST)->sum('sum');
        return view('cashbox.print.loan_slip', [
            'loan' => $loan,
            'loan_date' =>  date('«d» m Y', $loan->lend_date),
            'cashier_name' => Auth::user()->last_name . ' ' . Auth::user()->first_name,
            'cashier_license' => Auth::user()->cashboxUser->user_license,
            'interests' => $interests,
            'cashbox' => $cashbox,
        ]);
    }
}
