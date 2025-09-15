@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/admin/cashboxes/create" class="btn btn-primary text-uppercase">
            Добавить кассу
        </a>
    </p>
    @widget('Error')
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Название
        </th>
        <th>
            Внутреннее название
        </th>
        <th>
            Лицензия кассы
        </th>
        <th>
            Адрес
        </th>
        <th>
            Телефон
        </th>
        <th>
            Дата
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        @foreach($cashboxes as $key => $cashbox)
            <tr>
                <td>
                    {{$key+1}}.
                </td>
                <td>
                    {{$cashbox->name}}

                </td>
                <td>
                    <p class="badge badge-info">{{$cashbox->nickname}}</p>
                </td>
                <td>
                    {{$cashbox->license}}
                </td>
                <td>
                    {{$cashbox->address}}
                </td>

                <td>
                    {{$cashbox->phone}}
                </td>
                <td>
                    @date($cashbox->created_at->timestamp)
                </td>
                <td style="width:90px;">
                    <a href="/admin/cashboxes/{{$cashbox->id}}/update">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/admin/cashboxes/{{$cashbox->id}}/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
