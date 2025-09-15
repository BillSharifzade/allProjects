@extends('layout')

@section('content')
    <h5 class="m-2">Доставленные инкассатором (ожидают приема)</h5>
    <form method="post" action="/incassation/accept" class="m-2">
        {{ csrf_field() }}
        <table class="table table-sm table-striped">
            <thead>
            <tr>
                <th></th><th>Договор</th><th>Клиент</th><th>Инфо</th><th>Доставлено</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $i => $row)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $row->id }}" /></td>
                    <td>{{ $row->contract_no }}</td>
                    <td>{{ $row->client_name }}</td>
                    <td>{{ $row->loan_info }}</td>
                    <td>@date($row->delivered_at)</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="d-flex mb-3">
            <button class="btn btn-success mr-2">Принять выбранные</button>
            <button class="btn btn-outline-danger" formaction="/incassation/not-delivered" formmethod="post">Не доставлено</button>
        </div>
    </form>
    {{ $items->links() }}

    <hr />
    <h5 class="m-2">Ожидают доставки инкассатором</h5>
    <table class="table table-sm table-striped m-2">
        <thead>
        <tr>
            <th>Договор</th><th>Клиент</th><th>Инфо</th><th>Статус</th>
        </tr>
        </thead>
        <tbody>
        @foreach(($toDeliver ?? []) as $row)
            <tr>
                <td>{{ $row->contract_no }}</td>
                <td>{{ $row->client_name }}</td>
                <td>{{ $row->loan_info }}</td>
                <td>
                    @if($row->picked_by_incassator)
                        <span class="badge badge-info">Забрано инкассатором</span>
                    @else
                        <span class="badge badge-secondary">Ожидает инкассатора</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection


