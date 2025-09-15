<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanStoreRequest;
use App\Http\Requests\LoanUpdateRequest;
use App\Models\Loan;
use App\Models\LoanAuto;
use App\Models\LoanPhone;
use App\Models\Loaner;
use App\Models\LoanJewelry;
use App\Models\IncassationTransfer;
use App\Scopes\AuditScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\CashboxLedger;
use App\Models\CashierShift;
use App\Models\BlacklistEntry;
use App\Helpers;

class LoanController extends Controller
{
    private function currentShiftAndBalance(): array
    {
        $user = Auth::user();
        $cashboxId = $user->cashboxUser->cashbox_id;
        $shift = CashierShift::where('user_id', $user->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();

        if(!$shift) {
            return [null, 0.0];
        }

        $delta = CashboxLedger::where('shift_id', $shift->id)
            ->select(DB::raw('COALESCE(SUM(direction * amount),0) as delta'))
            ->value('delta');
        $balance = (float)$shift->opening_balance + (float)$delta;
        return [$shift, $balance];
    }
    private function writeLedgerDisbursement(Loan $loan): void
    {
        $user = Auth::user();
        $cashboxId = $user->cashboxUser->cashbox_id;
        $shift = CashierShift::where('user_id', $user->id)
            ->where('cashbox_id', $cashboxId)
            ->where('closed_at', 0)
            ->orderBy('id', 'desc')
            ->first();

        CashboxLedger::create([
            'company_id' => $user->company_id,
            'cashbox_id' => $cashboxId,
            'user_id' => $user->id,
            'shift_id' => $shift ? $shift->id : 0,
            'event_type' => 'loan_disbursement',
            'event_id' => (string)$loan->id,
            'direction' => -1,
            'amount' => (float)$loan->initial_sum,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function index(Request $request) {

        $loans = Loan::with('jewelries', 'auto', 'phone')
            ->with('loaner')
            ->where('closed_at', 0);

        $collateral_type = (int)$request->get('collateral_type');
        if($collateral_type != 0) {
            $loans->where('collateral_type', $collateral_type);
        }

        if($request->get('search') != '') {
            $loaners = Loaner::where('full_name', 'like', '%' . $request->get('search') . '%')
                ->orWhere('phone1', 'like', '%' . $request->get('search') . '%')
                ->orWhere('phone2', 'like', '%' . $request->get('search') . '%')
                ->orWhere('phone3', 'like', '%' . $request->get('search') . '%')
                ->orWhere('phone4', 'like', '%' . $request->get('search') . '%')
                ->get();

            $loans->whereIn('loaner_id', $loaners->map(function ($item){
                return $item->id;
            }));
        }

        $isOverdue = false;
        if($request->get('filter') == 'overdue') {
            // Filter strictly to loans with unpaid days >= 35 and order by that value desc
            $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
            $expr = "GREATEST(0, FLOOR(({$today} - (CASE WHEN last_principal_payment_date > 0 THEN last_principal_payment_date ELSE interest_accumulation_date END))/86400) - (CASE WHEN interest_rate > 0 AND left_sum > 0 THEN FLOOR(latest_interest_payments_sum / ((interest_rate/30/100) * left_sum)) ELSE 0 END))";
            $loans->whereRaw($expr . ' >= 35');
            $loans->whereHas('loaner');
            $loans->orderByRaw($expr . ' DESC');
            $isOverdue = true;
        }

        if($request->get('filter') == 'close_requests') {
            $loans->where('closed_at', 0)
                ->where('close_request_at', '>', 0);
        } else {
            $loans->where('close_request_at', 0);
        }

        if(!$isOverdue) {
            $loans->orderBy('id', 'desc');
        }

        return view('cashbox.loan.index', [
            'loans' => $loans->paginate(50),
        ]);
    }

    public function store(LoanStoreRequest $request) {
        $validated = $request->validated();

        // Robust balance check before creating a loan
        [$shift, $balance] = $this->currentShiftAndBalance();
        if(!$shift) {
            return redirect()->back()->withErrors(['Нет открытой смены'])->withInput();
        }
        if((float)$validated['initial_sum'] > $balance) {
            return redirect()->back()->withErrors([
                'Недостаточно средств в кассе. Доступно: ' . number_format($balance, 2, '.', ' ')
            ])->withInput();
        }

        // Blacklist check (fast indexed lookup)
        $pidNorm = Helpers::normalizePassportId($validated['passport_number'] ?? '');
        if($pidNorm !== '') {
            $exists = BlacklistEntry::where('passport_id_norm', $pidNorm)->exists();
            if($exists) {
                return redirect()->back()->withErrors(['Клиент находится в чёрном списке'])->withInput();
            }
        }

        DB::beginTransaction();

        $loaner = Loaner::where('passport_number', $request['passport_number'])->first();

        if(!$loaner) {
            $loaner = new Loaner();
        }

        $loaner->full_name = $validated['fullname'];
        $loaner->phone1 = $validated['phone1'];
        $loaner->phone2 = $validated['phone2'];
        $loaner->phone3 = $validated['phone3'];
        $loaner->phone4 = $validated['phone4'];
        $loaner->tin = $validated['tin'];
        $loaner->passport_number = $validated['passport_number'];
        $loaner->passport_issuer = $validated['passport_issuer'];
        $loaner->passport_issued_day = $validated['passport_issued_day'];
        $loaner->passport_issued_month = $validated['passport_issued_month'];
        $loaner->passport_issued_year = $validated['passport_issued_year'];
        $loaner->residence_address = $validated['residence_address'];
        $loaner->birth_day = $validated['birth_day'];
        $loaner->birth_month = $validated['birth_month'];
        $loaner->birth_year = $validated['birth_year'];
        $loaner->cashbox_id = Auth::user()->cashboxUser->cashbox_id;

        if ($loaner->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись #1'
            ])->withInput();
        }

        $lastLoan = Loan::withoutGlobalScope(new AuditScope)
            ->orderBy('id', 'desc')
            ->first();

        $auditLastLoan = Loan::where('in_audit', true)
            ->orderBy('id', 'desc')
            ->first();

        $lendDate = strtotime(date('m') . '/' . date('d') . '/' . date('Y')); //strtotime($validated['lend_month'] . '/' . $validated['lend_day'] . '/' . $validated['lend_year']);

        $documentNo = isset($lastLoan) ? $lastLoan->document_no + 1 : 1;
        $auditDocumentNo = 0;

        if($validated['in_audit'] == true) {
            $auditDocumentNo  = isset($auditLastLoan) ? $auditLastLoan->audit_document_no + 1 : 1;
        }

        $loan = new Loan();
        $loan->cashbox_id = Auth::user()->cashboxUser->cashbox_id;
        $loan->company_id = Auth::user()->company_id;
        $loan->user_id = Auth::user()->id;
        $loan->loaner_id = $loaner->id;
        $loan->document_no = $documentNo;
        $loan->audit_document_no = $auditDocumentNo;
        $loan->lend_date = $lendDate;
        $loan->interest_accumulation_date = $lendDate;
        $loan->image = '';
        $loan->in_audit = $validated['in_audit'] == true;
        $loan->is_notifiable = $validated['is_notifiable'] == true;
        $loan->initial_sum = $validated['initial_sum'];
        $loan->left_sum = $validated['initial_sum'];
        $loan->collateral_type = $validated['collateral_type'];

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

        if($loan->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись #2'
            ])->withInput();
        }

        if($validated['collateral_type'] == 1) {
            for ($i = 1; $i <= 10; $i++) {
                if($validated['item_' . $i . '_name'] != '' &&
                    $validated['item_' . $i . '_weight'] > 0 &&
                    $validated['item_' . $i . '_purity'] > 0 &&
                    $validated['item_' . $i . '_pure_weight'] > 0 &&
                    $validated['item_' . $i . '_count'] > 0 &&
                    $validated['item_' . $i . '_price'] > 0) {

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
        }

        if($validated['collateral_type'] == 2) {
            $loanAuto = new LoanAuto();
            $loanAuto->loan_id = $loan->id;
            $loanAuto->brand = $validated['vehicle_brand'];
            $loanAuto->year = $validated['vehicle_year'];
            $loanAuto->color = $validated['vehicle_color'];
            $loanAuto->plate_number = $validated['vehicle_plate_number'];
            $loanAuto->engine = $validated['vehicle_engine'];
            $loanAuto->location = $validated['vehicle_location'];
            $loanAuto->description = $validated['vehicle_description'];
            $loanAuto->mileage = $validated['vehicle_mileage'];
            $loanAuto->transmission = $validated['vehicle_transmission'];
            $loanAuto->gas = $validated['vehicle_gas'];

            if($loanAuto->save() === false) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'Не удалось сохранить запись #4'
                ])->withInput();
            }
        }

        if($validated['collateral_type'] == 3) {
            $loanPhone = new LoanPhone();
            $loanPhone->loan_id = $loan->id;
            $loanPhone->brand = $validated['phone_brand'];
            $loanPhone->model = $validated['phone_model'];
            $loanPhone->imei = $validated['phone_imei'] ?? '';
            $loanPhone->storage_gb = $validated['phone_storage_gb'] ?? null;
            $loanPhone->color = $validated['phone_color'] ?? '';
            $loanPhone->condition = $validated['phone_condition'] ?? '';
            $loanPhone->description = $validated['phone_description'] ?? '';

            if($loanPhone->save() === false) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'Не удалось сохранить запись #5'
                ])->withInput();
            }
        }

        DB::commit();

        // Ledger write for disbursement
        $this->writeLedgerDisbursement($loan);

        return redirect()->route('loans');
    }

    public function create(Request $request) {
        $collateral_type = (int)$request->input('collateral_type');

        if(!in_array($collateral_type, [1,2,3])) {
            $collateral_type = 1;
        }

        return view('cashbox.loan.create', [
            'collateral_type' => $collateral_type
        ]);
    }

    public function edit(Request $request, Loan $loan) {
        return view('cashbox.loan.edit', [
            'loan' => $loan
        ]);
    }

    public function update(LoanUpdateRequest $request, Loan $loan) {
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

        if($loan->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось сохранить запись #2'
            ])->withInput();
        }

        if($loan->collateral_type == 1) {
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
        }

        if($loan->collateral_type == 2) {
            $loanAuto = LoanAuto::where('loan_id', $loan->id)->first();
            $loanAuto->brand = $validated['vehicle_brand'];
            $loanAuto->year = $validated['vehicle_year'];
            $loanAuto->color = $validated['vehicle_color'];
            $loanAuto->plate_number = $validated['vehicle_plate_number'];
            $loanAuto->engine = $validated['vehicle_engine'];
            $loanAuto->location = $validated['vehicle_location'];
            $loanAuto->description = $validated['vehicle_description'];
            $loanAuto->mileage = $validated['vehicle_mileage'];
            $loanAuto->transmission = $validated['vehicle_transmission'];
            $loanAuto->gas = $validated['vehicle_gas'];

            if($loanAuto->save() === false) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'Не удалось сохранить запись #4'
                ])->withInput();
            }
        }

        if($loan->collateral_type == 3) {
            $loanPhone = \App\Models\LoanPhone::where('loan_id', $loan->id)->first();
            if(!$loanPhone) { $loanPhone = new LoanPhone(); $loanPhone->loan_id = $loan->id; }
            $loanPhone->brand = $validated['phone_brand'];
            $loanPhone->model = $validated['phone_model'];
            $loanPhone->imei = $validated['phone_imei'] ?? '';
            $loanPhone->storage_gb = $validated['phone_storage_gb'] ?? null;
            $loanPhone->color = $validated['phone_color'] ?? '';
            $loanPhone->condition = $validated['phone_condition'] ?? '';
            $loanPhone->description = $validated['phone_description'] ?? '';

            if($loanPhone->save() === false) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'Не удалось сохранить запись #5'
                ])->withInput();
            }
        }

        DB::commit();

        return redirect()->route('loans');
    }

    public function show(Request $request, Loan $loan) {
        print_r($loan);
        exit;
    }

    public function close(Request $request, Loan $loan) {
        if($loan->closed_at == 0) {
            return view('cashbox.loan.close', [
                'loan' => $loan
            ]);
        }
    }

    public function over(Request $request, Loan $loan) {
        if($loan->left_sum > 0) {
            return redirect()->back()->withErrors([
                'Основной кредит не погащен'
            ])->withInput();
        }

        // New rule: Closing is allowed only after incassation is completed and accepted by the cashier
        try {
            $inc = IncassationTransfer::where('company_id', Auth::user()->company_id)
                ->where('loan_id', $loan->id)
                ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
                ->first();
            if (!$inc || !$inc->accepted_by_cashier) {
                return redirect()->back()->withErrors([
                    'Сначала завершите инкассацию: предмет залога должен быть доставлен и принят кассиром'
                ])->withInput();
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'Сначала завершите инкассацию: предмет залога должен быть доставлен и принят кассиром'
            ])->withInput();
        }

        if($loan->close_request_at == 0) {

            $loan->close_request_at = time();
            if($loan->save() === false) {
                return redirect()->back()->withErrors([
                    'Не удалось сохранить запись'
                ])->withInput();
            }
        } else {
            if(($loan->close_request_at + 12* 3600) > time()) {
                return redirect()->back()->withErrors([
                    'Заявка на процессе обработки'
                ])->withInput();
            }

            $loan->closed_at = time();
            if($loan->save() === false) {
                return redirect()->back()->withErrors([
                    'Не удалось сохранить запись'
                ])->withInput();
            }

            // Archive snapshot for formally closed loans
            try {
                $snapshot = [
                    'loan' => $loan->load('loaner','jewelries','auto','phone','payments','cashbox','user')->toArray(),
                    'loaner' => optional($loan->loaner)->toArray(),
                    'jewelries' => $loan->jewelries ? $loan->jewelries->toArray() : [],
                    'auto' => optional($loan->auto)->toArray(),
                    'phone' => optional($loan->phone)->toArray(),
                    'payments' => $loan->payments ? $loan->payments->toArray() : [],
                    'cashbox' => optional($loan->cashbox)->toArray(),
                    'user' => optional($loan->user)->toArray(),
                ];
                \App\Models\Archive::create([
                    'company_id' => (int)$loan->company_id,
                    'loan_id' => (int)$loan->id,
                    'type' => 'closed',
                    'snapshot' => json_encode($snapshot, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
                    'archived_at' => time(),
                    'created_at' => time(),
                ]);
            } catch (\Throwable $e) {}

            // Auto-create incassation transfer entry for fully closed loans (to deliver)
            try {
                if($loan->left_sum == 0 && $loan->unpaid_interest == 0) {
                    $exists = \App\Models\IncassationTransfer::where('loan_id', $loan->id)->exists();
                    if(!$exists) {
                        $contractFull = '№' . $loan->document_no . ($loan->audit_document_no > 0 ? ('-' . $loan->audit_document_no) : '');
                        // Build concise loan info string
                        $info = '';
                        if ($loan->collateral_type == 1) {
                            foreach ($loan->jewelries as $j) {
                                $info .= ($info ? '; ' : '') . $j->name . ', ' . $j->purity . ' пр., ' . $j->weight . ' гр.';
                            }
                        } elseif ($loan->collateral_type == 2 && $loan->auto) {
                            $info = 'марка ' . $loan->auto->brand . ', ' . $loan->auto->year . ', ' . $loan->auto->plate_number;
                        } elseif ($loan->collateral_type == 3 && $loan->phone) {
                            $info = 'смартфон ' . $loan->phone->brand . ' ' . $loan->phone->model;
                            if (!empty($loan->phone->storage_gb)) { $info .= ' ' . $loan->phone->storage_gb . 'GB'; }
                            if (!empty($loan->phone->color)) { $info .= ', ' . $loan->phone->color; }
                            if (!empty($loan->phone->imei)) { $info .= ', IMEI ' . $loan->phone->imei; }
                        }

                        \App\Models\IncassationTransfer::create([
                            'company_id' => \Illuminate\Support\Facades\Auth::user()->company_id,
                            'cashbox_id' => \Illuminate\Support\Facades\Auth::user()->cashboxUser->cashbox_id,
                            'incassator_id' => null,
                            'cashier_id' => null,
                            'loan_id' => $loan->id,
                            'contract_no' => $contractFull,
                            'client_name' => optional($loan->loaner)->full_name,
                            'loan_info' => $info,
                            'picked_by_incassator' => false,
                            'picked_at' => 0,
                            'delivered_by_incassator' => false,
                            'delivered_at' => 0,
                            'accepted_by_cashier' => false,
                            'accepted_at' => 0,
                            'created_at' => time(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // do not block closing
            }
        }

        return redirect('/loans?filter=close_requests');
    }
}
