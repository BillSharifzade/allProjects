@extends('layout')

@section('content')
    @if(Auth::user()->isAudit())
        @widget('cashboxFilter', ['closed' => false, 'audit' => false])
    @else
        @widget('cashboxFilter', ['closed' => false])
    @endif

    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Касса
        </th>
        <th>
            ФИО
        </th>
        <th>
            Сумма платежа
        </th>
        <th>
            Тип
        </th>
        <th>
            Дата платежа
        </th>
        </thead>
        <tbody>
        @php
            $counter = (\Request::get('page', 1) - 1) * 50
        @endphp
        @foreach($payments as $key => $payment)
            @php
                $counter = $counter + 1
            @endphp
            @php
                $paymentLoan = null;
            @endphp

            @foreach($loans as $loan)

                @if($loan->id == $payment->loan_id)

                    @php
                        $paymentLoan = $loan;
                    @endphp

                @endif

            @endforeach

            <tr>
                <td>
                    {{$counter}}.
                </td>
                <td>
                    {{$paymentLoan->cashbox->name}}<br/>
                    <p class="badge badge-info">{{$paymentLoan->cashbox->nickname}}</p>
                </td>
                <td>
                    @if($paymentLoan->audit_document_no > 0)
                        №{{$paymentLoan->document_no}}-{{$paymentLoan->audit_document_no}}
                    @else
                        №{{$paymentLoan->document_no}}
                    @endif

                    {{$paymentLoan->loaner->full_name}}
                </td>
                <td>
                    {{$payment->sum}}
                </td>
                <td>
                    @if($payment->type == \App\Constants::PAYMENT_INTEREST)
                        Процент
                    @else
                        Основной кредит
                    @endif
                </td>
                <td>
                    @date($payment->paid_date)
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(count($payments) > 0)
        {{$payments->appends($_GET)->links()}}
    @endif
    <br />
    <h5>Итог</h5>
    <p class="p-0 m-1">Основной кредит: <strong>{{(int)$principalPaymentsTotalSum}} сомонӣ 00 дирам</strong></p>
    <p class="p-0 m-1">Процент: <strong>{{(int)$interestPaymentsTotalSum}} сомонӣ 00 дирам</strong></p>
@endsection
