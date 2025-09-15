@extends('incassator.layout')

@section('inc-content')
    <form method="get" action="/inc/safe" class="mb-2">
        <div class="form-group">
            <select name="cashbox" class="form-control" onchange="this.form.submit()">
                <option value="0">Все кассы</option>
                @foreach(($cashboxes ?? []) as $c)
                    <option value="{{ $c->id }}" {{ (int)($selectedCashbox ?? 0) === (int)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
    </form>
    
    <div class="table-responsive">
    <table class="table table-striped table-sm mb-2">
        <thead><tr><th>#</th><th>Касса</th><th>Договор</th><th>Клиент</th><th>Инфо</th></tr></thead>
        <tbody>
        @foreach($items as $i => $row)
            <tr>
                <td>{{ $items->firstItem() + $i }}</td>
                <td>{{ optional($row->cashbox)->name }}</td>
                <td>{{ $row->contract_no }}</td>
                <td>{{ $row->client_name }}</td>
                <td>{{ $row->loan_info }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div style="margin-bottom: 120px;">{{ $items->links() }}</div>

    <div class="inc-actions bg-white border-top" style="position:fixed;left:0;right:0;bottom:0;z-index:1030;">
        <div class="p-2">
            <a href="/inc/safe/create" class="btn btn-primary btn-block d-flex align-items-center justify-content-center" style="min-height:48px; font-size:16px;">Добавить</a>
        </div>
    </div>

    @push('styles')
    <style>
        body{padding-bottom:80px;}
    </style>
    @endpush
@endsection


