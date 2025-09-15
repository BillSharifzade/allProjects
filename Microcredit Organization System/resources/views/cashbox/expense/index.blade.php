@extends('layout')

@section('content')
    <div class="m-3 card p-3">
        <h4>Расходы за сегодня</h4>
        <a href="/expenses/create" class="btn btn-sm btn-success locked-hide">Добавить расход</a>

        <table class="table table-light table-hover zebra mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Категория</th>
                <th>Описание</th>
                <th>Сумма</th>
                <th>Время</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $i)
                <tr>
                    <td>{{$i->id}}</td>
                    <td>{{$i->category}}</td>
                    <td>{{$i->description}}</td>
                    <td>{{ number_format($i->amount, 2, '.', ' ') }}</td>
                    <td>@date($i->occurred_at)</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $items->withQueryString()->links() }}
    </div>
@endsection


