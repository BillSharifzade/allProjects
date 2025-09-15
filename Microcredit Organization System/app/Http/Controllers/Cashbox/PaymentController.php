<?php

namespace App\Http\Controllers\Cashbox;

use App\Constants;
use App\Events\PaymentCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CashboxLedger;
use App\Models\CashierShift;
use Illuminate\Support\Facades\Cache;

class PaymentController extends Controller
{
    private function writeLedger(string $eventType, string $eventId, int $direction, float $amount): void
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
            'event_type' => $eventType,
            'event_id' => $eventId,
            'direction' => $direction,
            'amount' => $amount,
            'occurred_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function store(PaymentStoreRequest $request, Loan $loan) {
        $validated = $request->validated();

        // Strong idempotency
        $token = (string)$request->input('idempotency_key', '');
        if ($token === '' || !session()->has('pay_idem.' . $token)) {
            return redirect()->back()->withErrors(['Повторная отправка формы. Обновите страницу и попробуйте снова'])->withInput();
        }
        $lockKey = 'pay_lock:' . Auth::id() . ':' . $loan->id . ':' . $token;
        $resultKey = 'pay_res:' . Auth::id() . ':' . $loan->id . ':' . $token;
        if (Cache::has($resultKey)) {
            $existingUuid = Cache::get($resultKey);
            return redirect('/print/payment?uuid=' . $existingUuid);
        }
        if (!Cache::add($lockKey, 1, 60)) {
            $existingUuid = Cache::get($resultKey);
            if ($existingUuid) {
                return redirect('/print/payment?uuid=' . $existingUuid);
            }
            return redirect()->back()->withErrors(['Платеж уже обрабатывается. Подождите...'])->withInput();
        }

        $requestedInterest = (float)($validated['interest_sum'] ?? 0);
        $requestedPrincipal = (float)($validated['principal_sum'] ?? 0);
        $maxPrincipal = max(0.0, (float)$loan->left_sum);
        $maxInterest = max(0.0, (float)ceil((float)$loan->unpaid_interest));
        $isFirstInterest = ((int)$loan->last_interest_payment_date <= 0);

        // Principal requires full interest (rounded up) to be paid
        if($requestedPrincipal > 0 && $requestedInterest < $maxInterest) {
            return redirect()->back()->withErrors([
                'Укажите полную сумму процентов'
            ])->withInput();
        }

        $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));
        $uuid = $loan->id . Str::uuid()->toString();

        DB::beginTransaction();
        $success = false;

        $interestPayment = null;
        $principalPayment = null;

        // First interest minimum rule: if it's the first interest payment and due < 10, enforce at least 10
        if ($isFirstInterest && $maxInterest < 10.0 && $requestedInterest > 0 && $requestedInterest < 10.0) {
            $requestedInterest = 10.0;
        }

        // Enforce caps (allow first-interest overpay to 10 when due < 10)
        if ($requestedInterest > $maxInterest) {
            if (!($isFirstInterest && $maxInterest < 10.0 && $requestedInterest >= 10.0)) {
                $requestedInterest = $maxInterest;
            }
        }
        if($requestedPrincipal > $maxPrincipal) { $requestedPrincipal = $maxPrincipal; }

        // Serialize by locking the loan row to avoid race updates
        Loan::where('id', $loan->id)->lockForUpdate()->first();

        if($requestedInterest > 0) {
            $interestPayment = new Payment();
            $interestPayment->company_id = Auth::user()->company_id;
            $interestPayment->loan_id = $loan->id;
            $interestPayment->uuid = $uuid;
            $interestPayment->cashbox_id = $loan->cashbox_id;
            $interestPayment->user_id = Auth::user()->id;
            $interestPayment->type = Constants::PAYMENT_INTEREST;
            $interestPayment->paid_date = $today;
            $interestPayment->sum = $requestedInterest;

            if($interestPayment->save() === false) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'Не удалось сохранить платеж #1'
                ])->withInput();
            }
        }

        if($requestedPrincipal > 0) {
            $principalPayment = new Payment();
            $principalPayment->company_id = Auth::user()->company_id;
            $principalPayment->loan_id = $loan->id;
            $principalPayment->uuid = $uuid;
            $principalPayment->cashbox_id = $loan->cashbox_id;
            $principalPayment->user_id = Auth::user()->id;
            $principalPayment->type = Constants::PAYMENT_PRINCIPAL;
            $principalPayment->paid_date = $today;
            $principalPayment->sum = $requestedPrincipal;

            if($principalPayment->save() === false) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'Не удалось сохранить платеж #2'
                ])->withInput();
            }

            $loan->left_sum -= $requestedPrincipal;
        }

        if($requestedPrincipal > 0) {
            $loan->last_principal_payment_date = $today;
        }

        if($requestedInterest > 0) {
            $loan->last_interest_payment_date = $today;
        }

        $lastPrincipalPayment = Payment::where('loan_id', $loan->id)
            ->where('type', Constants::PAYMENT_PRINCIPAL)
            ->orderBy('id', 'desc')
            ->first();

        $latestInterestPaymentsSum = DB::table('payments')
            ->where('loan_id', $loan->id)
            ->whereRaw('deleted_at IS NULL')
            ->where('type', Constants::PAYMENT_INTEREST)
            ->where('id', '>', isset($lastPrincipalPayment) ? $lastPrincipalPayment->id : 0)
            ->sum('sum');

        $loan->latest_interest_payments_sum = $latestInterestPaymentsSum;

        if($loan->save() === false) {
            DB::rollBack();
            Cache::forget($lockKey);
            return redirect()->back()->withErrors([
                'Не удалось сохранить платеж #2'
            ])->withInput();
        }

        // Ledger writes inside the same transaction for atomicity
        if ($interestPayment) {
            $this->writeLedger('interest_payment', (string)$interestPayment->id, +1, (float)$interestPayment->sum);
        }
        if ($principalPayment) {
            $this->writeLedger('principal_payment', (string)$principalPayment->id, +1, (float)$principalPayment->sum);
        }

        DB::commit();
        $success = true;

        // Auto-create incassation transfer entry for fully paid loans (to deliver)
        try {
            // Re-load fresh loan to recalculate derived fields via observer (unpaid_interest)
            $fresh = \App\Models\Loan::with('jewelries','auto','phone','loaner')
                ->where('id', $loan->id)->first();
            if($fresh && (float)$fresh->left_sum == 0.0 && (float)round($fresh->unpaid_interest,2) == 0.0) {
                $exists = \App\Models\IncassationTransfer::where('loan_id', $fresh->id)->exists();
                if(!$exists) {
                    // Only create if this loan is in the incassator's safe (any incassator) for this cashbox
                    $contractFull = ($fresh->audit_document_no > 0 ? ($fresh->document_no . '-' . $fresh->audit_document_no) : $fresh->document_no);
                    $inSafe = \App\Models\IncassatorSafeLoan::where('company_id', Auth::user()->company_id)
                        ->where('cashbox_id', Auth::user()->cashboxUser->cashbox_id)
                        ->where(function($q) use ($fresh, $contractFull){
                            $q->where('loan_id', $fresh->id)
                              ->orWhere('contract_no', $fresh->document_no)
                              ->orWhere('contract_no', $contractFull);
                        })
                        ->exists();
                    if(!$inSafe) { return; }
                    $contractFull = '№' . $fresh->document_no . ($fresh->audit_document_no > 0 ? ('-' . $fresh->audit_document_no) : '');
                    $info = '';
                    if ($fresh->collateral_type == 1) {
                        foreach ($fresh->jewelries as $j) {
                            $info .= ($info ? '; ' : '') . $j->name . ', ' . $j->purity . ' пр., ' . $j->weight . ' гр.';
                        }
                    } elseif ($fresh->collateral_type == 2 && $fresh->auto) {
                        $info = 'марка ' . $fresh->auto->brand . ', ' . $fresh->auto->year . ', ' . $fresh->auto->plate_number;
                    } elseif ($fresh->collateral_type == 3 && $fresh->phone) {
                        $info = 'смартфон ' . $fresh->phone->brand . ' ' . $fresh->phone->model;
                        if (!empty($fresh->phone->storage_gb)) { $info .= ' ' . $fresh->phone->storage_gb . 'GB'; }
                        if (!empty($fresh->phone->color)) { $info .= ', ' . $fresh->phone->color; }
                        if (!empty($fresh->phone->imei)) { $info .= ', IMEI ' . $fresh->phone->imei; }
                    }

                    \App\Models\IncassationTransfer::create([
                        'company_id' => \Illuminate\Support\Facades\Auth::user()->company_id,
                        'cashbox_id' => \Illuminate\Support\Facades\Auth::user()->cashboxUser->cashbox_id,
                        'incassator_id' => null,
                        'cashier_id' => null,
                        'loan_id' => $fresh->id,
                        'contract_no' => $contractFull,
                        'client_name' => optional($fresh->loaner)->full_name,
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
            // Do not block payment flow
        }

        // Defer notifications to after the response is sent to avoid blocking
        if (method_exists(\App\Events\PaymentCreated::class, 'dispatchAfterResponse')) {
            PaymentCreated::dispatchAfterResponse($interestPayment, $principalPayment);
        } else {
            app()->terminating(function() use ($interestPayment, $principalPayment) {
                event(new \App\Events\PaymentCreated($interestPayment, $principalPayment));
            });
        }

        // Map idempotency token to result for repeat-safe redirects
        Cache::put($resultKey, $uuid, 3600);
        session()->forget('pay_idem.' . $token);
        Cache::forget($lockKey);

        return redirect('/print/payment?uuid=' . $uuid);
    }

    public function create(Loan $loan) {
        $idempotencyToken = Str::uuid()->toString();
        // Allow one-shot use of token from this form
        session()->put('pay_idem.' . $idempotencyToken, 1);

        return view('cashbox.payment.create', [
            'loan' => $loan,
            'idempotencyToken' => $idempotencyToken,
        ]);
    }

    public function index(Request $request) {
        if($request->get('loanId') > 0) {
            return view('cashbox.payment.loan', [
                'loan' =>  Loan::with(['payments' => function($query){
                    $query->orderBy('id', 'desc');
                }])->where('id', $request->get('loanId'))->firstOrFail()
            ]);
        }

        $today = strtotime(date('m') . '/' . date('d') . '/' . date('Y'));

        $payments = DB::table('payments')
            ->join('loans', 'payments.loan_id', '=', 'loans.id')
            ->whereRaw('loans.deleted_at IS NULL')
            ->whereRaw('payments.deleted_at IS NULL')
            ->where('payments.cashbox_id', Auth::user()->cashboxUser->cashbox_id)
            ->where('payments.company_id', Auth::user()->company_id)
            ->where('payments.paid_date', '=', $today);

        if(Auth::user()->isCashierAudit()) {
            $payments->where('loans.in_audit', true);
        }

        $payments = $payments->get();

        $loans = Loan::with('loaner')
            ->with('cashbox')
            ->whereIn('id', $payments->map(function($payment){
                return $payment->loan_id;
            }))
            ->get();

        $totalPrincipalPayments = 0;
        $totalInterestPayments = 0;

        foreach($payments as $payment) {
            switch ($payment->type) {
                case Constants::PAYMENT_PRINCIPAL:
                    $totalPrincipalPayments += $payment->sum;
                    break;
                case Constants::PAYMENT_INTEREST:
                    $totalInterestPayments += $payment->sum;
                    break;
            }
        }

        return view('cashbox.payment.index', [
            'payments' => $payments,
            'totalPrincipalPayments' => $totalPrincipalPayments,
            'totalInterestPayments' => $totalInterestPayments,
            'loans' => $loans,
        ]);
    }
}
