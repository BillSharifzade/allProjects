<?php

namespace App\Http\Controllers\Reporter;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintController extends Controller
{
    public function loan(Request $request, Loan $loan) {
        $cashbox = Cashbox::where('id', $loan->cashbox_id)->firstOrFail();
        $user = User::where('id', $loan->user_id)->firstOrFail();

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
        ];

        $replaces = [
            Auth::user()->company->name,
            $loan->in_audit ? $loan->audit_document_no : $loan->document_no,
            $cashbox->license,
            $user->last_name . ' ' . $user->first_name,
            $user->cashboxUser->user_license,
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

        return view('reporter.print.print', [
            'printText' => $printText,
        ]);
    }
}
