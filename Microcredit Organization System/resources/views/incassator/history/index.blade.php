@extends('incassator.layout')

@section('inc-content')
    <div class="table-responsive">
    <table class="table table-striped table-sm mb-2">
        <thead>
        <tr><th>#</th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Доставлено</th><th>Принято кассиром</th></tr>
        </thead>
        <tbody>
        @foreach($items as $i => $row)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>{{ $row->contract_no }}</td>
                <td>{{ $row->client_name }}</td>
                <td>{{ $row->loan_info }}</td>
                <td>@date($row->delivered_at)</td>
                <td>
                    @if($row->accepted_by_cashier)
                        <span class="badge badge-success">Принято кассиром</span>
                    @else
                        <span class="badge badge-warning">Ожидает приема</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div>{{ $items->links() }}</div>
@endsection


