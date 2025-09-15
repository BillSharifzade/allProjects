@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/admin/gold-prices/create" class="btn btn-primary text-uppercase">
            Добавить цену
        </a>
    </p>
    @widget('Error')
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th style="width: 10px">
            #
        </th>
        <th>
            Проба
        </th>
        <th>
            Сумма
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        @foreach($goldPrices as $key => $goldPrice)
            <tr>
                <td>
                    {{$key+1}}.
                </td>
                <td>
                    {{$goldPrice->purity}}
                </td>
                <td>
                    {{$goldPrice->price}}
                </td>
                <td style="width:90px;">
                    <a href="/admin/gold-prices/{{$goldPrice->id}}/update">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/admin/gold-prices/{{$goldPrice->id}}/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
