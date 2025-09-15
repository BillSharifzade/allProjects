@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/admin/payments/{{$loan->id}}/create" class="btn btn-primary">
            ВНЕСТИ НОВЫЙ ПЛАТЕЖ
        </a>
    </p>
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
                    <a href="/admin/payments/{{$payment->id}}/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
