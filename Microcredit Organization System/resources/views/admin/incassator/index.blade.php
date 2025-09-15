@extends('layout')

@section('content')
    <a href="/admin/incassators/create" class="btn btn-primary m-2">Добавить инкассатора</a>
    <table class="table table-sm table-striped m-2">
        <thead><tr><th>#</th><th>Имя</th><th>Логин</th><th>Телефон</th><th>Сейф</th><th></th></tr></thead>
        <tbody>
        @foreach($items as $i => $u)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>{{ $u->last_name }} {{ $u->first_name }}</td>
                <td>{{ $u->login }}</td>
                <td>{{ $u->phone }}</td>
                <td><a href="/admin/incassators/{{ $u->id }}/safe">Открыть</a></td>
                <td><a href="/admin/incassators/{{ $u->id }}/delete" onclick="return confirm('Удалить?');">Удалить</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
@endsection


