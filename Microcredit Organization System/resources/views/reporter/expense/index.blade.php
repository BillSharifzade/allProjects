@extends('layout')

@section('content')
    <div class="m-3">
        <h4>История расходов</h4>
        <form method="get" class="form-inline my-2">
            <div class="form-group mr-2">
                <label class="mr-2">С</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="form-group mr-2">
                <label class="mr-2">По</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <button class="btn btn-primary">Показать</button>
        </form>

        @if(isset($byCategory) && count($byCategory) > 0)
            <h5 class="mt-3">Итого по категориям</h5>
            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th>Категория</th>
                    <th>Сумма</th>
                </tr>
                </thead>
                <tbody>
                @foreach($byCategory as $row)
                    <tr>
                        <td>{{ $row->category }}</td>
                        <td>{{ number_format($row->sum, 2, '.', ' ') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        <table class="table table-light mt-3">
            <thead>
            <tr>
                <th>ID</th>
                <th>Категория</th>
                <th>Описание</th>
                <th>Сумма</th>
                <th>Касса</th>
                <th>Кассир</th>
                <th>Дата</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $i)
                @php($cu = $cashboxUsers[$i->user_id] ?? null)
                <tr>
                    <td>{{$i->id}}</td>
                    <td>{{$i->category}}</td>
                    <td>{{$i->description}}</td>
                    <td>{{ number_format($i->amount, 2, '.', ' ') }}</td>
                    <td>{{ $cu ? $cu->cashbox->name : '-' }}</td>
                    <td>{{ $cu ? ($cu->user->last_name . ' ' . $cu->user->first_name) : '-' }}</td>
                    <td>@date($i->occurred_at)</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $items->withQueryString()->links() }}
    </div>
@endsection


