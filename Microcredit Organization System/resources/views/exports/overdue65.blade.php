@foreach($rows as $cashbox => $items)
    <table>
        <thead>
        <tr>
            <th colspan="9">Касса: {{ $cashbox ?: 'Без кассы' }}</th>
        </tr>
        <tr>
            <th>Договор</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Кассир</th>
            <th>Залог</th>
            <th>Сумма</th>
            <th>Неоп. дни</th>
            <th>Неопл. проценты</th>
            <th>Дата выдачи</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $r)
            <tr>
                <td>{{ $r['document_full'] }}</td>
                <td>{{ $r['full_name'] }}</td>
                <td>{{ $r['phone'] }}</td>
                <td>{{ $r['cashier'] }}</td>
                <td>{{ $r['collateral'] }}</td>
                <td>{{ $r['initial_sum'] }}</td>
                <td>{{ $r['unpaid_days'] }}</td>
                <td>{{ $r['unpaid_interest'] }}</td>
                <td>{{ $r['lend_date'] }}</td>
            </tr>
        @endforeach
        </tbody>
        @if(!empty($groupTotals) && isset($groupTotals[$cashbox ?: 'Без кассы']))
            <tfoot>
            <tr>
                <td colspan="8"><strong>Итого по кассе — Сумма кредитов</strong></td>
                <td><strong>{{ $groupTotals[$cashbox ?: 'Без кассы']['loan'] }}</strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>Итого по кассе — Золото</strong></td>
                <td><strong>{{ $groupTotals[$cashbox ?: 'Без кассы']['gold'] }}</strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>Итого по кассе — Неопл. проценты</strong></td>
                <td><strong>{{ $groupTotals[$cashbox ?: 'Без кассы']['interest'] }}</strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>Итого (кредиты + проценты)</strong></td>
                <td><strong>{{ $groupTotals[$cashbox ?: 'Без кассы']['grand'] }}</strong></td>
            </tr>
            <tr>
                <td colspan="8"><strong>После вычета стоимости золота</strong></td>
                <td><strong>{{ $groupTotals[$cashbox ?: 'Без кассы']['after'] }}</strong></td>
            </tr>
            </tfoot>
        @endif
    </table>
    <br/>
@endforeach

@if(!empty($totals))
    <table>
        <thead>
        <tr><th colspan="2">Итоги</th></tr>
        </thead>
        <tbody>
        <tr>
            <td>Сумма кредитов</td><td>{{ $totals['total_loan'] }}</td>
        </tr>
        <tr>
            <td>Сумма неоплаченных процентов</td><td>{{ $totals['total_unpaid_interest'] }}</td>
        </tr>
        <tr>
            <td>Стоимость золота (375={{ $totals['p375'] }}, 585={{ $totals['p585'] }}, 750={{ $totals['p750'] }}, 875={{ $totals['p875'] }})</td>
            <td>{{ $totals['gold_worth'] }}</td>
        </tr>
        <tr>
            <td>Итого (кредиты + проценты)</td><td>{{ $totals['grand_total'] }}</td>
        </tr>
        <tr>
            <td>После вычета стоимости золота</td><td>{{ $totals['after_gold_offset'] }}</td>
        </tr>
        </tbody>
    </table>
@endif


