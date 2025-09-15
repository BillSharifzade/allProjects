<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoanUpdateRequest;
use App\Models\Loan;
use App\Models\Loaner;
use App\Models\LoanJewelry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\IncassatorSafeLoan;

class LoanController extends Controller
{
    public function index(Request $request) {
        $loans = [];
        $loansTotalSum = 0;
        $loansInitialSum = 0;
        $loansLeftSum = 0;
        $loanJewelries = [];
        $incTransfers = collect();
        $collateral_type = (int)$request->get('collateral_type');

        if ($request->get('cashbox') > 0) {
            $loans = Loan::with('jewelries')
                ->with('loaner')
                ->with('auto')
                ->with('phone')
                ->where('closed_at', 0)
                ->where('cashbox_id', $request->get('cashbox'));

            $loansTotalSum = DB::table('loans')
                ->whereRaw('deleted_at is NULL')
                ->where('closed_at', 0)
                ->where('cashbox_id', $request->get('cashbox'));

            $loanJewelries = DB::table('loan_jewelries')
                ->selectRaw('SUM(weight) as weight, purity')
                ->join('loans', 'loan_jewelries.loan_id', '=', 'loans.id')
                ->whereRaw('loans.deleted_at IS NULL')
                ->whereRaw('loan_jewelries.deleted_at IS NULL')
                ->where('loans.closed_at', 0)
                ->where('loans.cashbox_id', $request->get('cashbox'));

            if($collateral_type > 0) {
                $loans->where('collateral_type', $collateral_type);
                $loansTotalSum->where('collateral_type', $collateral_type);
                $loanJewelries->where('loans.collateral_type', $collateral_type);
            }

            if($request->get('audit') == 'yes') {
                $loans->where('in_audit', true);
                $loansTotalSum->where('in_audit', true);
                $loanJewelries->where('loans.in_audit', true);
            }

            $isOverdue = false;
            if ($request->get('filter') == 'overdue') {
                $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
                $expr = "GREATEST(0, FLOOR(({$today} - (CASE WHEN last_principal_payment_date > 0 THEN last_principal_payment_date ELSE interest_accumulation_date END))/86400) - (CASE WHEN interest_rate > 0 AND left_sum > 0 THEN FLOOR(latest_interest_payments_sum / ((interest_rate/30/100) * left_sum)) ELSE 0 END))";
                $loans->whereRaw($expr . ' >= 35');
                $loans->whereHas('loaner');
                $loans->orderByRaw($expr . ' DESC');
                $isOverdue = true;
            }

            if (!$isOverdue) {
                $loans->orderBy('id', 'desc');
            }

            $loans = $loans->paginate(50);

            // Incassation transfer statuses for badges
            $incTransfers = collect();
            try {
                if ($loans->count() > 0) {
                    $incTransfers = \App\Models\IncassationTransfer::whereIn('loan_id', $loans->pluck('id')->all())
                        ->orderBy('id','desc')->get()->keyBy('loan_id');
                }
            } catch (\Throwable $e) { $incTransfers = collect(); }

            $loansLeftSum = clone $loansTotalSum;
            $loansInitialSum = clone $loansTotalSum;

            unset($loansTotalSum);

            $loansLeftSum = $loansLeftSum
                ->sum('left_sum');

            $loansInitialSum = $loansInitialSum
                ->sum('initial_sum');

            $loanJewelries = $loanJewelries
                ->groupBy('loan_jewelries.purity')
                ->get();
        }

        return view('admin.loan.index', [
            'loans' => $loans,
            'loansLeftSum' => $loansLeftSum,
            'loansInitialSum' => $loansInitialSum,
            'loanJewelries' => $loanJewelries,
            'incTransfers' => $incTransfers,
        ]);
    }

    public function closed(Request $request) {
        $loans = [];

        if ($request->get('cashbox') > 0) {
            $loans = Loan::with('jewelries')
                ->with('loaner')
                ->with('auto')
                ->with('phone')
                ->where('closed_at', '>',0)
                ->where('cashbox_id', $request->get('cashbox'))
                ->where('closed_at', '>=', strtotime($request->get('from')))
                ->where('closed_at', '<=', strtotime($request->get('to')));

            if($request->get('reporter') == 'yes') {
                $loans->where('in_audit', true);
            }

            $loans = $loans
                ->orderBy('id', 'desc')
                ->paginate(50);
        }

        $incTransfers = collect();
        try {
            if ($loans && $loans->count() > 0) {
                $incTransfers = \App\Models\IncassationTransfer::whereIn('loan_id', $loans->pluck('id')->all())
                    ->orderBy('id','desc')->get()->keyBy('loan_id');
            }
        } catch (\Throwable $e) { $incTransfers = collect(); }

        return view('admin.loan.index', [
            'loans' => $loans,
            'incTransfers' => $incTransfers,
        ]);
    }

    public function close_requests(Request $request) {
        $loans = [];

        if ($request->get('cashbox') > 0) {
            $loans = Loan::with('jewelries')
                ->with('loaner')
                ->with('auto')
                ->with('phone')
                ->where('closed_at', 0)
                ->where('close_request_at', '>',0)
                ->where('cashbox_id', $request->get('cashbox'));

            if($request->get('reporter') == 'yes') {
                $loans->where('in_audit', true);
            }

            if($request->get('from') != '') {
                $loans->where('close_request_at', '>=', strtotime($request->get('from')));
            }

            if($request->get('to') != '') {
                $loans->where('close_request_at', '<=', strtotime($request->get('to')));
            }

            $loans = $loans
                ->orderBy('id', 'desc')
                ->paginate(50);
        }

        $incTransfers = collect();
        try {
            if ($loans && $loans->count() > 0) {
                $incTransfers = \App\Models\IncassationTransfer::whereIn('loan_id', $loans->pluck('id')->all())
                    ->orderBy('id','desc')->get()->keyBy('loan_id');
            }
        } catch (\Throwable $e) { $incTransfers = collect(); }

        return view('admin.loan.index', [
            'loans' => $loans,
            'incTransfers' => $incTransfers,
        ]);
    }

    public function delete(Request $request, Loan $loan) {
        // Remove from any incassator safe lists
        try {
            $contractFull = ($loan->audit_document_no > 0 ? ($loan->document_no . '-' . $loan->audit_document_no) : $loan->document_no);
            IncassatorSafeLoan::where('company_id', Auth::user()->company_id)
                ->where(function($q) use ($loan, $contractFull){
                    $q->where('loan_id', $loan->id)
                      ->orWhere('contract_no', $loan->document_no)
                      ->orWhere('contract_no', $contractFull);
                })
                ->delete();
        } catch (\Throwable $e) {}

        $loan->delete();

        if($request->get('redirect') != '') {
            return redirect(base64_decode($request->get('redirect')));
        }

        return redirect()->route('admin-loans');
    }

    public function edit(Request $request, Loan $loan) {
        return view('admin.loan.edit', [
            'loan' => $loan
        ]);
    }

    public function update(AdminLoanUpdateRequest $request, Loan $loan) {
        $validated = $request->validated();

        DB::beginTransaction();

        $loaner = Loaner::where('id', $loan->loaner_id)->firstOrFail();
        $loaner->full_name = $validated['fullname'];
        $loaner->phone1 = $validated['phone1'];
        $loaner->phone2 = $validated['phone2'];
        $loaner->phone3 = $validated['phone3'];
        $loaner->phone4 = $validated['phone4'];
        $loaner->residence_address = $validated['residence_address'];

        if ($loaner->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись #1'
            ])->withInput();
        }

        $lendDate = strtotime($validated['lend_month'] . '/' . $validated['lend_day'] . '/' . $validated['lend_year']);
        $interestaccumulationDate = $lendDate + (int)$validated['grace_period'] * 24 * 3600;

        $loan->lend_date = $lendDate;
        $loan->interest_accumulation_date = $interestaccumulationDate;

        if(isset($validated['image'])) {
            $file = $validated['image'];
            if(!$file->isValid()) {
                DB::rollBack();
                return redirect()->back()->withErrors(['Недопустимый файл'])->withInput();
            }
            $mime = $file->getMimeType();
            $allowed = ['image/jpeg','image/png','image/webp'];
            if(!in_array($mime, $allowed)) {
                DB::rollBack();
                return redirect()->back()->withErrors(['Неверный тип файла изображения'])->withInput();
            }
            if($file->getSize() > 3 * 1024 * 1024) {
                DB::rollBack();
                return redirect()->back()->withErrors(['Изображение слишком большое (макс. 3MB)'])->withInput();
            }

            $newFileName = Auth::user()->id . rand(10000000, 99999999) . '.' . $file->getClientOriginalExtension();

            $uploadsDir = storage_path('app/public/uploads');
            if(!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }

            $file->move($uploadsDir, $newFileName);
            $loan->image = 'storage/uploads/' . $newFileName;
        }

        if($loan->left_sum == $loan->initial_sum) {
            $loan->initial_sum = $validated['initial_sum'];
            $loan->left_sum = $validated['initial_sum'];
        }

        if($loan->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись #2'
            ])->withInput();
        }

        LoanJewelry::where('loan_id', $loan->id)->delete();

        for ($i = 1; $i <= 10; $i++) {
            if($validated['item_' . $i . '_name'] != '' &&
                $validated['item_' . $i . '_weight'] > 0 &&
                $validated['item_' . $i . '_purity'] > 0) {

                $loanJewelry = new LoanJewelry();
                $loanJewelry->loan_id = $loan->id;
                $loanJewelry->name = $validated['item_' . $i . '_name'];
                $loanJewelry->purity = $validated['item_' . $i . '_purity'];
                $loanJewelry->weight = $validated['item_' . $i . '_weight'];
                $loanJewelry->pure_weight = $validated['item_' . $i . '_pure_weight'];
                $loanJewelry->count = $validated['item_' . $i . '_count'];
                $loanJewelry->price = $validated['item_' . $i . '_price'];

                if($loanJewelry->save() === false) {
                    DB::rollBack();
                    return redirect()->back()->withErrors([
                        'Не удалось сохранить запись #3'
                    ])->withInput();
                }
            }
        }

        DB::commit();

        if($request->get('redirect') != '') {
            return redirect(base64_decode($request->get('redirect')));
        }

        return redirect()->route('admin-loans');
    }
}
