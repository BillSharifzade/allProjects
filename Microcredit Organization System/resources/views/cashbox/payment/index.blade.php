@extends('layout')

@section('content')
    <div class="card m-2 p-2">
    <table class="table table-light table-hover zebra">
        <thead class=" table-sm">
        <th>
            #
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
    </div>
    <br />
    <h5>Итог</h5>
    <p class="p-0 m-1">Процент: <strong>{{$totalInterestPayments}} сомонӣ 00 дирам</strong></p>
    <p class="p-0 m-1">Основной кредит: <strong>{{$totalPrincipalPayments}} сомонӣ 00 дирам</strong></p>
@endsection
