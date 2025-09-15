<?php

namespace App\Http\Controllers\Admin;

use App\Constants;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPaymentStoreRequest;
use App\Models\Cashbox;
use App\Models\Loan;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\CashboxLedger;
use App\Models\CashierShift;
use App\Models\IncassationTransfer;

class PaymentController extends Controller
{
    private function writeLedger(string $eventType, string $eventId, int $direction, float $amount): void
    {
        $user = Auth::user();
        $cashboxId = $user->cashboxUser->cashbox_id ?? null;
        if (!$cashboxId) { return; }
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

    public function index(Request $request) {
        if($request->get('loanId') > 0) {
            return view('admin.payment.loan', [
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

            if($request->get('audit') == 'yes') {
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

        return view('admin.payment.index', [
            'payments' => $payments,
            'loans' => $loans,
            'principalPaymentsTotalSum' => $principalPaymentsTotalSum,
            'interestPaymentsTotalSum' => $interestPaymentsTotalSum,
        ]);
    }

    public function delete(Request $request, Payment $payment) {
        $followingPayments = Payment::where('created_at', '>=', $payment->created_at->timestamp)
            ->where('uuid', '<>',$payment->uuid)
            ->where('loan_id', $payment->loan_id)
            ->where('id', '>', $payment->id)
            ->get();

        if(count($followingPayments) > 0) {
            return redirect()->back()->withErrors([
                'Удаление невозможно. Существуют последующие платежи'
            ])->withInput();
        }

        $loan = Loan::where('id', $payment->loan_id)
            ->where('closed_at', 0)
            ->firstOrFail();

        DB::beginTransaction();

        // get principal payment with $payment->uuid
        $principalPayment = Payment::where('uuid', $payment->uuid)
            ->where('type', Constants::PAYMENT_PRINCIPAL)
            ->first();

        // add $principalPayment->sum to $loan->left_sum
        if($principalPayment) {
            $loan->left_sum += $principalPayment->sum;
        }

        // delete all payments with $payment->uuid
        Payment::where('uuid', $payment->uuid)->delete();

        // ledger reversal for this group
        $paymentsByUuid = Payment::withTrashed()->where('uuid', $payment->uuid)->get();
        foreach($paymentsByUuid as $p) {
            $direction = $p->type == Constants::PAYMENT_INTEREST || $p->type == Constants::PAYMENT_PRINCIPAL ? -1 : 0;
            if($direction !== 0) {
                $this->writeLedger('reversal', (string)$p->id, $direction, (float)$p->sum);
            }
        }

        // find last principal payment
        $lastPrincipalPayment = Payment::where('loan_id', $loan->id)
            ->where('type', Constants::PAYMENT_PRINCIPAL)
            ->orderBy('id', 'desc')
            ->first();

        $loan->last_principal_payment_date = $lastPrincipalPayment ? $lastPrincipalPayment->paid_date : 0;

        // find last interest payment
        $lastInterestPayment = Payment::where('loan_id', $loan->id)
            ->where('type', Constants::PAYMENT_INTEREST)
            ->orderBy('id', 'desc')
            ->first();

        $loan->last_interest_payment_date = $lastInterestPayment ? $lastInterestPayment->paid_date : 0;

        // get sum of all interest payments conducted after $lastPrincipalPayment
        $latestInterestPaymentsSum = DB::table('payments')
            ->where('loan_id', $loan->id)
            ->whereRaw('deleted_at is NULL')
            ->where('type', Constants::PAYMENT_INTEREST)
            ->where('id', '>', isset($lastPrincipalPayment) ? $lastPrincipalPayment->id : 0)
            ->sum('sum');

        $loan->latest_interest_payments_sum = $latestInterestPaymentsSum;

        if($loan->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось удалить платежи'
            ])->withInput();
        }

        // If loan becomes unsettled after deletion, ensure related incassation transfer is reset/cancelled
        try {
            if(($loan->left_sum > 0) || ($loan->unpaid_interest > 0)) {
                IncassationTransfer::where('company_id', Auth::user()->company_id)
                    ->where('loan_id', $loan->id)
                    ->update([
                        'accepted_by_cashier' => false,
                        'accepted_at' => 0,
                        'cashier_id' => null,
                        'delivered_by_incassator' => false,
                        'delivered_at' => 0,
                        'picked_by_incassator' => false,
                        'picked_at' => 0,
                        'incassator_id' => null,
                    ]);
            }
        } catch (\Throwable $e) {
            // do not block deletion flow
        }

        DB::commit();

        return redirect('/admin/payments?loanId=' . $loan->id);
    }

    public function create(Request $request, Loan $loan) {
        if($loan->closed_at != 0) {
            abort(404, 'LOAN_CLOSED');
        }

        return view('admin.payment.create', [
            'loan' => $loan
        ]);
    }

    public function store(AdminPaymentStoreRequest $request, Loan $loan) {
        $validated = $request->validated();

        $requestedInterest = (float)($validated['interest_sum'] ?? 0);
        $requestedPrincipal = (float)($validated['principal_sum'] ?? 0);
        $maxPrincipal = max(0.0, (float)$loan->left_sum);
        $maxInterest = max(0.0, (float)ceil((float)$loan->unpaid_interest));
        $isFirstInterest = ((int)$loan->last_interest_payment_date <= 0);

        if($requestedPrincipal > 0 && $requestedInterest < $maxInterest) {
            return redirect()->back()->withErrors([
                'Укажите полную сумму процентов'
            ])->withInput();
        }

        $paidDate = strtotime($validated['paid_month'] . '/' . $validated['paid_day'] . '/' . $validated['paid_year']);
        $uuid = $loan->id . Str::uuid()->toString();

        DB::beginTransaction();

        // First interest minimum rule (admin): if first interest due < 10, allow minimum 10 paid
        if ($isFirstInterest && $maxInterest < 10.0 && $requestedInterest > 0 && $requestedInterest < 10.0) {
            $requestedInterest = 10.0;
        }

        if ($requestedInterest > $maxInterest) {
            if (!($isFirstInterest && $maxInterest < 10.0 && $requestedInterest >= 10.0)) {
                $requestedInterest = $maxInterest;
            }
        }
        if($requestedPrincipal > $maxPrincipal) { $requestedPrincipal = $maxPrincipal; }

        if($requestedInterest > 0) {
            $interestPayment = new Payment();
            $interestPayment->company_id = Auth::user()->company_id;
            $interestPayment->loan_id = $loan->id;
            $interestPayment->uuid = $uuid;
            $interestPayment->cashbox_id = $loan->cashbox_id;
            $interestPayment->user_id = Auth::user()->id;
            $interestPayment->type = Constants::PAYMENT_INTEREST;
            $interestPayment->paid_date = $paidDate;
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
            $principalPayment->paid_date = $paidDate;
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
            $loan->last_principal_payment_date = $paidDate;
        }

        if($requestedInterest > 0) {
            $loan->last_interest_payment_date = $paidDate;
        }

        $lastPrincipalPayment = Payment::where('loan_id', $loan->id)
            ->where('type', Constants::PAYMENT_PRINCIPAL)
            ->orderBy('id', 'desc')
            ->first();

        $latestInterestPaymentsSum = DB::table('payments')
            ->where('loan_id', $loan->id)
            ->whereRaw('deleted_at is NULL')
            ->where('type', Constants::PAYMENT_INTEREST)
            ->where('id', '>', isset($lastPrincipalPayment) ? $lastPrincipalPayment->id : 0)
            ->sum('sum');

        $loan->latest_interest_payments_sum = $latestInterestPaymentsSum;

        if($loan->save() === false) {
            DB::rollBack();
            return redirect()->back()->withErrors([
                'Не удалось сохранить платеж #2'
            ])->withInput();
        }

        // Ledger writes inside same transaction
        if(isset($interestPayment)) {
            $this->writeLedger('interest_payment', (string)$interestPayment->id, +1, (float)$interestPayment->sum);
        }
        if(isset($principalPayment)) {
            $this->writeLedger('principal_payment', (string)$principalPayment->id, +1, (float)$principalPayment->sum);
        }

        DB::commit();

        return redirect('/admin/payments?loanId=' . $loan->id);
    }
}
