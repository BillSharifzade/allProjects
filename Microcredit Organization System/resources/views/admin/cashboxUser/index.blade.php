@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/admin/cashbox-users/create" class="btn btn-primary text-uppercase">
            Добавить кассира
        </a>
    </p>
    @widget('Error')
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Касса
        </th>
        <th>
            Имя
        </th>
        <th>
            Фамилия
        </th>
        <th>
            Лицензия кассира
        </th>
        <th>
            Логин
        </th>
        <th>
            Роль
        </th>
        <th>
            Телефон
        </th>
        <th>
            Смена
        </th>
        <th>
            Дата
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        @php($counter = 0)
        @foreach($cashboxUsers as $cashboxUser)
            @if($cashboxUser->user->role == 'cashier' || $cashboxUser->user->role == 'cashier-audit')
                @php($counter++)
                <tr>
                    <td>
                        {{$counter}}.
                    </td>
                    <td>
                        {{$cashboxUser->cashbox->name}}<br/>
                        <p class="badge badge-info">{{$cashboxUser->cashbox->nickname}}</p>
                    </td>
                    <td>
                        {{$cashboxUser->user->first_name}}
                    </td>
                    <td>
                        {{$cashboxUser->user->last_name}}
                    </td>
                    <td>
                        {{$cashboxUser->user_license}}
                    </td>
                    <td>
                        {{$cashboxUser->user->login}}
                    </td>
                    <td>
                        @if($cashboxUser->user->role == 'cashier')
                            Кассир
                        @else
                            Кассир (аудит)
                        @endif
                    </td>
                    <td>
                        {{$cashboxUser->user->phone}}
                    </td>
                    <td>
                        @php($isOpen = isset($shiftOpenMap[$cashboxUser->user_id . ':' . $cashboxUser->cashbox_id]))
                        <span class="badge {{ $isOpen ? 'badge-success' : 'badge-secondary' }}" style="font-size: 12px;">
                            {{ $isOpen ? 'Открыта' : 'Закрыта' }}
                        </span>
                    </td>
                    <td>
                        @date($cashboxUser->user->created_at->timestamp)
                    </td>
                    <td style="width:90px;">
                        <a href="/admin/cashbox-users/{{$cashboxUser->id}}/update">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/admin/cashbox-users/{{$cashboxUser->id}}/delete" style="font-size: 16px" class="px-1">
                            <i class="fas fa-trash text-danger"></i>
                        </a>
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endsection
