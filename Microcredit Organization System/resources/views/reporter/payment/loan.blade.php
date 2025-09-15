@extends('layout')

@section('content')
    @widget('Error')
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
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
