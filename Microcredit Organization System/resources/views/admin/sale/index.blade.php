@extends('layout')

@section('content')
    <form method="get" class="m-2 p-2 bg-light">
        <div class="form-row">
            <div class="col">
                <input type="date" name="from" value="{{ request('from') }}" class="form-control" />
            </div>
            <div class="col">
                <input type="date" name="to" value="{{ request('to') }}" class="form-control" />
            </div>
            <div class="col">
                <button class="btn btn-primary">Показать</button>
            </div>
        </div>
    </form>
    <table class="table table-sm table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Договор</th>
            <th>Касса</th>
            <th>Клиент</th>
            <th>Сумма</th>
            <th>Дата</th>
            <th>Статус</th>
            <th>Действие</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $i => $sale)
            @php($loan = $loans[$sale->loan_id] ?? null)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>
                    @if($loan)
                        №{{ $loan->document_no }}@if($loan->audit_document_no>0)-{{ $loan->audit_document_no }}@endif
                    @endif
                </td>
                <td>{{ optional($sale->cashbox)->name }}</td>
                <td>{{ optional(optional($loan)->loaner)->full_name }}</td>
                <td>{{ number_format($sale->total_amount, 2, '.', ' ') }}</td>
                <td>@date($sale->sold_at)</td>
                @if($sale->canceled_at == 0)
                    @php($p = (float)$sale->profit_amount)
                    <td class="text-nowrap">
                        @if($p > 0)
                            <span class="badge badge-success">+{{ number_format($p, 2, '.', ' ') }}</span>
                        @elseif($p < 0)
                            <span class="badge badge-danger">-{{ number_format(abs($p), 2, '.', ' ') }}</span>
                        @else
                            <span class="badge badge-secondary">0</span>
                        @endif
                    </td>
                    <td class="text-nowrap">
                        <a href="/admin/sales/{{ $sale->id }}/cancel" class="btn btn-sm btn-outline-danger" onclick="return confirm('Отменить продажу и вернуть баланс?');">Отменить</a>
                    </td>
                @else
                    <td><span class="badge badge-secondary">Отменено</span></td>
                    <td></td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->appends($_GET)->links() }}
@endsection


