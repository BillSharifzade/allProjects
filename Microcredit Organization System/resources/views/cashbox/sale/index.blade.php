@extends('layout')

@section('content')
    <h5 class="m-2">Сегодняшние продажи</h5>
    <table class="table table-sm table-striped m-2">
        <thead>
        <tr>
            <th>#</th>
            <th>Договор</th>
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


