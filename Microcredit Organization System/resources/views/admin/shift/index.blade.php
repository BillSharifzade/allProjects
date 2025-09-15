@extends('layout')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Смены кассиров</h5>
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Кассир</th>
                    <th>Касса</th>
                    <th>Открыта</th>
                    <th>Закрыта</th>
                    <th>Открытие</th>
                    <th>Дельта</th>
                    <th>Ожидаемо</th>
                    <th>Закрытие</th>
                    <th>Расхождение</th>
                </tr>
                </thead>
                <tbody>
                @foreach($shifts as $shift)
                    @php
                        $delta = isset($deltas[$shift->id]) ? (float)$deltas[$shift->id]->delta : 0;
                        $expected = (float)$shift->opening_balance + $delta;
                    @endphp
                    <tr>
                        <td>{{ $shift->id }}</td>
                        <td>{{ $shift->user_id }}</td>
                        <td>{{ $shift->cashbox_id }}</td>
                        <td>@date($shift->opened_at)</td>
                        <td>{{ $shift->closed_at ? date('Y-m-d H:i',$shift->closed_at) : '-' }}</td>
                        <td>{{ number_format($shift->opening_balance,2,'.',' ') }}</td>
                        <td>{{ number_format($delta,2,'.',' ') }}</td>
                        <td>{{ number_format($expected,2,'.',' ') }}</td>
                        <td>{{ number_format($shift->closing_balance,2,'.',' ') }}</td>
                        <td>{{ number_format($shift->discrepancy,2,'.',' ') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $shifts->links() }}
        </div>
    </div>
@endsection


