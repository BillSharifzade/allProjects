@extends('layout')

@section('content')
    <div class="m-3 card p-3">
        <h4>Сегодняшние переводы</h4>
        <table class="table table-light table-hover zebra mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Тип</th>
                <th>Касса</th>
                <th>Кассир</th>
                <th>Сумма</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $i)
                @php($cu = $cashboxUsers[$i->user_id] ?? null)
                <tr>
                    <td>{{$i->id}}</td>
                    <td>
                        @php($typeLabel = $i->event_type == 'admin_fund' ? 'Инвестиция (админ)' : ($i->event_type == 'transfer_in' ? 'Подкрепление' : 'Передача'))
                        {{$typeLabel}}
                    </td>
                    <td>{{$cu ? $cu->cashbox->name : '-'}}</td>
                    <td>{{$cu ? ($cu->user->last_name . ' ' . $cu->user->first_name) : '-'}}</td>
                    <td>{{ number_format($i->amount, 2, '.', ' ') }}</td>
                    <td>@date($i->occurred_at)</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
@endsection


