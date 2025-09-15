@extends('layout')

@section('content')
    <p class="flex justify-content-end">
        <a href="/admin/interest-rates/create" class="btn btn-primary text-uppercase">
            Добавить процентовку
        </a>
    </p>
    @widget('Error')
    <table class="m-2 table table-light">
        <thead class=" table-sm">
        <th>
            #
        </th>
        <th>
            Сумма от
        </th>
        <th>
            Сумма до
        </th>
        <th>
            Процент (месяц)
        </th>
        <th>
            Действие
        </th>
        </thead>
        <tbody>
        @foreach($interestRates as $key => $interestRate)
            <tr>
                <td>
                    {{$key+1}}.
                </td>
                <td>
                    {{$interestRate->sum_from}}
                </td>
                <td>
                    {{$interestRate->sum_to}}
                </td>
                <td>
                    {{$interestRate->rate}}
                </td>
                <td style="width:90px;">
                    <a href="/admin/interest-rates/{{$interestRate->id}}/update">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/admin/interest-rates/{{$interestRate->id}}/delete" style="font-size: 16px" class="px-1">
                        <i class="fas fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
