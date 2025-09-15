@extends('layout')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Сотрудники</h4>
            <a class="btn btn-primary" href="/admin/hr/create">Добавить</a>
        </div>

        <table class="table table-sm table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>ФИО</th>
                <th>Должность</th>
                <th>Телефон</th>
                <th>Текущий контракт</th>
                <th>Оклад</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $i)
                @php($last = $i->contracts->first())
                <tr>
                    <td>{{ $i->id }}</td>
                    <td>{{ $i->last_name }} {{ $i->first_name }}</td>
                    <td>{{ $i->position }}</td>
                    <td>{{ $i->phone }}</td>
                    <td>{{ optional($last)->contract_no }}</td>
                    <td>{{ $last ? number_format($last->salary,2,'.',' ') . ' ' . $last->currency : '' }}</td>
                    <td>
                        <a class="btn btn-sm btn-outline-secondary" href="/admin/hr/{{ $i->id }}/edit">Редактировать</a>
                        <a class="btn btn-sm btn-outline-danger" href="/admin/hr/{{ $i->id }}/delete" onclick="return confirm('Удалить сотрудника {{ $i->last_name }} {{ $i->first_name }}?');">Удалить</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $items->links() }}
    </div>
@endsection


