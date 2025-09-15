@extends('layout')

@section('content')
<div class="container-fluid">
    <h5 class="mb-3">Ежедневные отчёты по кассирам</h5>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
            <tr>
                <th>Касса</th>
                <th>Кассир</th>
                <th>Статус смены</th>
                <th>Последняя смена</th>
                <th class="text-right">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $link)
                @php
                    $key = $link->user_id . ':' . $link->cashbox_id;
                    $ls = $latestShifts[$key][0] ?? null;
                    $isOpen = $ls && (int)$ls->closed_at === 0;
                @endphp
                <tr>
                    <td>{{ optional($link->cashbox)->name }}</td>
                    <td>{{ optional($link->user)->last_name }} {{ optional($link->user)->first_name }}</td>
                    <td>
                        @if($ls)
                            @if($isOpen)
                                <span class="badge badge-success">Открыта</span>
                            @else
                                <span class="badge badge-secondary">Закрыта</span>
                            @endif
                        @else
                            <span class="badge badge-light">Нет смен</span>
                        @endif
                    </td>
                    <td>
                        @if($ls)
                            #{{ $ls->id }}: {{ date('Y-m-d H:i', $ls->opened_at) }} — {{ (int)$ls->closed_at>0 ? date('Y-m-d H:i', $ls->closed_at) : '...' }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($ls && (int)$ls->closed_at > 0)
                            <a class="btn btn-sm btn-primary" href="/reporter/daily-report/{{ $ls->id }}/download">Скачать отчет</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $items->links() }}
</div>
@endsection


