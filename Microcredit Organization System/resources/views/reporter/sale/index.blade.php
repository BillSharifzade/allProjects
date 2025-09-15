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
                <td>
                    @if($sale->canceled_at > 0)
                        <span class="badge badge-secondary">Отменено админом</span>
                    @else
                        <span class="badge badge-success">Завершено</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $items->appends($_GET)->links() }}
@endsection


