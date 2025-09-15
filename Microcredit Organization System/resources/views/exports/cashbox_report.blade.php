<table >
    <tbody>
    <tr>
        <td colspan="9">Операции по ЗАЛОГОВЫМ БИЛЕТАМ с {{request()->get('from')}} по {{request()->get('to')}}</td>
    </tr>
    <tr>
        <td colspan="9">Предприятие: ЧДММ Гаравхонаи Нигин</td>
    </tr>
    <tr>
        <td colspan="9">Ломбард: {{$data['cashbox']}}</td>
    </tr>
    <tr>
        <td rowspan="2">Дата</td>
        <td rowspan="2">Код</td>
        <td rowspan="2" width="30">Номер договора</td>
        <td rowspan="2" width="30">Заемщик (ФИО)</td>
        <td>Расход</td>
        <td colspan="4">Приход</td>
    </tr>
    <tr>
        <td width="30">Займ</td>
        <td width="30">Закрытие</td>
        <td width="30">Проценты за пользование займом</td>
        <td width="30">Частичное погашение</td>
        <td width="30">Итого Приход</td>
    </tr>
        @php
            $sum = 0;
            $closeSum = 0;
            $interestSum = 0;
            $principalSum = 0;
            $totalSum = 0;
        @endphp

        @foreach($data['transactions'] as $date=>$transactions)
            @foreach($transactions as $transaction)
            <tr>
            <td>
                {{$date}}
            </td>
            <td>

            </td>
            <td>
                {{ is_array($transaction) ? ($transaction['document_no'] ?? '') : (is_object($transaction) ? ($transaction->document_no ?? '') : '') }}
            </td>
            <td>
                {{ is_array($transaction) ? ($transaction['loaner'] ?? '') : (is_object($transaction) ? ($transaction->loaner ?? '') : '') }}
            </td>
            <td>
                {{ $s = (float)(is_array($transaction) ? ($transaction['sum'] ?? 0) : (is_object($transaction) ? ($transaction->sum ?? 0) : 0)) }}
                @php($sum += $s)
            </td>
            <td>
                {{ $cs = (float)(is_array($transaction) ? ($transaction['close_sum'] ?? 0) : (is_object($transaction) ? ($transaction->close_sum ?? 0) : 0)) }}
                @php($closeSum += $cs)
            </td>
            <td>
                {{ $is = (float)(is_array($transaction) ? ($transaction['interest_sum'] ?? 0) : (is_object($transaction) ? ($transaction->interest_sum ?? 0) : 0)) }}
                @php($interestSum += $is)
            </td>
            <td>
                {{ $ps = (float)(is_array($transaction) ? ($transaction['principal_sum'] ?? 0) : (is_object($transaction) ? ($transaction->principal_sum ?? 0) : 0)) }}
                @php($principalSum += $ps)
            </td>
            <td>
                {{ $tot = $is + $ps + $cs }}
                @php($totalSum += $tot)
            </td>
            </tr>
            @endforeach
        @endforeach
        <tr>
            <td colspan="9"></td>
        </tr>
        <tr>
            <td colspan="4"><strong>ИТОГ</strong></td>
            <td><strong>{{$sum}}</strong></td>
            <td><strong>{{$closeSum}}</strong></td>
            <td><strong>{{$interestSum}}</strong></td>
            <td><strong>{{$principalSum}}</strong></td>
            <td><strong>{{$totalSum}}</strong></td>
        </tr>

    </tbody>
</table>
