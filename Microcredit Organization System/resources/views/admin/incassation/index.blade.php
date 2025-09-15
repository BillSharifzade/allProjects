@extends('layout')

@section('content')
    <form method="get" class="m-2 p-2 bg-light">
        <div class="form-row">
            <div class="col"><input type="date" name="from" value="{{ request('from') }}" class="form-control" /></div>
            <div class="col"><input type="date" name="to" value="{{ request('to') }}" class="form-control" /></div>
            <div class="col"><button class="btn btn-primary">Показать</button></div>
        </div>
    </form>
    @php($groups = $items->getCollection()->groupBy('cashbox_id'))
    @php($n = $items->firstItem())
    <table class="table table-sm table-striped m-2">
        <thead><tr><th>#</th><th>Касса</th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Инкассатор</th><th>Кассир</th><th>Забрал</th><th>Доставил</th><th>Статус</th></tr></thead>
        <tbody>
        @foreach($groups as $cashboxId => $rows)
            <tr class="table-secondary">
                <td colspan="10"><strong>{{ optional($cashboxes[$cashboxId] ?? null)->name ?: 'Без кассы' }}</strong></td>
            </tr>
            @foreach($rows as $row)
                <tr>
                    <td>{{ $n }}</td>
                    <td>{{ optional($cashboxes[$row->cashbox_id] ?? null)->name }}</td>
                    <td>{{ $row->contract_no }}</td>
                    <td>{{ $row->client_name }}</td>
                    <td>{{ $row->loan_info }}</td>
                    <td>{{ $row->incassator_id ? (($users[$row->incassator_id]->last_name ?? '') . ' ' . ($users[$row->incassator_id]->first_name ?? '')) : '-' }}</td>
                    <td>{{ $row->cashier_id ? (($users[$row->cashier_id]->last_name ?? '') . ' ' . ($users[$row->cashier_id]->first_name ?? '')) : '-' }}</td>
                    <td>{{ $row->picked_by_incassator ? date('Y-m-d H:i', $row->picked_at) : '-' }}</td>
                    <td>{{ $row->delivered_by_incassator ? date('Y-m-d H:i', $row->delivered_at) : '-' }}</td>
                    <td>
                        @php($log = ($latestLogs[$row->id] ?? null))
                        @if($row->accepted_by_cashier)
                            <span class="badge badge-success">Принято {{ date('Y-m-d H:i', $row->accepted_at) }}</span>
                        @elseif($log && $log->action === 'reset')
                            <span class="badge badge-danger">Отклонено кассиром {{ date('Y-m-d H:i', $log->created_at) }}</span>
                        @elseif($row->delivered_by_incassator)
                            <span class="badge badge-warning">Ожидает приема</span>
                        @elseif($row->picked_by_incassator)
                            <span class="badge badge-info">В пути</span>
                        @else
                            <span class="badge badge-secondary">Ожидает инкассатора</span>
                        @endif
                    </td>
                </tr>
                @php($n++)
            @endforeach
        @endforeach
        </tbody>
    </table>
    {{ $items->links() }}
@endsection


