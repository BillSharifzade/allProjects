@extends('layout')

@section('content')
    @widget('cashboxFilter', ['closed' => false])
    <br />
    @if($loansInitialSum > 0 || $loansLeftSum > 0 || $principalPaymentsTotalSum > 0 || $interestPaymentsTotalSum > 0)
        <h5>Итог</h5>
        <p class="p-0 m-1">Сумма кредитов: <strong>{{(int)$loansInitialSum}} сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Остаток кредитов: <strong>{{(int)$loansLeftSum}} сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Сумма погащений основных кредитов: <strong>{{(int)$principalPaymentsTotalSum}} сомонӣ 00 дирам</strong></p>
        <p class="p-0 m-1">Сумма погащений процентов: <strong>{{(int)$interestPaymentsTotalSum}} сомонӣ 00 дирам</strong></p>
        <p><a href="/admin/excel/cashbox?from={{request()->get('from')}}&to={{request()->get('to')}}&cashbox={{request()->get('cashbox')}}&audit={{request()->get('audit')}}">Загузить в Excel</a></p>
    @endif
@endsection
