@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/payments/{{$loan->id}}/create" class="btn btn-primary locked-hide">
            ВНЕСТИ НОВЫЙ ПЛАТЕЖ
        </a>
    </p>

    <table class="m-2 table table-light">
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
            <th>
                Действие
            </th>
        </thead>
        <tbody>
        @foreach($loan->payments as $key => $payment)
            <tr>
                <td>
                    {{$key+1}}.
                </td>
                <td>

                    @if($loan->audit_document_no > 0)
                        №{{$loan->document_no}}-{{$loan->audit_document_no}}
                    @else
                        №{{$loan->document_no}}
                    @endif

                    {{$loan->loaner->full_name}}
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
                <td>
                    <a href="/print/payment?uuid={{$payment->uuid}}" style="font-size: 16px" class="px-1">
                        <i class="fas fa-print"></i>
                    </a>

                    <a href="/print/receipt?uuid={{$payment->uuid}}" style="font-size: 16px" class="px-1">
                        <i class="fas fa-print" style="color: red;"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
